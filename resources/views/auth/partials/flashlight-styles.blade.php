{{-- Shared dark flashlight styles for the auth pages (login, register). --}}
<style>
<style>
/* ==========================================================================
   Lost & Found — flashlight login
   Everything is scoped to .lf so it cannot leak into other pages.
   The beam is a real radial-gradient MASK on the scene layer: the objects
   underneath are only rendered where the mask is opaque.
   ========================================================================== */
html, body { background: #000 !important; background-image: none !important; }
body { overflow-x: hidden; }

.lf {
    --lf-black: #000;
    --lf-card: #07080b;
    --lf-field: #0d0e12;
    --lf-line: #26272d;
    --lf-text: #f3f5f9;
    --lf-muted: #7c8494;
    --lf-blue: #2563eb;
    --lf-blue-hi: #3b82f6;
    --lf-beam: 158, 196, 255;
    --lf-hand: "Segoe Print", "Bradley Hand", "Marker Felt", "Comic Sans MS", cursive;

    position: relative;
    min-height: 100vh;
    min-height: 100dvh;
    display: grid;
    place-items: center;
    padding: 5.5rem 1rem 4rem;
    background: var(--lf-black);
    color: var(--lf-text);
    isolation: isolate;
}

/* ---------- Scene: the hidden objects, revealed through the mask ---------- */
.lf-scene {
    --x: 50vw; --y: 50vh; --r: 1px;
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    overflow: hidden;
    -webkit-mask-image: radial-gradient(circle var(--r) at var(--x) var(--y),
        #000 0%, rgba(0,0,0,.92) 38%, rgba(0,0,0,.45) 72%, rgba(0,0,0,.035) 100%);
            mask-image: radial-gradient(circle var(--r) at var(--x) var(--y),
        #000 0%, rgba(0,0,0,.92) 38%, rgba(0,0,0,.45) 72%, rgba(0,0,0,.035) 100%);
}
/* the lit "floor" — only visible where the mask lets it through */
.lf-wash {
    position: absolute; inset: 0;
    background: radial-gradient(circle var(--r) at var(--x) var(--y),
        rgba(var(--lf-beam), .20) 0%, rgba(var(--lf-beam), .08) 55%, rgba(var(--lf-beam), 0) 100%);
}

.lf-obj {
    --rot: 0deg; --s: 1;
    position: absolute;
    left: var(--l); top: var(--t);
    width: var(--w, 120px);
    transform: translate(-50%, -50%) rotate(var(--rot)) scale(var(--s));
    transition: transform .6s cubic-bezier(.2,.7,.2,1);
}
.lf-obj.is-lit { --s: 1.04; }
.lf-obj svg { display: block; width: 100%; height: auto; overflow: visible; }

.lf-lbl {
    position: absolute; left: 50%; top: 100%;
    margin-top: 8px;
    transform: translateX(-50%) rotate(calc(var(--rot) * -1));
    padding: 2px 9px;
    border: 1px solid #2b3552;
    border-radius: 999px;
    background: rgba(6, 9, 18, .88);
    color: #cfe0ff;
    font: 500 11px/1.5 system-ui, sans-serif;
    white-space: nowrap;
    opacity: 0;
    transition: opacity .35s ease;
}
.lf-obj.is-lit .lf-lbl { opacity: 1; }

.lf-note {
    padding: 14px 16px 12px;
    background: #cdbb8c;
    color: #3b2f1a;
    font: 400 20px/1.15 var(--lf-hand);
    box-shadow: 0 6px 18px rgba(0,0,0,.5);
    clip-path: polygon(0 0, 96% 0, 100% 8%, 100% 100%, 0 100%);
}
.lf-scrawl {
    font: 400 17px/1.25 var(--lf-hand);
    color: #9db4d6;
    white-space: nowrap;
}
.lf-leaf { color: #2f6b3f; }
.lf-leaf svg { filter: drop-shadow(0 0 1px rgba(0,0,0,.6)); }

.lf-dust {
    position: absolute;
    border-radius: 50%;
    background: rgba(200, 220, 255, .85);
    animation: lf-drift var(--d, 18s) ease-in-out var(--delay, 0s) infinite alternate;
}
@keyframes lf-drift {
    from { transform: translate3d(0, 0, 0); opacity: .25; }
    to   { transform: translate3d(var(--dx, 14px), var(--dy, -22px), 0); opacity: .9; }
}

/* ---------- Brand + hint ---------- */
.lf-brand {
    position: absolute; top: 1.5rem; left: clamp(1rem, 6vw, 6.5rem);
    z-index: 3; display: flex; align-items: center; gap: .65rem;
    pointer-events: none;
}
.lf-brand svg { width: 34px; height: 40px; flex: none; }
.lf-brand b { display: block; font-size: 1.4rem; line-height: 1.1; font-weight: 700; color: #4a4f5c; }
.lf-brand b em { font-style: normal; color: #1d4ed8; }
.lf-brand small { display: block; font-size: .8rem; color: #1e2a4a; }

.lf-hint {
    position: fixed; left: 0; right: 0; bottom: 1rem; z-index: 3;
    margin: 0; text-align: center; font-size: .75rem; color: #4a5060;
    pointer-events: none;
}
.lf-hint .touch { display: none; }
@media (hover: none), (pointer: coarse) {
    .lf-hint .fine { display: none; }
    .lf-hint .touch { display: inline; }
}

/* ---------- Login card ---------- */
.lf-card {
    --cx: -999px; --cy: -999px; --r: 1px;
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 440px;
    padding: 2rem 2.25rem 1.75rem;
    border: 1px solid var(--lf-line);
    border-radius: 1.5rem;
    background: rgba(7, 8, 11, .94);
}
/* lit border + lit surface, both masked by the same beam, in card coordinates */
.lf-card::before, .lf-card::after {
    content: ""; position: absolute; inset: -1px;
    border-radius: inherit; pointer-events: none;
}
.lf-card::before {
    border: 1px solid rgba(var(--lf-beam), .8);
    -webkit-mask-image: radial-gradient(circle var(--r) at var(--cx) var(--cy), #000, transparent);
            mask-image: radial-gradient(circle var(--r) at var(--cx) var(--cy), #000, transparent);
}
.lf-card::after {
    background: radial-gradient(circle var(--r) at var(--cx) var(--cy),
        rgba(var(--lf-beam), .13), rgba(var(--lf-beam), .04) 55%, transparent);
}

.lf-mark {
    display: inline-grid; place-items: center;
    width: 54px; height: 54px; border-radius: 1rem;
    background: #1d4ed8; color: #fff; font-size: 1.6rem;
}
.lf-card h1 { margin: 1rem 0 .25rem; font-size: 1.75rem; font-weight: 700; letter-spacing: -.01em; color: var(--lf-text); }
.lf-card h1 span { color: var(--lf-blue-hi); }
.lf-sub { margin: 0; font-size: .9rem; color: #9aa3b5; }

.lf-card .form-label { color: #e4e8f0; font-size: .85rem; }
.lf-card .input-group-text,
.lf-card .form-control,
.lf-card .btn-outline-secondary {
    background-color: var(--lf-field);
    border-color: #2a2b32;
    color: var(--lf-text);
}
.lf-card .input-group-text { color: #8b93a3; }
.lf-card .form-control::placeholder { color: #5f6676; opacity: 1; }
.lf-card .form-control:focus {
    background-color: var(--lf-field); color: var(--lf-text);
    border-color: var(--lf-blue-hi);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .28);
}
.lf-card .form-control.is-invalid { border-color: #b4424a; background-image: none; }
.lf-card .btn-outline-secondary { color: #8b93a3; border-left: none; }
.lf-card .btn-outline-secondary:hover { background-color: #16171c; color: #fff; }
.lf-card .btn:focus-visible,
.lf-card .form-check-input:focus-visible,
.lf-card a:focus-visible { outline: 2px solid var(--lf-blue-hi); outline-offset: 2px; box-shadow: none; }
.lf-card input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 1000px var(--lf-field) inset;
    -webkit-text-fill-color: var(--lf-text);
    caret-color: var(--lf-text);
}
.lf-card .invalid-feedback { color: #f08a90; }

.lf-card .form-check-input { background-color: var(--lf-field); border-color: #3a3b44; }
.lf-card .form-check-input:checked { background-color: var(--lf-blue); border-color: var(--lf-blue); }
.lf-card .form-check-label { color: #b6bdcb !important; }

.lf-submit {
    background: #1d4ed8; border: 1px solid #2559e0; color: #fff;
    border-radius: .75rem; transition: background-color .2s ease;
}
.lf-submit:hover, .lf-submit:focus { background: #2563eb; border-color: #3b82f6; color: #fff; }

.lf-demo { border-top: 1px solid var(--lf-line); }
.lf-demo-title { font-size: .68rem; letter-spacing: .05em; color: var(--lf-muted); text-transform: uppercase; }
.lf-card .demo-fill-btn {
    background: #0d0e12; border: 1px solid #2a2b32; color: #c9ced8;
    border-radius: 999px; font-size: .74rem;
    transition: border-color .2s ease, color .2s ease;
}
.lf-card .demo-fill-btn:hover { border-color: #3b82f6; color: #fff; background: #12141a; }

.lf-card .alert { background: #0d1320; border: 1px solid #22304f; color: #cfe0ff; }
.lf-card .alert-danger { background: #1b0e11; border-color: #4a2227; color: #f4b9be; }
.lf-card .btn-close { filter: invert(1) grayscale(1); opacity: .6; }

.lf-card .lf-register { color: #8b93a3; }
.lf-card .lf-register a { color: var(--lf-blue-hi); font-weight: 600; text-decoration: none; }
.lf-card .lf-register a:hover { text-decoration: underline; }

/* ---------- Flashlight indicator (replaces the OS cursor on desktop) ---------- */
.lf-torch {
    position: fixed; top: 0; left: 0; z-index: 50;
    width: 0; height: 0;
    pointer-events: none;
    opacity: 0;
    transition: opacity .25s ease;
    will-change: transform;
}
.lf-torch.is-on { opacity: 1; }
.lf-torch i {
    position: absolute; left: -4px; top: -4px; width: 8px; height: 8px;
    border-radius: 50%; border: 1.5px solid rgba(var(--lf-beam), .9);
}
.lf-torch svg { position: absolute; left: 10px; top: 10px; width: 26px; height: 26px; filter: drop-shadow(0 0 4px rgba(var(--lf-beam), .6)); }
@media (hover: none), (pointer: coarse) { .lf-torch { display: none; } }

html.lf-hide-cursor, html.lf-hide-cursor * { cursor: none !important; }

/* ---------- Responsive ---------- */
.lf-obj { left: var(--l); top: var(--t); }
@media (max-width: 767px) {
    .lf { padding: 4.75rem .75rem 3.5rem; }
    .lf-card { padding: 1.5rem 1.25rem 1.25rem; border-radius: 1.25rem; }
    .lf-brand small { display: none; }
    .lf-obj { left: var(--ml, var(--l)); top: var(--mt, var(--t)); width: var(--mw, var(--w, 120px)); }
    .lf-obj.hide-sm { display: none; }
}

/* ---------- Reduced motion ---------- */
@media (prefers-reduced-motion: reduce) {
    .lf *, .lf *::before, .lf *::after { animation: none !important; transition: none !important; }
}
/* ---------- Extras for the register form ---------- */
.lf-card--wide { max-width: 520px; }
.lf-card .form-select {
    background-color: var(--lf-field);
    border-color: #2a2b32;
    color: var(--lf-text);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%238b93a3' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
}
.lf-card .form-select:focus {
    background-color: var(--lf-field); color: var(--lf-text);
    border-color: var(--lf-blue-hi);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .28);
}
.lf-card .form-select.is-invalid { border-color: #b4424a; }
.lf-card .form-select option { background: #0d0e12; color: #f3f5f9; }
.lf-card .form-check-input.is-invalid { border-color: #b4424a; }
.lf-card .text-muted { color: var(--lf-muted) !important; }
.lf-card .form-check-label a { color: var(--lf-blue-hi); }
.lf-card hr { border-color: var(--lf-line); opacity: 1; }
</style>
