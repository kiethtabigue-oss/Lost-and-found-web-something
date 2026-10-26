{{-- Hidden objects, brand, hint and flashlight indicator. Include as the first child of .lf#lfRoot. --}}
    {{-- Hidden objects. Decorative only; revealed by the beam mask. --}}
    <div class="lf-scene" aria-hidden="true">
        <svg width="0" height="0" style="position:absolute">
            <defs>
                <symbol id="lf-leaf" viewBox="0 0 60 80">
                    <path d="M30 4 C8 22 6 52 30 76 C54 52 52 22 30 4Z" fill="currentColor"/>
                    <path d="M30 14 V72" stroke="#0c2414" stroke-width="2" fill="none"/>
                </symbol>
            </defs>
        </svg>
        <div class="lf-wash"></div>

        {{-- Missing-pet poster --}}
        <div class="lf-obj" style="--l:14%;--t:24%;--w:132px;--rot:-6deg;--ml:20%;--mt:9%;--mw:92px" data-lit>
            <svg viewBox="0 0 130 170">
                <rect x="2" y="2" width="126" height="166" rx="3" fill="#e7dfcb"/>
                <text x="65" y="26" text-anchor="middle" font-size="17" font-weight="700" fill="#8a1c1c" font-family="system-ui,sans-serif">MISSING</text>
                <rect x="16" y="36" width="98" height="84" fill="#5d3f23"/>
                <ellipse cx="42" cy="70" rx="11" ry="20" fill="#8e5a22" transform="rotate(18 42 70)"/>
                <ellipse cx="88" cy="70" rx="11" ry="20" fill="#8e5a22" transform="rotate(-18 88 70)"/>
                <ellipse cx="65" cy="80" rx="27" ry="26" fill="#c98a3e"/>
                <ellipse cx="65" cy="93" rx="15" ry="11" fill="#e4b46a"/>
                <ellipse cx="65" cy="86" rx="6" ry="4.5" fill="#1a1208"/>
                <circle cx="54" cy="74" r="3" fill="#1a1208"/><circle cx="76" cy="74" r="3" fill="#1a1208"/>
                <ellipse cx="65" cy="101" rx="5" ry="6" fill="#e07a8a"/>
                <text x="65" y="141" text-anchor="middle" font-size="16" fill="#3b2f1a" font-family="Segoe Print,Bradley Hand,Comic Sans MS,cursive">Biscuit</text>
                <text x="65" y="158" text-anchor="middle" font-size="10" fill="#5a4a2d" font-family="Segoe Print,Bradley Hand,Comic Sans MS,cursive">please call us ♥</text>
            </svg>
            <span class="lf-lbl">Missing pet — Biscuit</span>
        </div>

        {{-- Handwritten note --}}
        <div class="lf-obj hide-sm" style="--l:9%;--t:62%;--w:150px;--rot:-14deg">
            <div class="lf-note">Lost<br>but not<br>forgotten ♥</div>
            <span class="lf-lbl">A note someone left behind</span>
        </div>

        {{-- FOUND keychain --}}
        <div class="lf-obj" style="--l:25%;--t:46%;--w:78px;--rot:10deg;--ml:84%;--mt:84%;--mw:60px">
            <svg viewBox="0 0 100 150">
                <circle cx="50" cy="22" r="16" fill="none" stroke="#c9d1dc" stroke-width="4"/>
                <path d="M50 38 V52" stroke="#9aa6b8" stroke-width="3"/>
                <rect x="14" y="52" width="72" height="86" rx="22" fill="#cfd6e2"/>
                <rect x="20" y="58" width="60" height="74" rx="17" fill="none" stroke="#8d99ad" stroke-width="1.5"/>
                <text x="50" y="102" text-anchor="middle" font-size="17" font-weight="800" fill="#27324a" font-family="system-ui,sans-serif" letter-spacing="1">FOUND</text>
                <path d="M50 120 C40 110 40 104 46 104 C48 104 50 106 50 107 C50 106 52 104 54 104 C60 104 60 110 50 120Z" fill="#2563eb" transform="translate(0 -2)"/>
            </svg>
            <span class="lf-lbl">FOUND keychain</span>
        </div>

        {{-- Earbuds --}}
        <div class="lf-obj" style="--l:27%;--t:78%;--w:128px;--rot:12deg;--ml:18%;--mt:90%;--mw:96px">
            <svg viewBox="0 0 150 90">
                <path d="M38 36 C30 80 70 88 78 60 C84 40 108 30 114 30" fill="none" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="34" cy="26" r="13" fill="#e6ebf2"/><rect x="29" y="34" width="10" height="26" rx="5" fill="#e6ebf2"/>
                <circle cx="118" cy="28" r="13" fill="#e6ebf2"/><rect x="113" y="36" width="10" height="26" rx="5" fill="#e6ebf2"/>
                <circle cx="31" cy="24" r="4" fill="#aab4c4"/><circle cx="121" cy="26" r="4" fill="#aab4c4"/>
            </svg>
            <span class="lf-lbl">Earbuds, one missing a tip</span>
        </div>

        {{-- Backpack with LOST tag --}}
        <div class="lf-obj" style="--l:75%;--t:30%;--w:170px;--rot:3deg;--ml:78%;--mt:11%;--mw:104px">
            <svg viewBox="0 0 160 190">
                <path d="M60 32 C58 6 102 6 100 32" fill="none" stroke="#1e3a8a" stroke-width="7" stroke-linecap="round"/>
                <path d="M24 72 Q24 26 80 26 Q136 26 136 72 L141 160 Q141 178 123 178 L37 178 Q19 178 19 160 Z" fill="#2c5bc9"/>
                <path d="M30 84 Q80 102 130 84" fill="none" stroke="#1d3d91" stroke-width="3"/>
                <path d="M34 110 Q34 100 46 100 L114 100 Q126 100 126 110 L128 160 Q128 168 118 168 L42 168 Q32 168 32 160 Z" fill="#2449a8"/>
                <path d="M42 114 H118" stroke="#9db9f2" stroke-width="2" stroke-dasharray="3 3" opacity=".75"/>
                <path d="M92 70 Q88 82 100 90" fill="none" stroke="#c8a26a" stroke-width="2"/>
                <g transform="rotate(-12 100 110)">
                    <path d="M82 88 H114 L120 96 V132 Q120 136 116 136 H84 Q80 136 80 132 V92 Z" fill="#d9b377"/>
                    <circle cx="100" cy="95" r="2.5" fill="#2c5bc9"/>
                    <text x="100" y="116" text-anchor="middle" font-size="14" fill="#4a3516" font-family="Segoe Print,Bradley Hand,Comic Sans MS,cursive">Lost</text>
                    <text x="100" y="130" text-anchor="middle" font-size="11" fill="#7a2f2f">♥</text>
                </g>
            </svg>
            <span class="lf-lbl">Blue backpack — tagged LOST</span>
        </div>

        {{-- Phone --}}
        <div class="lf-obj" style="--l:70%;--t:70%;--w:96px;--rot:-24deg;--ml:14%;--mt:76%;--mw:70px">
            <svg viewBox="0 0 110 190">
                <rect x="4" y="4" width="102" height="182" rx="16" fill="#1b1d24" stroke="#5b6272" stroke-width="3"/>
                <rect x="12" y="14" width="86" height="162" rx="8" fill="#0b0d14"/>
                <rect x="40" y="18" width="30" height="6" rx="3" fill="#1b1d24"/>
                <path d="M20 40 L88 150 M40 150 L70 100" stroke="#2a3350" stroke-width="1" fill="none"/>
                <text x="55" y="84" text-anchor="middle" font-size="16" fill="#9fc0ff" font-family="system-ui,sans-serif">12 missed</text>
                <text x="55" y="102" text-anchor="middle" font-size="16" fill="#9fc0ff" font-family="system-ui,sans-serif">calls</text>
                <path d="M12 14 H60 L12 80Z" fill="#fff" opacity=".05"/>
            </svg>
            <span class="lf-lbl">Phone, 12 missed calls</span>
        </div>

        {{-- Keys --}}
        <div class="lf-obj" style="--l:84%;--t:66%;--w:112px;--rot:-8deg;--ml:80%;--mt:78%;--mw:80px">
            <svg viewBox="0 0 130 130">
                <circle cx="38" cy="40" r="22" fill="none" stroke="#c9d1dc" stroke-width="5"/>
                <g transform="rotate(38 38 40)">
                    <rect x="52" y="35" width="66" height="10" rx="4" fill="#b8c2d0"/>
                    <rect x="98" y="45" width="8" height="13" fill="#b8c2d0"/><rect x="110" y="45" width="6" height="9" fill="#b8c2d0"/>
                </g>
                <g transform="rotate(78 38 40)">
                    <rect x="52" y="35" width="58" height="10" rx="4" fill="#9fb0c6"/>
                    <rect x="90" y="45" width="8" height="11" fill="#9fb0c6"/><rect x="102" y="45" width="6" height="8" fill="#9fb0c6"/>
                </g>
            </svg>
            <span class="lf-lbl">A set of keys</span>
        </div>

        {{-- Handwritten clues --}}
        <div class="lf-obj lf-scrawl hide-sm" style="--l:87%;--t:86%;--w:auto;--rot:-4deg">check the bench by the old fountain</div>
        <div class="lf-obj lf-scrawl hide-sm" style="--l:12%;--t:90%;--w:auto;--rot:3deg">last seen near the bus stop</div>
        <div class="lf-obj lf-scrawl" style="--l:50%;--t:94%;--w:auto;--rot:-2deg;--ml:50%;--mt:95%">left at the barangay hall?</div>

        {{-- Leaves --}}
        <div class="lf-obj lf-leaf" style="--l:6%;--t:40%;--w:44px;--rot:30deg"><svg viewBox="0 0 60 80"><use href="#lf-leaf"/></svg></div>
        <div class="lf-obj lf-leaf" style="--l:36%;--t:16%;--w:34px;--rot:-50deg;--ml:90%;--mt:30%"><svg viewBox="0 0 60 80"><use href="#lf-leaf"/></svg></div>
        <div class="lf-obj lf-leaf" style="--l:93%;--t:44%;--w:48px;--rot:70deg;--ml:8%;--mt:55%"><svg viewBox="0 0 60 80"><use href="#lf-leaf"/></svg></div>
        <div class="lf-obj lf-leaf" style="--l:64%;--t:90%;--w:38px;--rot:-20deg;--ml:60%;--mt:90%"><svg viewBox="0 0 60 80"><use href="#lf-leaf"/></svg></div>
        <div class="lf-obj lf-leaf hide-sm" style="--l:80%;--t:8%;--w:30px;--rot:120deg"><svg viewBox="0 0 60 80"><use href="#lf-leaf"/></svg></div>
        <div class="lf-obj lf-leaf hide-sm" style="--l:20%;--t:66%;--w:36px;--rot:-100deg"><svg viewBox="0 0 60 80"><use href="#lf-leaf"/></svg></div>

        {{-- Dust motes are generated by the script below --}}
    </div>

    <div class="lf-brand" aria-hidden="true">
        <svg viewBox="0 0 34 40"><path d="M17 1 L32 7 V19 C32 29 25 36 17 39 C9 36 2 29 2 19 V7Z" fill="#12307a" stroke="#1d4ed8" stroke-width="1.5"/><path d="M17 27 C11 22 11 17 14.5 17 C16 17 17 18.2 17 19 C17 18.2 18 17 19.5 17 C23 17 23 22 17 27Z" fill="#0b1a42" stroke="#2f5ccc" stroke-width="1"/></svg>
        <div><b>Lost &amp; <em>Found</em></b><small>Reunite what's lost</small></div>
    </div>

    <p class="lf-hint" aria-hidden="true">
        <span class="fine">Move your light to find what's lost</span>
        <span class="touch">Tap anywhere to shine a light</span>
    </p>

    {{-- Flashlight indicator: replaces the OS cursor on desktop, never intercepts input --}}
    <div class="lf-torch" id="lfTorch" aria-hidden="true">
        <i></i>
        <svg viewBox="0 0 26 26"><g transform="rotate(-45 13 13)"><rect x="9" y="3" width="8" height="5" rx="1" fill="#e8f0ff"/><path d="M8 8 H18 L16 13 V22 Q16 24 14 24 H12 Q10 24 10 22 V13Z" fill="#8a94a8"/><rect x="10" y="14" width="6" height="2" fill="#4b5365"/></g></svg>
    </div>
