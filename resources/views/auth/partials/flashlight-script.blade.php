{{-- Shared flashlight behaviour. Needs #lfRoot containing .lf-scene, .lf-card and #lfTorch. --}}
<script>
(() => {
    /* ------------------------------------------------------------------
       Flashlight
       - The beam is the CSS mask on .lf-scene; JS only feeds it coordinates.
       - rAF loop runs only while the beam is still moving/resizing.
       ------------------------------------------------------------------ */
    const root  = document.getElementById('lfRoot');
    if (!root) return;
    const scene = root.querySelector('.lf-scene');
    const card  = root.querySelector('.lf-card');
    const torch = document.getElementById('lfTorch');
    const html  = document.documentElement;
    const objs  = Array.from(scene.querySelectorAll('.lf-obj'));

    const ac = new AbortController();
    const opt = { signal: ac.signal };
    const optPassive = { signal: ac.signal, passive: true };

    const mqReduce = matchMedia('(prefers-reduced-motion: reduce)');
    const mqFine   = matchMedia('(hover: hover) and (pointer: fine)');
    let reduce = mqReduce.matches;

    let R = 190;                 // beam radius target (150–220px)
    let x = innerWidth / 2, y = innerHeight / 2;   // smoothed beam centre
    let tx = x, ty = y;          // where the pointer actually is
    let r = 0, tr = 0;           // current / target radius
    let raf = 0, last = 0;
    let cardRect = card.getBoundingClientRect();
    let centres = [];
    let lastInput = 'none';
    const lit = new WeakMap();

    // dust motes (only visible inside the beam)
    const frag = document.createDocumentFragment();
    for (let i = 0; i < 28; i++) {
        const d = document.createElement('i');
        d.className = 'lf-dust';
        const s = 1 + Math.random() * 2;
        d.style.cssText =
            `left:${(Math.random() * 100).toFixed(1)}%;top:${(Math.random() * 100).toFixed(1)}%;` +
            `width:${s.toFixed(1)}px;height:${s.toFixed(1)}px;` +
            `--d:${(14 + Math.random() * 14).toFixed(1)}s;--delay:${(-Math.random() * 14).toFixed(1)}s;` +
            `--dx:${(Math.random() * 40 - 20).toFixed(0)}px;--dy:${(-10 - Math.random() * 30).toFixed(0)}px`;
        frag.appendChild(d);
    }
    scene.appendChild(frag);

    function measure() {
        R = Math.round(Math.min(220, Math.max(150, Math.min(innerWidth, innerHeight) * 0.24)));
        cardRect = card.getBoundingClientRect();
        centres = objs.map(el => {
            const b = el.getBoundingClientRect();
            return b.width ? { el, cx: b.left + b.width / 2, cy: b.top + b.height / 2 } : null;
        }).filter(Boolean);
        if (tr > 0) tr = R;
    }

    function paint() {
        const rr = Math.max(r, 1).toFixed(1) + 'px';
        scene.style.setProperty('--x', x.toFixed(1) + 'px');
        scene.style.setProperty('--y', y.toFixed(1) + 'px');
        scene.style.setProperty('--r', rr);
        card.style.setProperty('--cx', (x - cardRect.left).toFixed(1) + 'px');
        card.style.setProperty('--cy', (y - cardRect.top).toFixed(1) + 'px');
        card.style.setProperty('--r', rr);

        // contextual labels: light up objects near the beam core
        const core = r * 0.62;
        for (const c of centres) {
            const on = Math.hypot(c.cx - x, c.cy - y) < core;
            if (lit.get(c.el) !== on) { lit.set(c.el, on); c.el.classList.toggle('is-lit', on); }
        }
    }

    function frame(t) {
        const dt = Math.min(64, t - (last || t));
        last = t;
        const a  = reduce ? 1 : 1 - Math.exp(-dt / 70);   // position easing
        const ar = reduce ? 1 : 1 - Math.exp(-dt / 140);  // radius easing
        x += (tx - x) * a; y += (ty - y) * a; r += (tr - r) * ar;
        const settled = Math.abs(tx - x) < 0.3 && Math.abs(ty - y) < 0.3 && Math.abs(tr - r) < 0.3;
        if (settled) { x = tx; y = ty; r = tr; }
        paint();
        raf = settled ? 0 : requestAnimationFrame(frame);
        if (settled) last = 0;
    }
    const kick = () => { if (!raf) raf = requestAnimationFrame(frame); };

    function aim(px, py, snap) {
        tx = px; ty = py; tr = R;
        if (snap) { x = px; y = py; r = 0; }
        kick();
    }

    /* pointer: mouse/pen follow, touch taps reveal */
    addEventListener('pointermove', e => {
        if (e.pointerType === 'touch') { aim(e.clientX, e.clientY, false); return; }
        lastInput = 'pointer';
        torch.style.transform = `translate3d(${e.clientX}px,${e.clientY}px,0)`;
        torch.classList.add('is-on');
        aim(e.clientX, e.clientY, false);
    }, optPassive);

    addEventListener('pointerdown', e => {
        lastInput = 'pointer';
        if (e.pointerType === 'touch' || e.pointerType === 'pen' && !mqFine.matches) aim(e.clientX, e.clientY, true);
    }, optPassive);

    html.addEventListener('pointerleave', () => {
        if (!mqFine.matches) return;
        torch.classList.remove('is-on');
        tr = 0; kick();
    }, optPassive);

    /* keyboard: when the user tabs, the light moves to the focused control */
    addEventListener('keydown', e => { if (e.key === 'Tab') lastInput = 'key'; }, optPassive);
    addEventListener('focusin', e => {
        if (lastInput !== 'key' || !root.contains(e.target)) return;
        const b = e.target.getBoundingClientRect();
        aim(b.left + b.width / 2, b.top + b.height / 2, false);
    }, optPassive);

    /* layout changes */
    addEventListener('resize', () => { measure(); kick(); }, optPassive);
    addEventListener('scroll', () => { cardRect = card.getBoundingClientRect(); kick(); }, optPassive);
    mqReduce.addEventListener('change', e => { reduce = e.matches; }, opt);

    function syncCursor() { html.classList.toggle('lf-hide-cursor', mqFine.matches); }
    mqFine.addEventListener('change', syncCursor, opt);

    /* initial state */
    measure();
    syncCursor();
    paint();
    if (!mqFine.matches) {
        // touch devices: pulse a beam in over the right-hand side so the page reads as "lit by something"
        aim(innerWidth * 0.72, innerHeight * 0.38, true);
    }

    /* cleanup (navigation, bfcache, Turbo/PJAX-style swaps) */
    addEventListener('pagehide', () => {
        cancelAnimationFrame(raf);
        html.classList.remove('lf-hide-cursor');
        ac.abort();
    }, { once: true });
})();
</script>
