{{--
  Community Lost & Found Recovery System: landing page
  Self-contained (no build step needed). Drop-in replacement for resources/views/welcome.blade.php.

  CONFIGURE the route names below to match your app (first existing name wins, else "#").
--}}
@php
    $go = function (array $names, array $params = [], string $fallback = '#') {
        foreach ($names as $n) {
            if (Route::has($n)) { return route($n, $params); }
        }
        return $fallback;
    };
    $u = [
        'login'    => $go(['login']),
        'register' => $go(['register']),
        'start'    => auth()->check() ? $go(['dashboard', 'home']) : $go(['register', 'login']),
        'browse'   => $go(['reports.index', 'items.index', 'browse']),
        'report'   => $go(['reports.create', 'items.create', 'report.create', 'report']),
        'search'   => $go(['reports.index', 'items.index', 'browse']),
        'map'      => $go(['map', 'reports.map']),
        'alerts'   => $go(['notifications.index', 'notifications']),
        'claims'   => $go(['claims.index', 'claims']),
        'stats'    => Route::has('public.stats') ? route('public.stats') : '',
    ];
    $types = [
        ['key' => 'lost_property',  'title' => 'Lost Property',  'tone' => 'rose',   'icon' => 'alert',  'text' => 'Report items you have lost: phones, wallets, IDs, keys, documents, and more.'],
        ['key' => 'found_property', 'title' => 'Found Property', 'tone' => 'mint',   'icon' => 'box',    'text' => 'Register items you have found so the rightful owner can be located and notified.'],
        ['key' => 'missing_pet',    'title' => 'Missing Pets',   'tone' => 'amber',  'icon' => 'paw',    'text' => 'Report a missing pet with photos and details so the community can help.'],
        ['key' => 'found_pet',      'title' => 'Found Pets',     'tone' => 'sky',    'icon' => 'heart',  'text' => 'Report a found or sheltered animal so it can be safely reunited with its owner.'],
    ];
    $features = [
        ['Advanced Search',  'search', 'Filter by category, location, date, and status to find matching reports.', $u['search']],
        ['Smart Matching',   'link',   'Matching compares category, description, location, and date to suggest likely pairs.', $u['browse']],
        ['Secure Claims',    'shield', 'Ownership checks protect against fraudulent claims before any handover.', $u['claims']],
        ['Community Map',    'map',    'See approximate incident locations across your barangay.', $u['map']],
        ['Real-Time Alerts', 'bell',   'Get notified about new matches, claims, and status changes.', $u['alerts']],
        ['Analytics',        'chart',  'Track reports, recoveries, and community activity over time.', $u['stats'] ?: $u['browse']],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#06113a">
    <title>{{ config('app.name', 'Lost & Found') }}: Community Lost &amp; Found Recovery System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Caveat:wght@500&display=swap" rel="stylesheet">
    @verbatim
    <style>
    :root{
        --navy-950:#040b26; --navy-900:#071545; --navy-800:#0a2070; --blue-600:#1d4ed8; --blue-500:#2f6bff;
        --cyan:#1ec8ff; --ink:#0b1a4a; --muted:#5b6b94; --paper:#f3f7ff; --card:#fff;
        --rose:#ff5d8f; --mint:#18c9a0; --amber:#ffb020; --sky:#2fa8ff; --violet:#8b5cf6;
        --font:'Plus Jakarta Sans',system-ui,-apple-system,'Segoe UI',sans-serif; --script:'Caveat',cursive;
        --ease:cubic-bezier(.2,.7,.2,1);
    }
    *,*::before,*::after{box-sizing:border-box;margin:0}
    html{scroll-behavior:smooth;scroll-padding-top:84px}
    body{font-family:var(--font);color:var(--ink);background:var(--paper);line-height:1.5;overflow-x:hidden;-webkit-font-smoothing:antialiased}
    a{color:inherit;text-decoration:none}
    :focus-visible{outline:2px solid var(--cyan);outline-offset:3px;border-radius:8px}
    .wrap{width:min(1160px,calc(100% - 40px));margin-inline:auto}
    svg{display:block}
    .ico{width:1.25em;height:1.25em;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}

    /* progress */
    #progress{position:fixed;inset:0 0 auto 0;height:3px;z-index:100;background:linear-gradient(90deg,var(--cyan),var(--blue-500),var(--violet));transform-origin:0 50%;transform:scaleX(0)}

    /* nav */
    .nav{position:fixed;inset:0 0 auto 0;z-index:90;transition:background .3s,backdrop-filter .3s,box-shadow .3s}
    .nav.scrolled{background:rgba(5,14,48,.72);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);box-shadow:0 1px 0 rgba(255,255,255,.08)}
    .nav .wrap{display:flex;align-items:center;gap:28px;height:72px}
    .brand{white-space:nowrap;display:flex;align-items:center;gap:10px;color:#fff}
    .brand b{display:block;font-size:18px;font-weight:800;line-height:1.1}
    .brand small{display:block;font-size:12px;color:var(--cyan);font-weight:500}
    .brand .logo{width:38px;height:38px;border-radius:11px;background:linear-gradient(145deg,var(--blue-500),var(--cyan));display:grid;place-items:center;box-shadow:0 0 22px rgba(30,200,255,.45)}
    .links{display:flex;gap:6px;margin-left:36px}
    .links a{white-space:nowrap;color:#c9d6ff;font-size:14px;font-weight:500;padding:8px 14px;border-radius:999px;position:relative;transition:color .2s}
    .links a:hover,.links a.active{color:#fff}
    .links a.active::after{content:"";position:absolute;left:14px;right:14px;bottom:2px;height:2px;border-radius:2px;background:var(--cyan)}
    .search{margin-left:auto;display:flex;align-items:center;gap:8px;width:min(300px,28vw);height:40px;padding:0 6px 0 16px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.07);color:#fff}
    .search input{flex:1;min-width:0;background:none;border:0;color:#fff;font:inherit;font-size:13px;outline:0}
    .search input::placeholder{color:#9fb2ea}
    .search button{background:none;border:0;color:#cfe0ff;cursor:pointer;width:32px;height:32px;display:grid;place-items:center;border-radius:50%}
    .search:focus-within{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(30,200,255,.2)}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;font:inherit;font-weight:600;font-size:14px;height:42px;padding:0 22px;border-radius:999px;border:1px solid transparent;cursor:pointer;transition:transform .25s var(--ease),box-shadow .25s,background .25s,border-color .25s;white-space:nowrap}
    .btn:hover{transform:translateY(-2px)}
    .btn-primary{color:#fff;background:linear-gradient(135deg,#2f6bff,#1e8bff);box-shadow:0 8px 26px rgba(47,107,255,.5)}
    .btn-primary:hover{box-shadow:0 12px 34px rgba(30,200,255,.55)}
    .btn-ghost{color:#fff;background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.28);backdrop-filter:blur(8px)}
    .btn-ghost:hover{border-color:var(--cyan);background:rgba(30,200,255,.12)}
    .btn-outline{color:var(--cyan);border-color:rgba(30,200,255,.6);background:rgba(30,200,255,.06)}
    .nav-actions{display:flex;gap:10px}
    .burger{display:none;margin-left:auto;width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.07);color:#fff;cursor:pointer;place-items:center}

    /* hero */
    .hero{position:relative;color:#fff;background:radial-gradient(1200px 600px at 75% 20%,#1b4fe0 0%,transparent 60%),linear-gradient(160deg,var(--navy-950),var(--navy-900) 45%,#0b2a96);padding:150px 0 120px;overflow:hidden;isolation:isolate}
    .hero::before{content:"";position:absolute;inset:-30%;z-index:-1;background:conic-gradient(from 0deg at 70% 30%,transparent,rgba(30,200,255,.12),transparent 40%,rgba(139,92,246,.1),transparent);animation:spin 40s linear infinite}
    .hero::after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:90px;background:var(--paper);clip-path:ellipse(75% 100% at 50% 100%)}
    .hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:30px;align-items:center}
    .pill{display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:var(--cyan);padding:6px 14px;border-radius:999px;background:rgba(30,200,255,.1);border:1px solid rgba(30,200,255,.25)}
    h1{font-size:clamp(34px,5.2vw,58px);line-height:1.08;font-weight:800;letter-spacing:-.02em;margin:20px 0 20px}
    .grad{background:linear-gradient(90deg,#22d3ee,#3b82f6 60%,#6d8dff);background-size:200% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:slide 6s ease-in-out infinite alternate}
    .lede{color:#c7d4ff;font-size:17px;max-width:520px}
    .cta{display:flex;gap:14px;margin-top:30px;flex-wrap:wrap}
    .cta .btn{height:50px;padding:0 28px;font-size:15px}
    .art{position:relative;aspect-ratio:1/.92;max-width:560px;margin-inline:auto;width:100%}
    .art svg.main{position:absolute;inset:0;width:100%;height:100%;overflow:visible}
    .layer{position:absolute;will-change:transform;transition:transform .25s linear}
    .orb{position:absolute;width:76px;height:76px;border-radius:50%;display:grid;place-items:center;color:#fff;background:radial-gradient(circle at 30% 25%,#4f86ff,#173fc4);box-shadow:0 0 0 6px rgba(79,134,255,.14),0 0 40px rgba(47,107,255,.6);animation:float 6s ease-in-out infinite}
    .orb .ico{width:32px;height:32px}
    .note{position:absolute;font-family:var(--script);font-size:25px;line-height:1.05;color:#9db8ff;transform:rotate(-8deg)}
    .paw{position:absolute;color:rgba(80,150,255,.55);animation:float 7s ease-in-out infinite}
    .particles{position:absolute;inset:0;z-index:-1;pointer-events:none}
    .particles i{position:absolute;width:3px;height:3px;border-radius:50%;background:#7fb4ff;opacity:.5;animation:twinkle 4s ease-in-out infinite}

    /* stats */
    .stats{position:relative;z-index:3;margin-top:-62px;display:grid;grid-template-columns:repeat(3,1fr);border-radius:20px;border:1px solid rgba(255,255,255,.14);background:linear-gradient(180deg,rgba(18,40,120,.78),rgba(8,22,80,.82));backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);box-shadow:0 24px 60px rgba(4,11,38,.5);color:#fff}
    .stat{display:flex;align-items:center;gap:18px;padding:22px 28px;position:relative}
    .stat+.stat::before{content:"";position:absolute;left:0;top:22%;bottom:22%;width:1px;background:rgba(255,255,255,.12)}
    .stat .chip .ico{width:24px;height:24px}
    .stat .chip{width:50px;height:50px;border-radius:50%;display:grid;place-items:center;flex:none}
    .stat strong{display:block;font-size:30px;line-height:1;font-weight:800;font-variant-numeric:tabular-nums}
    .stat span{font-size:13px;color:#b8c8f5}
    .skel{display:inline-block;width:46px;height:28px;border-radius:8px;background:linear-gradient(90deg,rgba(255,255,255,.08),rgba(255,255,255,.22),rgba(255,255,255,.08));background-size:200% 100%;animation:shimmer 1.2s linear infinite}
    .stats-msg{grid-column:1/-1;padding:0 28px 16px;font-size:13px;color:#ffd6a0;display:none}
    .stats-msg button{background:none;border:0;color:var(--cyan);font:inherit;text-decoration:underline;cursor:pointer}

    /* sections */
    section{position:relative}
    .sec{padding:84px 0}
    .head{text-align:center;margin-bottom:44px}
    .head .tag{display:inline-block;font-size:12px;font-weight:600;color:var(--blue-600);background:#dfe9ff;padding:5px 14px;border-radius:999px;margin-bottom:12px}
    .head h2{font-size:clamp(28px,3.6vw,38px);font-weight:800;letter-spacing:-.015em}
    .head p{color:var(--muted);margin-top:8px}
    .cards{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
    .card{position:relative;display:flex;flex-direction:column;gap:14px;padding:24px;border-radius:20px;background:var(--card);border:1px solid #e3ebff;box-shadow:0 10px 30px rgba(20,50,140,.07);transition:transform .35s var(--ease),box-shadow .35s,border-color .35s}
    .card::before{content:"";position:absolute;inset:-1px;border-radius:inherit;padding:1.5px;background:linear-gradient(135deg,var(--c,#2f6bff),transparent 60%);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:0;transition:opacity .35s;pointer-events:none}
    .card:hover,.card:focus-visible{transform:translateY(-8px);box-shadow:0 22px 50px rgba(30,70,200,.2),0 0 0 4px color-mix(in srgb,var(--c) 14%,transparent)}
    .card:hover::before,.card:focus-visible::before{opacity:1}
    .card .badge{width:52px;height:52px;border-radius:16px;display:grid;place-items:center;color:var(--c);background:color-mix(in srgb,var(--c) 14%,#fff)}
    .card .badge .ico{width:26px;height:26px}
    .card h3{font-size:17px;font-weight:700}
    .card p{font-size:13.5px;color:var(--muted)}
    .card .go{margin-top:auto;display:flex;justify-content:space-between;align-items:center;font-size:12px;font-weight:600;color:var(--c)}
    .card .go i{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;background:color-mix(in srgb,var(--c) 14%,#fff);transition:transform .3s var(--ease)}
    .card:hover .go i{transform:translateX(4px)}
    .rose{--c:var(--rose)} .mint{--c:var(--mint)} .amber{--c:var(--amber)} .sky{--c:var(--sky)} .violet{--c:var(--violet)} .blue{--c:var(--blue-500)}
    .count-note{font-size:12px;color:var(--muted)}

    /* how */
    .how{background:linear-gradient(170deg,var(--navy-950),#0a2070);color:#fff;clip-path:ellipse(120% 100% at 50% 50%);padding:96px 0}
    .how .head .tag{background:rgba(30,200,255,.12);color:var(--cyan);border:1px solid rgba(30,200,255,.25)}
    .how .head p{color:#a9bbee}
    .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;position:relative}
    .steps::before{content:"";position:absolute;top:60px;left:16%;right:16%;border-top:2px dashed rgba(120,160,255,.35);z-index:0}
    .step{position:relative;z-index:1;padding:26px;border-radius:20px;border:1px solid rgba(255,255,255,.14);background:linear-gradient(160deg,rgba(40,80,200,.34),rgba(10,25,90,.55));backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:transform .35s var(--ease),border-color .35s,box-shadow .35s}
    .step:hover{transform:translateY(-6px);border-color:rgba(30,200,255,.55);box-shadow:0 0 40px rgba(30,200,255,.22)}
    .step .n{width:44px;height:44px;border-radius:50%;display:grid;place-items:center;font-weight:800;color:#fff;background:linear-gradient(145deg,#3b82f6,#1ec8ff);box-shadow:0 0 24px rgba(30,200,255,.55);margin-bottom:16px}
    .step:nth-child(2) .n{background:linear-gradient(145deg,#8b5cf6,#3b82f6)}
    .step:nth-child(3) .n{background:linear-gradient(145deg,#18c9a0,#1ec8ff)}
    .step h3{font-size:18px;margin-bottom:6px}
    .step p{font-size:14px;color:#b8c8f5}

    /* features */
    .feat{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
    .feat .card{flex-direction:row;align-items:flex-start;gap:16px}
    .feat .card .badge{flex:none;width:46px;height:46px;border-radius:14px}

    /* cta band */
    .band{margin:10px 0 0;padding:64px 0;text-align:center;color:#fff;background:linear-gradient(120deg,#0a2070,#1d4ed8 55%,#12b5e5);background-size:200% 100%;animation:slide 10s ease-in-out infinite alternate}
    .band h2{font-size:clamp(24px,3.4vw,34px);font-weight:800}
    .band p{color:#d8e5ff;margin:8px 0 24px}
    footer{padding:26px 0;text-align:center;font-size:13px;color:var(--muted);background:var(--paper)}

    /* reveal */
    .rv{opacity:0;transform:translateY(28px);transition:opacity .7s var(--ease),transform .7s var(--ease);transition-delay:calc(var(--i,0)*90ms)}
    .rv.in{opacity:1;transform:none}
    .rv.l{transform:translateX(-30px)} .rv.l.in{transform:none}

    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
    @keyframes spin{to{transform:rotate(360deg)}}
    @keyframes slide{to{background-position:100% 0}}
    @keyframes twinkle{0%,100%{opacity:.15;transform:scale(1)}50%{opacity:.8;transform:scale(1.8)}}
    @keyframes shimmer{to{background-position:-200% 0}}
    @keyframes pulseRing{0%{transform:scale(.8);opacity:.7}100%{transform:scale(1.5);opacity:0}}
    .pad-ring{transform-origin:center;transform-box:fill-box;animation:pulseRing 3s ease-out infinite}

    @media (max-width:1020px){
        .links,.search{display:none}.burger{display:grid}
        .nav-actions{display:none}
        .nav.open{background:rgba(5,14,48,.96)}
        .nav.open .mobile{display:flex}
        .hero-grid{grid-template-columns:1fr}.art{max-width:420px}
        .cards{grid-template-columns:repeat(2,1fr)}.feat{grid-template-columns:repeat(2,1fr)}
    }
    .mobile{display:none;flex-direction:column;gap:6px;padding:8px 20px 20px;background:rgba(5,14,48,.96)}
    .mobile a:not(.btn){color:#d6e0ff;padding:12px 6px;border-bottom:1px solid rgba(255,255,255,.08)}
    .mobile .row{display:flex;gap:10px;margin-top:12px}.mobile .row .btn{flex:1}
    @media (max-width:680px){
        .hero{padding:118px 0 100px}
        .stats{grid-template-columns:1fr}.stat+.stat::before{left:8%;right:8%;top:0;bottom:auto;width:auto;height:1px}
        .cards,.feat{grid-template-columns:1fr}.steps{grid-template-columns:1fr}.steps::before{display:none}
        .note,.orb.sm-hide{display:none}.how{clip-path:none}
    }
    @media (prefers-reduced-motion:reduce){
        html{scroll-behavior:auto}
        *,*::before,*::after{animation:none!important;transition-duration:.01ms!important}
        .rv{opacity:1;transform:none}
    }
    </style>
    @endverbatim
</head>
<body>
<div id="progress" aria-hidden="true"></div>

@php
    $svg = [
      'search'=>'<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
      'alert' =>'<path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 10v5M12 18h.01"/>',
      'box'   =>'<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
      'paw'   =>'<circle cx="6" cy="10" r="1.7"/><circle cx="10" cy="6" r="1.7"/><circle cx="14" cy="6" r="1.7"/><circle cx="18" cy="10" r="1.7"/><path d="M12 11c-3 0-5.5 3-5.5 5.5 0 1.7 1.3 2.5 2.7 2.5 1 0 1.8-.5 2.8-.5s1.8.5 2.8.5c1.4 0 2.7-.8 2.7-2.5C17.5 14 15 11 12 11Z"/>',
      'heart' =>'<path d="M12 20s-7.5-4.6-7.5-10A4.3 4.3 0 0 1 12 7.6 4.3 4.3 0 0 1 19.5 10c0 5.4-7.5 10-7.5 10Z"/>',
      'link'  =>'<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3A4 4 0 0 0 11 18.700l1-1"/>',
      'shield'=>'<path d="M12 3 4.500 6v5.500c0 4.300 3 8 7.500 9.500 4.500-1.500 7.500-5.200 7.500-9.500V6L12 3Z"/><path d="m9 12 2 2 4-4"/>',
      'map'   =>'<path d="m3 6 6-2 6 2 6-2v14l-6 2-6-2-6 2V6Z"/><path d="M9 4v14M15 6v14"/>',
      'bell'  =>'<path d="M6 16V11a6 6 0 1 1 12 0v5l1.500 2h-15L6 16Z"/><path d="M10 21h4"/>',
      'chart' =>'<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
      'doc'   =>'<path d="M7 3h7l4 4v14H7V3Z"/><path d="M14 3v4h4M10 12h5M10 16h5"/>',
      'users' =>'<circle cx="9" cy="9" r="3.200"/><path d="M3 20c0-3.500 2.700-5.500 6-5.500s6 2 6 5.500"/><circle cx="17" cy="8" r="2.500"/><path d="M17 13.500c2.500 0 4.500 1.600 4.500 4.500"/>',
      'camera'=>'<path d="M4 8h3l1.500-2h7L17 8h3v11H4V8Z"/><circle cx="12" cy="13" r="3.500"/>',
      'arrow' =>'<path d="M5 12h14M13 6l6 6-6 6"/>',
      'info'  =>'<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
      'pin'   =>'<path d="M12 21s-6.500-5.800-6.500-11a6.500 6.500 0 0 1 13 0c0 5.200-6.500 11-6.500 11Z"/><circle cx="12" cy="10" r="2.300"/>',
      'user+' =>'<circle cx="9" cy="8" r="3.500"/><path d="M3 20c0-3.500 2.700-5.500 6-5.500M18 9v6M15 12h6"/>',
    ];
    $i = fn($n, $c = '') => '<svg class="ico '.$c.'" viewBox="0 0 24 24" aria-hidden="true">'.$svg[$n].'</svg>';
@endphp

<header class="nav" id="nav">
    <div class="wrap">
        <a href="#top" class="brand" aria-label="{{ config('app.name') }} home">
            <span class="logo">{!! $i('shield') !!}</span>
            <span><b>Lost &amp; Found</b><small>Reunite what's lost</small></span>
        </a>
        <nav class="links" aria-label="Primary">
            <a href="#top" class="active">Home</a>
            <a href="{{ $u['report'] !== '#' ? $u['report'] : '#report' }}">Report</a>
            <a href="{{ $u['browse'] !== '#' ? $u['browse'] : '#report' }}">Browse</a>
            <a href="#how">How It Works</a>
        </nav>
        <form class="search" role="search" action="{{ $u['search'] }}" method="GET">
            <input type="search" name="q" placeholder="Search lost items, pets, or reports…" aria-label="Search reports">
            <button type="submit" aria-label="Search">{!! $i('search') !!}</button>
        </form>
        <div class="nav-actions">
            @auth
                <a class="btn btn-outline" href="{{ $u['start'] }}">Dashboard</a>
            @else
                <a class="btn btn-outline" href="{{ $u['login'] }}">Sign In</a>
                <a class="btn btn-primary" href="{{ $u['register'] }}">Register</a>
            @endauth
        </div>
        <button class="burger" id="burger" aria-label="Open menu" aria-expanded="false" aria-controls="mobile">
            <svg class="ico" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>
    <div class="mobile" id="mobile">
        <a href="#top">Home</a>
        <a href="{{ $u['report'] !== '#' ? $u['report'] : '#report' }}">Report</a>
        <a href="{{ $u['browse'] !== '#' ? $u['browse'] : '#report' }}">Browse</a>
        <a href="#how">How It Works</a>
        <form class="search" style="display:flex;width:100%;margin-top:10px" action="{{ $u['search'] }}" method="GET">
            <input type="search" name="q" placeholder="Search reports…" aria-label="Search reports">
            <button type="submit" aria-label="Search">{!! $i('search') !!}</button>
        </form>
        <div class="row">
            @auth
                <a class="btn btn-primary" href="{{ $u['start'] }}">Dashboard</a>
            @else
                <a class="btn btn-outline" href="{{ $u['login'] }}">Sign In</a>
                <a class="btn btn-primary" href="{{ $u['register'] }}">Register</a>
            @endauth
        </div>
    </div>
</header>

<main id="top">
<section class="hero">
    <div class="particles" id="particles" aria-hidden="true"></div>
    <div class="wrap hero-grid">
        <div>
            <span class="pill rv">{!! $i('heart') !!} Together We Bring Things Home</span>
            <h1 class="rv" style="--i:1">Community Lost &amp; Found <span class="grad">Recovery</span> System</h1>
            <p class="lede rv" style="--i:2">Help your community recover lost belongings and reunite missing pets. Report, search, match, and recover, all in one secure platform.</p>
            <div class="cta rv" style="--i:3">
                <a class="btn btn-primary" href="{{ $u['start'] }}">{!! $i('user+') !!} Get Started {!! $i('arrow') !!}</a>
                <a class="btn btn-ghost" href="#how">{!! $i('info') !!} Learn More</a>
            </div>
        </div>

        <div class="art" aria-hidden="true">
            <div class="layer" data-depth="10" style="inset:0">
                <svg class="main" viewBox="0 0 560 515" fill="none">
                    <defs>
                        <linearGradient id="bag" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#4f86ff"/><stop offset="1" stop-color="#1237b8"/></linearGradient>
                        <linearGradient id="bag2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2f5fe8"/><stop offset="1" stop-color="#0d2a94"/></linearGradient>
                        <radialGradient id="pad" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#5fd4ff"/><stop offset="1" stop-color="#1b5be0"/></radialGradient>
                        <linearGradient id="pet" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffd9a0"/><stop offset="1" stop-color="#e58a3a"/></linearGradient>
                        <filter id="glow"><feGaussianBlur stdDeviation="10"/></filter>
                    </defs>
                    <ellipse cx="285" cy="440" rx="215" ry="52" fill="#1e6bff" opacity=".35" filter="url(#glow)"/>
                    <ellipse cx="285" cy="425" rx="200" ry="44" fill="url(#pad)"/>
                    <ellipse cx="285" cy="410" rx="170" ry="34" fill="#a9ecff" opacity=".55"/>
                    <ellipse class="pad-ring" cx="285" cy="410" rx="170" ry="34" stroke="#6fe0ff" stroke-width="2"/>
                    <!-- backpack -->
                    <path d="M215 130c0-40 30-62 70-62s70 22 70 62" stroke="#143aa8" stroke-width="16" stroke-linecap="round"/>
                    <rect x="170" y="120" width="230" height="280" rx="70" fill="url(#bag)"/>
                    <rect x="150" y="270" width="48" height="104" rx="22" fill="#1c47c8"/>
                    <rect x="372" y="270" width="48" height="104" rx="22" fill="#1c47c8"/>
                    <rect x="212" y="270" width="146" height="116" rx="34" fill="url(#bag2)" stroke="#6fa0ff" stroke-opacity=".5"/>
                    <path d="M212 305h146" stroke="#8fb4ff" stroke-opacity=".6" stroke-width="3"/>
                    <rect x="234" y="148" width="102" height="86" rx="26" fill="#1a43c2" stroke="#6fa0ff" stroke-opacity=".45"/>
                    <path d="M285 118v34" stroke="#cfe0ff" stroke-width="4" stroke-linecap="round"/>
                    <path d="M200 160c20-20 50-30 85-30" stroke="#a9c6ff" stroke-opacity=".55" stroke-width="5" stroke-linecap="round"/>
                    <!-- tag -->
                    <path d="M338 190 372 232" stroke="#dbe7ff" stroke-width="3"/>
                    <circle cx="380" cy="246" r="30" fill="#f4f8ff"/>
                    <g fill="#1f6bff"><circle cx="370" cy="240" r="4"/><circle cx="379" cy="234" r="4"/><circle cx="389" cy="236" r="4"/><circle cx="394" cy="245" r="4"/><path d="M382 246c-8 0-13 7-11 12 2 4 7 2 11 2s9 2 11-2c2-5-3-12-11-12Z"/></g>
                    <!-- pet card -->
                    <g transform="rotate(14 430 150)">
                        <rect x="360" y="70" width="150" height="150" rx="14" fill="#eef3ff"/>
                        <rect x="370" y="80" width="130" height="104" rx="8" fill="url(#pet)"/>
                        <circle cx="435" cy="130" r="30" fill="#fff3da" opacity=".85"/>
                        <circle cx="425" cy="125" r="4" fill="#5a3512"/><circle cx="445" cy="125" r="4" fill="#5a3512"/><ellipse cx="435" cy="139" rx="7" ry="5" fill="#5a3512"/>
                        <path d="M410 108c-12-4-20 10-14 26M460 108c12-4 20 10 14 26" stroke="#b86a28" stroke-width="10" stroke-linecap="round"/>
                        <rect x="400" y="170" width="92" height="26" rx="13" fill="#18c9a0"/>
                        <text x="446" y="188" text-anchor="middle" font-family="Plus Jakarta Sans,sans-serif" font-weight="700" font-size="14" fill="#fff">✓ Found!</text>
                    </g>
                    <!-- keys + phone -->
                    <g transform="translate(70 330) rotate(-12)">
                        <rect x="0" y="0" width="52" height="86" rx="10" fill="#0f2c8c" stroke="#6fa0ff" stroke-opacity=".6"/>
                        <rect x="5" y="8" width="42" height="64" rx="5" fill="#2f6bff" opacity=".7"/><circle cx="26" cy="78" r="3" fill="#9cc0ff"/>
                    </g>
                    <g transform="translate(470 330)" stroke="#bfe6ff" stroke-width="5" stroke-linecap="round" fill="none">
                        <circle cx="12" cy="12" r="12"/><path d="M22 20 54 52M42 40l8-8M50 48l8-8"/>
                    </g>
                </svg>
            </div>
            <div class="layer" data-depth="22" style="inset:0;pointer-events:none">
                <div class="orb" style="left:2%;top:8%"><svg class="ico" viewBox="0 0 24 24"><path d="M12 3 4.500 6v5.500c0 4.300 3 8 7.500 9.500 4.500-1.500 7.500-5.200 7.500-9.500V6L12 3Z"/></svg></div>
                <div class="orb" style="left:12%;top:40%;width:62px;height:62px;animation-delay:-2s">{!! $i('pin') !!}</div>
                <div class="orb sm-hide" style="right:6%;top:80%;width:66px;height:66px;animation-delay:-4s">{!! $i('heart') !!}</div>
                <svg class="paw" style="right:42%;top:-6%;width:34px;animation-delay:-1s" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"/><circle cx="10" cy="6" r="2"/><circle cx="14" cy="6" r="2"/><circle cx="18" cy="10" r="2"/><path d="M12 11c-3 0-5.500 3-5.500 5.500 0 1.700 1.300 2.500 2.700 2.500 1 0 1.800-.5 2.800-.5s1.800.5 2.800.5c1.400 0 2.700-.8 2.700-2.500C17.500 14 15 11 12 11Z"/></svg>
                <svg class="paw" style="left:-6%;top:56%;width:22px;animation-delay:-3s" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"/><circle cx="10" cy="6" r="2"/><circle cx="14" cy="6" r="2"/><circle cx="18" cy="10" r="2"/><path d="M12 11c-3 0-5.500 3-5.500 5.500 0 1.700 1.300 2.500 2.700 2.500 1 0 1.800-.5 2.800-.5s1.800.5 2.800.5c1.400 0 2.700-.8 2.700-2.500C17.500 14 15 11 12 11Z"/></svg>
                <span class="note" style="right:-2%;top:-9%">Lost something?<br>We're here to help!</span>
            </div>
        </div>
    </div>
</section>

<div class="wrap">
    <div class="stats rv" id="stats" data-url="{{ $u['stats'] }}" aria-live="polite">
        <div class="stat"><span class="chip" style="background:linear-gradient(145deg,#4f6bff,#2f3fd0)">{!! $i('doc') !!}</span>
            <div><strong data-stat="total_reports"><i class="skel"></i></strong><span>Total Reports</span></div></div>
        <div class="stat"><span class="chip" style="background:linear-gradient(145deg,#8b5cf6,#5b3fd6)">{!! $i('box') !!}</span>
            <div><strong data-stat="items_recovered"><i class="skel"></i></strong><span>Items Recovered</span></div></div>
        <div class="stat"><span class="chip" style="background:linear-gradient(145deg,#18c9a0,#0a9b7c)">{!! $i('users') !!}</span>
            <div><strong data-stat="active_members"><i class="skel"></i></strong><span>Active Members</span></div></div>
        <p class="stats-msg" id="stats-msg"></p>
    </div>
</div>

<section class="sec" id="report">
    <div class="wrap">
        <div class="head rv"><span class="tag">Report Types</span><h2>What would you like to report?</h2><p>Four categories help us organize and match reports efficiently.</p></div>
        <div class="cards">
            @foreach ($types as $k => $t)
                <a class="card {{ $t['tone'] }} rv" style="--i:{{ $k }}" href="{{ $u['report'] !== '#' ? $u['report'].(str_contains($u['report'], '?') ? '&' : '?').'type='.$t['key'] : $u['register'] }}">
                    <span class="badge">{!! $i($t['icon']) !!}</span>
                    <h3>{{ $t['title'] }}</h3>
                    <p>{{ $t['text'] }}</p>
                    <span class="go"><span class="count-note" data-type-count="{{ $t['key'] }}"></span><i>{!! $i('arrow') !!}</i></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="how" id="how">
    <div class="wrap">
        <div class="head rv"><span class="tag">Simple · Secure · Community Driven</span><h2>How It Works</h2><p>A simple process to help recover what matters.</p></div>
        <div class="steps">
            <div class="step rv" style="--i:0"><div class="n">1</div><h3>Submit a Report</h3><p>Create a detailed report with photos, location, and a description of the lost or found item or pet.</p></div>
            <div class="step rv" style="--i:2"><div class="n">2</div><h3>Smart Matching</h3><p>The system identifies possible matches by category, description, location, and date.</p></div>
            <div class="step rv" style="--i:4"><div class="n">3</div><h3>Recover &amp; Reunite</h3><p>Verify ownership through a secure claim, arrange a safe handover, and confirm the recovery.</p></div>
        </div>
    </div>
</section>

<section class="sec">
    <div class="wrap">
        <div class="head rv"><h2>Key Features</h2><p>Everything you need for a safer, more connected community.</p></div>
        <div class="feat">
            @php $tones = ['blue','violet','mint','sky','amber','rose']; @endphp
            @foreach ($features as $k => $f)
                <a class="card {{ $tones[$k] }} rv" style="--i:{{ $k }}" href="{{ $f[3] }}">
                    <span class="badge">{!! $i($f[1]) !!}</span>
                    <div><h3>{{ $f[0] }}</h3><p>{{ $f[2] }}</p></div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap rv">
        <h2>Lost something, or found something?</h2>
        <p>Every report helps someone in your community get closer to home.</p>
        <a class="btn btn-ghost" style="height:50px;padding:0 30px" href="{{ $u['start'] }}">{{ auth()->check() ? 'Go to dashboard' : 'Create a free account' }}</a>
    </div>
</section>
</main>
<footer>&copy; {{ date('Y') }} {{ config('app.name') }}. Community Lost &amp; Found Recovery System.</footer>

@verbatim
<script>
(function(){
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var $ = function(s,r){return (r||document).querySelector(s)}, $$ = function(s,r){return Array.prototype.slice.call((r||document).querySelectorAll(s))};

    /* scroll progress + sticky nav (single rAF-throttled handler) */
    var nav=$('#nav'), bar=$('#progress'), ticking=false;
    function onScroll(){
        var h=document.documentElement, max=h.scrollHeight-h.clientHeight;
        bar.style.transform='scaleX('+(max>0?h.scrollTop/max:0)+')';
        nav.classList.toggle('scrolled', h.scrollTop>16);
        ticking=false;
    }
    window.addEventListener('scroll',function(){ if(!ticking){ticking=true;requestAnimationFrame(onScroll)} },{passive:true});
    onScroll();

    /* mobile menu */
    var burger=$('#burger');
    burger.addEventListener('click',function(){
        var open=nav.classList.toggle('open'); burger.setAttribute('aria-expanded',open);
    });
    $$('#mobile a').forEach(function(a){a.addEventListener('click',function(){nav.classList.remove('open');burger.setAttribute('aria-expanded','false')})});

    /* scroll-triggered reveal */
    if('IntersectionObserver' in window && !reduce){
        var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}})},{threshold:.12,rootMargin:'0px 0px -6% 0px'});
        $$('.rv').forEach(function(el){io.observe(el)});
    } else { $$('.rv').forEach(function(el){el.classList.add('in')}); }

    /* particles (small fixed count) */
    var p=$('#particles');
    if(p && !reduce){
        for(var i=0;i<26;i++){var d=document.createElement('i');
            d.style.left=Math.random()*100+'%';d.style.top=Math.random()*100+'%';
            d.style.animationDelay=(Math.random()*4)+'s';d.style.animationDuration=(3+Math.random()*4)+'s';p.appendChild(d)}
    }

    /* parallax: pointer + scroll, transform only */
    var layers=$$('.layer[data-depth]'), art=$('.art');
    if(art && !reduce && window.matchMedia('(pointer:fine)').matches){
        var mx=0,my=0,raf=0;
        window.addEventListener('mousemove',function(e){
            mx=(e.clientX/innerWidth-.5);my=(e.clientY/innerHeight-.5);
            if(!raf)raf=requestAnimationFrame(function(){raf=0;
                layers.forEach(function(l){var d=+l.dataset.depth;l.style.transform='translate3d('+(-mx*d)+'px,'+(-my*d)+'px,0)'})})
        },{passive:true});
    }

    /* animated counters */
    function countUp(el,to){
        if(reduce||to===0){el.textContent=to.toLocaleString();return}
        var t0=performance.now(),dur=1400;
        (function tick(t){var k=Math.min(1,(t-t0)/dur),e=1-Math.pow(1-k,3);
            el.textContent=Math.round(to*e).toLocaleString(); if(k<1)requestAnimationFrame(tick)})(t0);
    }

    /* live stats from backend: loading / error / empty states */
    var box=$('#stats'), msg=$('#stats-msg'), url=box.dataset.url;
    function setErr(text){
        $$('[data-stat]',box).forEach(function(el){el.textContent='–'});
        msg.style.display='block'; msg.innerHTML=text+' <button type="button" id="retry">Retry</button>';
        var r=$('#retry'); if(r) r.onclick=load;
    }
    function load(){
        msg.style.display='none';
        $$('[data-stat]',box).forEach(function(el){el.innerHTML='<i class="skel"></i>'});
        if(!url){ setErr('Live statistics are not connected yet.'); var r=$('#retry'); if(r) r.remove(); return; }
        fetch(url,{headers:{'Accept':'application/json'},credentials:'same-origin'})
            .then(function(r){ if(!r.ok) throw new Error(r.status); return r.json(); })
            .then(function(d){
                d = d && d.data ? d.data : d;
                var keys=['total_reports','items_recovered','active_members'], any=false;
                keys.forEach(function(k){
                    var el=$('[data-stat="'+k+'"]',box), v=Number(d[k]);
                    if(isFinite(v)){any=true; var io2=new IntersectionObserver(function(es,o){if(es[0].isIntersecting){countUp(el,v);o.disconnect()}}); io2.observe(el);}
                    else el.textContent='–';
                });
                if(!any) setErr('No statistics are available yet.');
                if(d.by_type){ $$('[data-type-count]').forEach(function(el){var n=d.by_type[el.dataset.typeCount]; if(n!=null) el.textContent=Number(n).toLocaleString()+' reports'}); }
            })
            .catch(function(){ setErr('Could not load live statistics.'); });
    }
    load();
})();
</script>
@endverbatim
</body>
</html>