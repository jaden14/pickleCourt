<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Reserve your pickleball court online. Browse available courts, choose an hour, and pay securely.">
    <title>{{ config('app.name', 'Pickleball Courts') }}</title>
    <style>
        :root { color-scheme: dark; --amber:#f59e0b; --amber-dark:#b45309; --ink:#07090d; --panel:#111827; --muted:#94a3b8; --line:#263244; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; background:var(--ink); color:#f8fafc; font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; line-height:1.5; }
        a { color:inherit; text-decoration:none; }
        img { display:block; max-width:100%; }
        .container { width:min(1180px,calc(100% - 2rem)); margin-inline:auto; }
        .nav { position:absolute; z-index:10; top:0; right:0; left:0; border-bottom:1px solid rgb(255 255 255 / .12); background:rgb(7 9 13 / .6); backdrop-filter:blur(12px); }
        .nav-inner { min-height:74px; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .brand { display:flex; align-items:center; gap:.7rem; font-weight:900; letter-spacing:-.02em; }
        .brand-mark { display:grid; width:2.4rem; height:2.4rem; place-items:center; border-radius:50%; background:var(--amber); color:#111827; font-size:1.15rem; }
        .nav-links { display:flex; align-items:center; gap:1.25rem; color:#dbe2ea; font-size:.9rem; font-weight:650; }
        .button { display:inline-flex; min-height:44px; align-items:center; justify-content:center; gap:.45rem; padding:.7rem 1.05rem; border:1px solid transparent; border-radius:.65rem; cursor:pointer; font-weight:800; transition:transform .15s,background .15s,border-color .15s; }
        .button:hover { transform:translateY(-1px); }
        .button-primary { background:var(--amber); color:#111827; }
        .button-primary:hover { background:#fbbf24; }
        .button-secondary { border-color:rgb(255 255 255 / .22); background:rgb(255 255 255 / .07); color:white; }
        .hero { position:relative; min-height:720px; display:grid; align-items:center; overflow:hidden; background:#0f172a; }
        .hero-image { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.48; }
        .hero::after { content:""; position:absolute; inset:0; background:linear-gradient(90deg,rgb(3 7 18 / .96) 0%,rgb(3 7 18 / .73) 50%,rgb(3 7 18 / .42) 100%),linear-gradient(0deg,var(--ink),transparent 36%); }
        .hero-content { position:relative; z-index:1; max-width:730px; padding:9rem 0 6rem; }
        .eyebrow { display:inline-flex; align-items:center; gap:.45rem; margin-bottom:1.1rem; color:#fbbf24; font-size:.78rem; font-weight:900; letter-spacing:.12em; text-transform:uppercase; }
        .eyebrow::before { content:""; width:2rem; height:2px; background:currentColor; }
        h1 { max-width:700px; margin:0; font-size:clamp(3rem,7vw,6rem); line-height:.95; letter-spacing:-.06em; }
        .hero-copy { max-width:620px; margin:1.5rem 0 2rem; color:#d6dee8; font-size:clamp(1rem,2vw,1.2rem); }
        .hero-actions { display:flex; flex-wrap:wrap; gap:.75rem; }
        .login-note { margin-top:1rem; color:#aab7c6; font-size:.82rem; }
        .stats { position:relative; z-index:2; display:grid; grid-template-columns:repeat(3,1fr); margin-top:-4.5rem; border:1px solid var(--line); border-radius:1rem; overflow:hidden; background:rgb(17 24 39 / .96); box-shadow:0 20px 50px rgb(0 0 0 / .3); }
        .stat { padding:1.3rem 1.5rem; border-right:1px solid var(--line); }
        .stat:last-child { border:0; }
        .stat strong { display:block; color:#fbbf24; font-size:1.45rem; }
        .stat span { color:var(--muted); font-size:.8rem; }
        .section { padding:6rem 0; }
        .section-head { display:flex; align-items:end; justify-content:space-between; gap:2rem; margin-bottom:2rem; }
        .section-head h2 { margin:.25rem 0 0; font-size:clamp(2rem,4vw,3rem); letter-spacing:-.04em; }
        .section-head p { max-width:490px; margin:0; color:var(--muted); }
        .court-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
        .court-card { overflow:hidden; border:1px solid var(--line); border-radius:1rem; background:var(--panel); }
        .court-image { width:100%; height:230px; object-fit:cover; background:#1e293b; }
        .court-placeholder { height:230px; display:grid; place-items:center; background:linear-gradient(135deg,#1e293b,#0f172a); color:#64748b; font-size:3rem; }
        .court-body { padding:1.15rem; }
        .court-title { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:.7rem; }
        .court-title h3 { margin:0; font-size:1.1rem; }
        .available { padding:.25rem .5rem; border-radius:999px; background:rgb(16 185 129 / .15); color:#6ee7b7; font-size:.7rem; font-weight:800; }
        .rates { display:flex; gap:1rem; color:var(--muted); font-size:.78rem; }
        .rates strong { color:white; }
        .steps { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; counter-reset:steps; }
        .step { position:relative; padding:1.3rem; border:1px solid var(--line); border-radius:1rem; background:#0d131e; counter-increment:steps; }
        .step::before { content:"0" counter(steps); display:block; margin-bottom:1.2rem; color:var(--amber); font-size:1.7rem; font-weight:900; }
        .step h3 { margin:0 0 .4rem; }
        .step p { margin:0; color:var(--muted); font-size:.85rem; }
        .cta { padding:0 0 6rem; }
        .cta-box { display:flex; align-items:center; justify-content:space-between; gap:2rem; padding:2.3rem; border-radius:1.2rem; background:linear-gradient(120deg,#d97706,#f59e0b); color:#111827; }
        .cta-box h2 { margin:0; font-size:clamp(1.8rem,4vw,3rem); letter-spacing:-.04em; }
        .cta-box p { max-width:620px; margin:.35rem 0 0; color:#3f2a08; }
        .cta-box .button { flex:none; background:#111827; color:white; }
        footer { padding:2rem 0; border-top:1px solid var(--line); color:var(--muted); font-size:.82rem; }
        .footer-inner { display:flex; justify-content:space-between; gap:1rem; }
        @media (max-width:900px) { .court-grid { grid-template-columns:repeat(2,1fr); } .steps { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:680px) { .nav-links a:not(.button) { display:none; } .hero { min-height:650px; } .stats { grid-template-columns:1fr; margin-top:-2.5rem; } .stat { border-right:0; border-bottom:1px solid var(--line); } .court-grid,.steps { grid-template-columns:1fr; } .section { padding:4rem 0; } .section-head,.cta-box,.footer-inner { align-items:flex-start; flex-direction:column; } .court-image,.court-placeholder { height:210px; } }
    </style>
</head>
<body>
    <header class="nav">
        <div class="container nav-inner">
            <a href="{{ route('home') }}" class="brand"><span class="brand-mark">P</span>{{ config('app.name', 'Pickleball Courts') }}</a>
            <nav class="nav-links" aria-label="Main navigation">
                <a href="#courts">Courts</a>
                <a href="#how-it-works">How it works</a>
                @auth
                    <a class="button button-primary" href="{{ url('/admin/reservations') }}">Book a Court</a>
                @else
                    <a class="button button-secondary" href="{{ url('/admin/login') }}">Sign In</a>
                    <a class="button button-primary" href="{{ url('/admin/register') }}">Create Account</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            @if ($courts->first()?->image)
                <img class="hero-image" src="{{ Storage::disk('public')->url($courts->first()->image) }}" alt="Pickleball court">
            @endif
            <div class="container hero-content">
                <span class="eyebrow">Play more. Wait less.</span>
                <h1>Your court is ready.</h1>
                <p class="hero-copy">Browse live court availability, reserve your preferred hour, and submit payment in one simple booking experience.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="{{ url('/admin/reservations') }}">View Availability →</a>
                    <a class="button button-secondary" href="#courts">Explore Courts</a>
                </div>
                @guest
                    <p class="login-note">You can browse freely. Login is required when you are ready to book.</p>
                @endguest
            </div>
        </section>

        <div class="container stats">
            <div class="stat"><strong>{{ $courts->count() }}</strong><span>Courts currently reservable</span></div>
            <div class="stat"><strong>Hourly</strong><span>Flexible court reservations</span></div>
            <div class="stat"><strong>Live</strong><span>Availability and payment status</span></div>
        </div>

        <section id="courts" class="section">
            <div class="container">
                <div class="section-head">
                    <div><span class="eyebrow">Our courts</span><h2>Choose your playing space</h2></div>
                    <p>Only active and reservable courts are shown. Rates update automatically based on daytime and nighttime schedules.</p>
                </div>
                <div class="court-grid">
                    @forelse ($courts as $court)
                        <article class="court-card">
                            @if ($court->image)
                                <img class="court-image" src="{{ Storage::disk('public')->url($court->image) }}" alt="{{ $court->name }}" loading="lazy">
                            @else
                                <div class="court-placeholder">◉</div>
                            @endif
                            <div class="court-body">
                                <div class="court-title"><h3>{{ $court->name }}</h3><span class="available">Available</span></div>
                                <div class="rates">
                                    <span>Day <strong>₱{{ number_format((float) $court->day_hourly_rate, 0) }}/hr</strong></span>
                                    <span>Night <strong>₱{{ number_format((float) $court->night_hourly_rate, 0) }}/hr</strong></span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="court-card"><div class="court-body"><h3>No courts are currently available.</h3><p style="color:var(--muted)">Please check again later.</p></div></article>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="how-it-works" class="section" style="background:#090e16;">
            <div class="container">
                <div class="section-head">
                    <div><span class="eyebrow">Easy booking</span><h2>From court to checkout</h2></div>
                </div>
                <div class="steps">
                    <article class="step"><h3>Browse</h3><p>Check the weekly schedule and compare active courts without signing in.</p></article>
                    <article class="step"><h3>Sign in</h3><p>Login is required before holding court hours and proceeding to payment.</p></article>
                    <article class="step"><h3>Select</h3><p>Choose one or several available hours and review the live total.</p></article>
                    <article class="step"><h3>Pay</h3><p>Use the posted QR payment method and submit a reference or proof image.</p></article>
                </div>
            </div>
        </section>

        <section class="cta">
            <div class="container cta-box">
                <div><h2>Ready to get on court?</h2><p>View this week’s available schedules. You will be asked to sign in before completing a reservation.</p></div>
                <a class="button" href="{{ url('/admin/reservations') }}">Book Now →</a>
            </div>
        </section>
    </main>

    <footer><div class="container footer-inner"><span>© {{ now()->year }} {{ config('app.name', 'Pickleball Courts') }}</span><span>Online court reservations and payment verification</span></div></footer>
</body>
</html>
