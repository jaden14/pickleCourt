<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f6f7fb">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/pwa-icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/pwa-icon.svg">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>CaPikol Ballers · Open Play</title>
    <style>
        :root { --blue:#1673d1; --blue-dark:#0e5fb3; --ink:#18202b; --muted:#697489; --line:#d8dee8; --bg:#f6f7fb; --card:#fff; --soft:#f1f3f8; --disabled:#c9d1df; --shadow:0 8px 30px rgb(25 39 64 / .08); }
        * { box-sizing:border-box; }
        html,body { margin:0; min-height:100%; background:#e9edf3; color:var(--ink); font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; }
        button,input,textarea { font:inherit; }
        button { color:inherit; }
        [hidden] { display:none!important; }
        .app { width:100%; min-height:100dvh; margin:auto; background:var(--bg); }
        .screen { display:none; min-height:100dvh; padding:calc(1.5rem + env(safe-area-inset-top)) clamp(1.25rem,4vw,2.5rem) calc(6.75rem + env(safe-area-inset-bottom)); }
        .screen.active { display:block; animation:enter .22s ease-out; }
        @keyframes enter { from { opacity:.35; transform:translateY(4px); } }
        .content { width:min(100%,720px); margin-inline:auto; }
        .home-content { min-height:calc(100dvh - 10rem); display:flex; flex-direction:column; }
        .home-actions { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin:.15rem 0 .7rem; }
        .dashboard-back { display:inline-flex; align-items:center; gap:.4rem; width:max-content; color:#536176; text-decoration:none; font-size:.78rem; font-weight:750; }
        .dashboard-back:hover { color:var(--blue); }
        .theme-toggle { display:grid; width:36px; height:36px; place-items:center; border:1px solid var(--line); border-radius:50%; background:#fff; cursor:pointer; font-size:1rem; }
        .brand { display:flex; align-items:center; gap:.65rem; margin-top:1.6rem; color:#4d5869; font-size:.78rem; font-weight:800; letter-spacing:.18em; }
        .ball { position:relative; width:22px; height:22px; flex:none; border-radius:50%; background:#f9a81a; box-shadow:inset -2px -2px 0 rgb(0 0 0 / .08); }
        .ball:after { content:"···"; position:absolute; inset:-2px 0 0 4px; color:#a96708; font-size:13px; letter-spacing:-1px; transform:rotate(55deg); }
        h1 { margin:.65rem 0 0; font-size:clamp(2.35rem,7vw,4rem); line-height:1; letter-spacing:-.055em; }
        .subtitle { margin:.45rem 0 1.7rem; color:var(--muted); font-size:1rem; }
        .primary-card { width:100%; display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1.25rem 1.15rem; border:0; border-radius:14px; background:var(--blue); color:#fff; cursor:pointer; text-align:left; box-shadow:0 8px 18px rgb(22 115 209 / .2); }
        .primary-card:hover { background:var(--blue-dark); }
        .primary-card strong { display:block; margin-bottom:.22rem; font-size:1rem; }
        .primary-card small { color:#cce5ff; }
        .arrow { flex:none; font-size:1.65rem; font-weight:300; }
        .section-label { display:block; margin:1.35rem 0 .45rem; color:#647084; font-size:.7rem; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
        .session-card,.form-card,.match-card { border:1px solid var(--line); border-radius:15px; background:var(--card); box-shadow:0 2px 5px rgb(21 30 45 / .025); }
        .session-card { margin-top:1rem; padding:1.1rem; }
        .session-card h3 { margin:.35rem 0 .25rem; font-size:1.1rem; }
        .meta { margin:0; color:var(--muted); font-size:.86rem; }
        .card-link { width:100%; margin-top:.7rem; padding:.25rem 0 0; border:0; background:none; color:var(--blue); cursor:pointer; text-align:right; font-weight:750; }
        .empty { margin:2rem 0; color:#98a2b2; text-align:center; }
        .topbar { display:flex; align-items:flex-start; gap:.75rem; margin-bottom:1.45rem; }
        .back { display:grid; width:32px; height:38px; margin-left:-.5rem; place-items:center; border:0; background:none; cursor:pointer; font-size:1.9rem; line-height:1; }
        .topbar h2 { margin:0; font-size:clamp(1.65rem,5vw,2.1rem); line-height:1.15; letter-spacing:-.035em; }
        .topbar p { margin:.15rem 0 0; color:var(--muted); font-size:.82rem; }
        .field { width:100%; height:45px; padding:0 .85rem; border:1px solid #cbd3df; border-radius:11px; outline:none; background:#fff; color:var(--ink); }
        .date-range { display:grid; grid-template-columns:1fr 1fr; gap:.65rem; }
        .date-range label span { display:block; margin:0 0 .35rem; color:var(--muted); font-size:.72rem; font-weight:700; }
        @media (max-width:480px) { .date-range { grid-template-columns:1fr; } }
        .field:focus,textarea:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgb(22 115 209 / .1); }
        .stepper { display:flex; align-items:center; gap:1rem; }
        .circle-button { width:43px; height:43px; border:1px solid var(--line); border-radius:50%; background:#fff; cursor:pointer; font-size:1.35rem; }
        .stepper-output { min-width:1.5rem; text-align:center; font-size:1.2rem; font-weight:750; }
        .option { position:relative; display:block; width:100%; margin:.5rem 0; padding:.85rem 3rem .85rem .85rem; border:1px solid #cbd3df; border-radius:11px; background:#fff; color:var(--ink); cursor:pointer; font:inherit; text-align:left; }
        .option.selected { border:2px solid var(--blue); padding:.8rem calc(3rem - 1px) .8rem calc(.85rem - 1px); background:#f4f8ff; }
        .option strong { display:block; font-size:.87rem; }
        .option small { display:block; margin-top:.15rem; color:var(--muted); line-height:1.35; }
        .radio { position:absolute; top:50%; right:.85rem; width:20px; height:20px; border:2px solid #c7cfdb; border-radius:50%; transform:translateY(-50%); }
        .selected .radio { border-color:var(--blue); box-shadow:inset 0 0 0 4px #fff; background:var(--blue); }
        .row-between { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .toggle { width:57px; height:29px; padding:3px; border:0; border-radius:99px; background:#dce2ed; cursor:pointer; transition:.2s; }
        .toggle:after { content:""; display:block; width:23px; height:23px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgb(0 0 0 / .16); transition:.2s; }
        .toggle.on { background:var(--blue); }
        .toggle.on:after { transform:translateX(28px); }
        .pills { display:flex; gap:.55rem; }
        .pill { min-width:43px; height:37px; border:1px solid #ccd4df; border-radius:99px; background:#fff; cursor:pointer; }
        .pill.selected { border-color:var(--blue); background:var(--blue); color:white; }
        .sticky-action { position:fixed; z-index:10; right:0; bottom:0; left:0; padding:.7rem max(1.25rem,calc((100vw - 720px)/2)) calc(.7rem + env(safe-area-inset-bottom)); border-top:1px solid #e1e5ec; background:rgb(246 247 251 / .94); backdrop-filter:blur(12px); }
        .main-button { width:100%; min-height:50px; border:0; border-radius:12px; background:var(--blue); color:#fff; cursor:pointer; font-weight:750; }
        .main-button:disabled { background:var(--disabled); cursor:not-allowed; }
        .form-card { padding:1rem; }
        .skill-head { display:flex; align-items:center; justify-content:space-between; gap:.5rem; }
        .help { border:0; background:none; color:var(--blue); cursor:pointer; font-size:.76rem; font-weight:700; }
        .skills { display:grid; grid-template-columns:repeat(5,1fr); gap:.4rem; }
        .skill { min-width:0; padding:.55rem .2rem; border:1px solid transparent; border-radius:9px; cursor:pointer; font-weight:800; transition:transform .16s ease,box-shadow .16s ease,filter .16s ease; }
        .skill:hover { transform:translateY(-2px); filter:saturate(1.08); box-shadow:0 5px 12px rgb(24 32 43 / .12); }
        .skill:focus-visible { outline:3px solid rgb(22 115 209 / .22); outline-offset:2px; }
        .skill small { display:block; margin-top:.1rem; color:#f5a000; font-size:.58rem; font-weight:800; letter-spacing:.02em; text-shadow:0 1px 0 rgb(255 255 255 / .7); }
        .skill[data-rating="1"] { border-color:#fecaca; background:#fff1f2; color:#be123c; }
        .skill[data-rating="2"] { border-color:#fed7aa; background:#fff7ed; color:#c2410c; }
        .skill[data-rating="3"] { border-color:#fde68a; background:#fffbeb; color:#a16207; }
        .skill[data-rating="4"] { border-color:#bef264; background:#f7fee7; color:#4d7c0f; }
        .skill[data-rating="5"] { border-color:#99f6e4; background:#f0fdfa; color:#0f766e; }
        .skill.selected { border-color:transparent; color:white; box-shadow:0 6px 14px rgb(24 32 43 / .18); transform:translateY(-1px); }
        .skill.selected[data-rating="1"] { background:linear-gradient(135deg,#fb7185,#e11d48); }
        .skill.selected[data-rating="2"] { background:linear-gradient(135deg,#fb923c,#ea580c); }
        .skill.selected[data-rating="3"] { background:linear-gradient(135deg,#facc15,#d97706); }
        .skill.selected[data-rating="4"] { background:linear-gradient(135deg,#84cc16,#16a34a); }
        .skill.selected[data-rating="5"] { background:linear-gradient(135deg,#2dd4bf,#1684d8); }
        .skill.selected small { color:#fff4b8; text-shadow:0 1px 2px rgb(0 0 0 / .2); }
        .add-button { width:100%; height:44px; margin-top:.65rem; border:0; border-radius:10px; background:var(--blue); color:#fff; cursor:pointer; font-weight:700; }
        .add-button:disabled { background:#d9dee7; color:#9aa5b7; }
        .text-button { display:block; margin:.8rem auto 0; border:0; background:none; color:var(--blue); cursor:pointer; font-weight:650; }
        textarea { width:100%; min-height:115px; margin-top:.8rem; padding:.75rem; resize:vertical; border:0; border-radius:10px; outline:none; background:var(--soft); color:var(--ink); }
        .hint { margin:.35rem 0; color:#7b8596; font-size:.7rem; }
        .players { display:grid; gap:.5rem; margin-top:1rem; }
        .player { display:flex; align-items:center; gap:.7rem; padding:.7rem .8rem; border:1px solid var(--line); border-radius:11px; background:white; }
        .avatar { display:grid; width:34px; height:34px; flex:none; place-items:center; border-radius:50%; background:#e5f1ff; color:var(--blue); font-weight:800; }
        .player-info { min-width:0; flex:1; }
        .player-info strong { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .player-info small { color:var(--muted); }
        .rating-stars { color:#f6a800; letter-spacing:.04em; text-shadow:0 1px 1px rgb(155 98 0 / .12); }
        .rating-stars .empty-star { color:#d8dee8; text-shadow:none; }
        .remove { border:0; background:none; color:#9aa3b0; cursor:pointer; font-size:1.2rem; }
        .team-rosters { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.8rem; margin-top:1rem; }
        .team-roster { overflow:hidden; border:1px solid var(--line); border-left:5px solid var(--team-color,#1673d1); border-radius:12px; background:var(--card); }
        .team-roster-head { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.75rem .85rem; background:var(--soft); }
        .team-roster-name { min-width:0; flex:1; border:0; border-bottom:1px solid transparent; outline:0; background:transparent; color:var(--ink); font:800 .85rem/1.2 inherit; text-transform:uppercase; }
        .team-color-picker { width:94px; height:30px; flex:none; padding:0 .35rem; border:1px solid var(--line); border-radius:8px; background:var(--card); color:var(--ink); font-size:.7rem; }
        .team-roster-name:focus { border-color:var(--blue); }
        .team-roster-count { color:var(--muted); font-size:.75rem; font-weight:700; white-space:nowrap; }
        .team-roster .player { margin:0; border-width:1px 0 0; border-radius:0; }
        .team-player-grid { display:block; }
        .team-player-grid .waiting-player { min-width:0; border-top:1px solid var(--line); }
        .team-stats-head { display:grid; grid-template-columns:minmax(0,1fr) 3rem 3.2rem 3.4rem 3.3rem 5rem; gap:.2rem; padding:.42rem .8rem; border-top:1px solid var(--line); color:var(--muted); font-size:.58rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
        .team-stats-head span:not(:first-child),.team-player-stats span { text-align:center; }
        .team-player-grid .waiting-player { display:grid; grid-template-columns:minmax(0,1fr) 3rem 3.2rem 3.4rem 3.3rem 5rem; gap:.2rem; }
        .team-player-grid .waiting-avatar { grid-column:1; grid-row:1; background:var(--team-color,#458fe0)!important; }
        .team-player-grid .waiting-name { grid-column:1; grid-row:1; margin-left:2.35rem; }
        .team-player-grid .waiting-name small { display:block; margin-top:.12rem; color:var(--muted); font-size:.62rem; font-weight:650; }
        .team-player-grid .waiting-player.completed-player { background:linear-gradient(90deg,rgb(22 163 74 / .24),rgb(34 197 94 / .08)); border-left:3px solid #22c55e; }
        .team-player-grid .availability-pill.completed { background:#16a34a; color:#fff; }
        .team-player-stats { display:contents; }
        #waiting > .match-history { display:none; }
        .active-game-limit { display:flex; align-items:end; justify-content:space-between; gap:.75rem; margin-bottom:.2rem; padding:.8rem .9rem; border:1px solid var(--line); border-radius:12px; background:var(--card); }
        .active-game-limit label { margin:0; }
        .active-game-limit input { width:5.25rem; height:38px; }
        .match-plan { display:flex; flex-wrap:wrap; gap:.35rem 1rem; margin-top:.45rem; color:var(--muted); font-size:.75rem; }
        .match-plan strong { color:var(--ink); }
        @media (max-width:700px) { .team-rosters { grid-template-columns:1fr; } }
        .team-picker { max-width:9rem; padding:.4rem; border:1px solid var(--line); border-radius:8px; background:var(--card); color:var(--ink); font:inherit; font-size:.73rem; }
        .session-toolbar { display:flex; gap:.6rem; margin-bottom:1rem; }
        .session-toolbar button { flex:1; padding:.7rem; border:1px solid var(--line); border-radius:10px; background:#fff; cursor:pointer; font-weight:700; }
        .session-toolbar .standings-button { border-color:#cfe8d8; background:#effbf3; color:#16834a; }
        .session-toolbar .end-session-button { border-color:#f3d0ce; background:#fff5f4; color:#cf3530; }
        .status { display:flex; align-items:center; gap:.5rem; margin:-.55rem 0 1rem 2.75rem; color:#0b8b58; font-size:.78rem; }
        .status:before { content:""; width:7px; height:7px; border-radius:50%; background:#10b981; }
        .court-grid { display:grid; gap:.8rem; }
        .match-card { padding:1rem; }
        .match-card.playing { position:relative; border-color:#d8eee0; }
        .match-card.playing:before { content:""; position:absolute; top:0; bottom:0; left:-1px; width:4px; border-radius:15px 0 0 15px; background:#20b76d; }
        .match-card.open-match { cursor:pointer; }
        .match-card.open-match:focus-visible { outline:3px solid rgb(22 115 209 / .25); outline-offset:2px; }
        .match-head { display:flex; justify-content:space-between; color:var(--muted); font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .match-head strong { color:var(--ink); font-size:1rem; letter-spacing:-.02em; text-transform:none; }
        .court-live { display:flex; align-items:center; gap:.45rem; }
        .court-alert { color:#8793a6; font-size:.9rem; line-height:1; }
        .match-timer { display:inline-flex; align-items:center; gap:.28rem; padding:.24rem .48rem; border-radius:99px; background:#edf9f1; color:#16834a; font-size:.66rem; font-weight:850; letter-spacing:0; text-transform:none; }
        .match-timer:before { content:""; width:6px; height:6px; border-radius:50%; background:#22b968; box-shadow:0 0 0 3px rgb(34 185 104 / .1); }
        .match-timer.complete { background:#f1f3f6; color:#667186; }
        .match-timer.complete:before { background:#8d98a9; box-shadow:none; }
        .live-team-actions { display:grid; grid-template-columns:1fr 1fr; gap:.55rem; margin-top:.15rem; }
        .live-team-winner { overflow:hidden; padding:0; border:0; border-radius:10px; cursor:pointer; color:#fff; text-align:left; box-shadow:0 4px 10px rgb(30 41 59 / .1); }
        .live-team-winner.team-a { background:linear-gradient(135deg,#2787df,#176bc2); }
        .live-team-winner.team-b { background:linear-gradient(135deg,#ff9418,#ea6f00); }
        .live-team-label { display:block; padding:.38rem .55rem; border-bottom:1px solid rgb(255 255 255 / .2); font-size:.58rem; font-weight:850; letter-spacing:.09em; text-transform:uppercase; }
        .live-team-names { display:block; min-height:37px; padding:.42rem .55rem .2rem; overflow:hidden; font-size:.66rem; font-weight:700; text-overflow:ellipsis; white-space:nowrap; }
        .live-team-win { display:block; margin:.15rem; padding:.42rem; border-radius:7px; background:rgb(255 255 255 / .18); font-size:.68rem; font-weight:850; text-align:center; }
        .live-team-winner:hover { filter:saturate(1.08) brightness(1.03); transform:translateY(-1px); }
        .court-preview { position:relative; display:grid; grid-template-columns:repeat(4,1fr); min-height:104px; margin:.8rem 0; overflow:hidden; border:1px solid #83c292; border-radius:10px; background:linear-gradient(90deg,#dbefdf 0 25%,#c9e4ce 25% 50%,#c1dfc7 50% 75%,#dbefdf 75%); }
        .court-preview:before { content:""; position:absolute; z-index:1; inset:0; background:linear-gradient(90deg,transparent calc(25% - .5px),rgb(255 255 255 / .55) 25%,transparent calc(25% + .5px),transparent calc(50% - .5px),rgb(63 119 76 / .28) 50%,transparent calc(50% + .5px),transparent calc(75% - .5px),rgb(255 255 255 / .55) 75%,transparent calc(75% + .5px)),linear-gradient(0deg,transparent calc(50% - .5px),rgb(255 255 255 / .5) 50%,transparent calc(50% + .5px)); pointer-events:none; }
        .court-preview:after { content:""; position:absolute; z-index:1; top:50%; left:50%; width:1px; height:100%; background:rgb(58 108 69 / .34); transform:translate(-50%,-50%); pointer-events:none; }
        .court-slot { position:relative; z-index:2; display:grid; align-content:center; justify-items:center; min-width:0; padding:.3rem .12rem; text-align:center; }
        .court-slot-rating { min-height:.9rem; color:#596779; font-size:.61rem; font-weight:750; }
        .court-avatar { display:grid; width:30px; height:30px; margin:.08rem 0 .15rem; place-items:center; border:2px solid rgb(255 255 255 / .82); border-radius:50%; background:var(--player-team-color,#ef5350); color:#fff; font-size:.65rem; font-weight:850; box-shadow:0 2px 5px rgb(31 70 40 / .18); }
        .court-player-name { width:100%; overflow:hidden; color:#314238; font-size:.62rem; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
        .court-preview.empty .court-slot { visibility:hidden; }
        .stage { width:100%; padding:.7rem; border:0; border-radius:9px; background:var(--blue); color:#fff; cursor:pointer; font-weight:750; }
        .stage:disabled { background:var(--disabled); cursor:not-allowed; }
        .empty-court-actions { display:grid; gap:.15rem; text-align:center; }
        .manual-pick,.remove-court { padding:.45rem; border:0; background:none; cursor:pointer; font-size:.7rem; font-weight:700; }
        .manual-pick { color:var(--blue); }
        .remove-court { color:#dc3535; }
        .remove-court:disabled { color:#b9c0ca; cursor:not-allowed; }
        .add-court { width:100%; margin-top:.8rem; padding:.85rem; border:1px dashed #bfc8d5; border-radius:12px; background:transparent; color:var(--blue); cursor:pointer; font-weight:750; }
        .add-court:hover { border-color:#7caee1; background:#f2f8ff; }
        .add-court:disabled { border-color:#d7dce4; color:#9ca6b5; cursor:not-allowed; background:transparent; }
        .up-next { margin-top:1.1rem; }
        .up-next-head { display:flex; align-items:center; justify-content:space-between; gap:.75rem; margin-bottom:.55rem; }
        .up-next-head h3 { margin:0; color:#566174; font-size:.68rem; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
        .up-next-add { padding:.48rem .65rem; border:0; border-radius:8px; background:#eaf3ff; color:var(--blue); cursor:pointer; font-size:.7rem; font-weight:750; }
        .up-next-add:disabled { background:#edf0f4; color:#9aa5b4; cursor:not-allowed; }
        .up-next-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.7rem; }
        @media (max-width:700px) { .up-next-grid { grid-template-columns:1fr; } }
        .up-next-card { overflow:hidden; border:1px solid #a7edc3; border-radius:12px; background:#f5fff8; }
        .up-next-card-head { display:flex; align-items:center; justify-content:space-between; padding:.45rem .65rem; color:#16834a; font-size:.66rem; font-weight:850; }
        .remove-up-next { padding:0; border:0; background:none; color:#779184; cursor:pointer; font-size:.95rem; line-height:1; }
        .up-next-teams { display:grid; grid-template-columns:1fr 1fr; }
        .up-next-team { padding:.4rem .55rem; }
        .up-next-team:first-child { background:#e5f2ff; border-right:1px solid #b7d9f9; }
        .up-next-team:last-child { background:#fff3df; }
        .up-next-team label { display:block; margin-bottom:.22rem; color:#397cc0; font-size:.55rem; font-weight:850; letter-spacing:.08em; }
        .up-next-team:last-child label { color:#ce7b11; }
        .up-next-player { display:flex; align-items:center; justify-content:space-between; gap:.3rem; min-height:27px; color:#1d3147; font-size:.72rem; font-weight:700; }
        .up-next-player button { width:20px; height:20px; padding:0; border:0; border-radius:5px; background:transparent; color:#94a0ae; cursor:pointer; }
        .up-next-player button:hover { background:#fff; color:#d5534d; }
        .up-next-actions { display:flex; gap:.4rem; padding:.5rem; }
        .up-next-actions button { flex:1; padding:.45rem; border:0; border-radius:7px; cursor:pointer; font-size:.68rem; font-weight:800; }
        .send-up-next { background:#18a657; color:#fff; }.edit-up-next { background:#e8edf3; color:#5e697a; }
        .match-card > .next-queue-actions { display:none; }
        .match-card.building { position:relative; border-color:#f4cc7a; }
        .match-card.building:before { content:""; position:absolute; top:0; bottom:0; left:-1px; width:4px; border-radius:15px 0 0 15px; background:#efa615; }
        .manual-slot { border:0; background:none; cursor:pointer; font:inherit; }
        .empty-slot-circle { display:grid; width:30px; height:30px; margin:.08rem 0 .15rem; place-items:center; border:1.5px dashed #5f9270; border-radius:50%; background:rgb(255 255 255 / .55); color:#4e7e5d; font-size:1rem; }
        .empty-slot-label { color:#718879; font-size:.58rem; font-weight:700; }
        .building-actions { display:grid; grid-template-columns:minmax(0,1fr) auto auto; align-items:center; gap:.5rem; }
        .autofill { padding:.65rem; border:0; border-radius:9px; background:var(--blue); color:#fff; cursor:pointer; font-weight:750; }
        .autofill:disabled { background:var(--disabled); }
        .slots-needed { color:#7c8798; font-size:.68rem; white-space:nowrap; }
        .court-actions { display:grid; grid-template-columns:minmax(0,1fr) auto auto auto; gap:.5rem; }
        .court-actions button { padding:.65rem; border:0; border-radius:9px; cursor:pointer; font-weight:700; }
        .start-match { background:var(--blue); color:#fff; }
        .shuffle { background:#f1f3f8; color:#586274; }
        .call-players { min-width:40px; background:#edf3ff; color:#276fbe; font-size:.9rem; }
        .unstage { background:#f1f3f8; color:#586274; }
        .teams { display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:.65rem; margin:1rem 0; text-align:center; }
        .team { display:grid; gap:.35rem; }
        .team span { padding:.5rem; border-radius:8px; background:var(--soft); font-size:.84rem; font-weight:650; }
        .vs { color:#98a2b1; font-size:.7rem; font-weight:800; }
        .finish { width:100%; padding:.65rem; border:1px solid #b8d5f5; border-radius:9px; background:#edf6ff; color:var(--blue); cursor:pointer; font-weight:750; }
        .waiting { margin-top:1rem; color:var(--muted); font-size:.78rem; }
        .waiting-head { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:0 .2rem .45rem; }
        .waiting-title { color:#566174; font-size:.68rem; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
        .stage-all { border:0; background:none; color:var(--blue); cursor:pointer; font-size:.72rem; font-weight:700; }
        .waiting-columns { display:grid; grid-template-columns:1fr 3.5rem 4.1rem; gap:.35rem; padding:0 .7rem .28rem; color:#8994a6; font-size:.58rem; font-weight:800; letter-spacing:.16em; text-align:center; text-transform:uppercase; }
        .waiting-columns span:first-child { text-align:right; }
        .waiting-list { overflow:hidden; border:1px solid #e0e5ec; border-radius:11px; background:#fff; }
        .waiting-player { display:grid; grid-template-columns:31px minmax(0,1fr) 3.5rem 4.1rem; align-items:center; gap:.5rem; min-height:46px; padding:.45rem .7rem; border-bottom:1px solid #e7eaf0; color:var(--ink); }
        button.waiting-player { width:100%; border-top:0; border-right:0; border-left:0; background:#fff; cursor:pointer; text-align:left; }
        button.waiting-player:hover { background:#f8fbff; }
        .waiting-player:last-child { border-bottom:0; }
        .waiting-avatar { display:grid; width:27px; height:27px; place-items:center; border-radius:50%; background:#458fe0; color:#fff; font-size:.62rem; font-weight:850; }
        .waiting-player:nth-child(4n+2) .waiting-avatar { background:#9b51c6; }
        .waiting-player:nth-child(4n+3) .waiting-avatar { background:#d04438; }
        .waiting-player:nth-child(4n+4) .waiting-avatar { background:#20b76d; }
        .waiting-name { min-width:0; overflow:hidden; font-size:.78rem; font-weight:700; text-overflow:ellipsis; white-space:nowrap; }
        .waiting-rating { margin-left:.35rem; font-size:.65rem; font-weight:800; letter-spacing:.02em; }
        .waiting-rating[data-rating="1"] { color:#e11d48; }
        .waiting-rating[data-rating="2"] { color:#ea580c; }
        .waiting-rating[data-rating="3"] { color:#d18a00; }
        .waiting-rating[data-rating="4"] { color:#4f9b16; }
        .waiting-rating[data-rating="5"] { color:#0d9488; }
        .waiting-link { display:block; margin-top:.08rem; color:#718096; font-size:.56rem; font-weight:600; }
        .wait-time,.waiting-record { color:#7e899b; font-size:.67rem; font-weight:700; text-align:center; white-space:nowrap; }
        .waiting-record { padding:.18rem .3rem; border-radius:99px; background:#f2f4f7; }
        .waiting-empty { margin:.5rem 0 0; padding:.85rem; border-radius:10px; background:#eef1f6; text-align:center; }
        .unavailable-section { margin-top:1rem; }
        .unavailable-section .waiting-player { background:#f8f9fb; }
        .availability-pill { padding:.2rem .42rem; border-radius:99px; background:#fff0cf; color:#a86500; font-size:.58rem; font-weight:800; text-align:center; text-transform:capitalize; }
        .availability-pill.away { background:#fde8e7; color:#bd3b35; }
        .match-history { margin-top:1.2rem; }
        .match-history-title { margin:0 0 .45rem .15rem; color:#566174; font-size:.68rem; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
        .match-history-card { overflow:hidden; border:1px solid #dbe1e9; border-radius:11px; background:#fff; }
        .history-match { position:relative; display:grid; grid-template-columns:36px minmax(0,1fr) auto; align-items:center; gap:.55rem; min-height:58px; padding:.5rem .7rem .5rem .85rem; border-bottom:1px solid #e7eaf0; background:linear-gradient(90deg,#f8fffa,#fff 38%); }
        .history-match:before { content:""; position:absolute; top:.45rem; bottom:.45rem; left:0; width:4px; border-radius:0 5px 5px 0; background:#31b66d; }
        .history-match.no-winner-row { background:linear-gradient(90deg,#fffaf0,#fff 38%); }
        .history-match.no-winner-row:before { background:#f2a51a; }
        .history-match:last-child { border-bottom:0; }
        .history-round { padding:.3rem .35rem; border-radius:7px; background:#eaf3ff; color:#3277c8; font-size:.61rem; font-weight:850; text-align:center; }
        .no-winner-row .history-round { background:#fff0cf; color:#b76b00; }
        .history-teams { min-width:0; font-size:.72rem; line-height:1.45; }
        .history-teams span { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .history-teams .winner { color:#16834a; font-weight:850; }
        .history-teams .winner:before { content:"★ "; color:#f3a712; }
        .history-score { display:grid; gap:.08rem; min-width:1.25rem; color:#6f7b8d; font-size:.72rem; font-weight:750; text-align:right; }
        .history-score .winning-score { color:#16834a; }
        .history-duration { margin-top:.18rem; color:#7e8a9d; font-size:.57rem; font-weight:750; white-space:nowrap; }
        .history-duration:before { content:"◷ "; color:#4d8ed8; }
        .winner-mark { display:grid; min-width:25px; height:25px; place-items:center; border-radius:50%; background:#daf5e4; color:#128146; font-size:.68rem; font-weight:900; box-shadow:0 2px 6px rgb(22 131 74 / .15); }
        .loser-mark { color:#a4adba; text-align:center; }
        .no-winner-label { padding:.27rem .5rem; border-radius:99px; background:#fff0cf; color:#aa6500; font-size:.6rem; font-weight:850; white-space:nowrap; }
        .match-detail-content { width:min(100%,720px); margin-inline:auto; }
        .match-detail-head { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1rem; }
        .match-detail-head h2 { margin:0; font-size:1.6rem; letter-spacing:-.04em; }
        .match-detail-head p { margin:.1rem 0 0; color:var(--muted); font-size:.75rem; }
        .match-cancel { padding:.35rem 0; border:0; background:none; color:#596579; cursor:pointer; font-size:.78rem; }
        .match-clock { margin:.2rem 0 0; text-align:center; font-size:2.75rem; font-weight:850; letter-spacing:-.05em; font-variant-numeric:tabular-nums; }
        .match-target { margin:.1rem 0 1rem; color:var(--muted); text-align:center; font-size:.72rem; }
        .score-team { position:relative; width:100%; min-height:125px; overflow:hidden; padding:1rem .75rem; border:1px solid #dce2ea; border-radius:16px; background:#fff; cursor:pointer; isolation:isolate; transition:border-color .2s,box-shadow .2s,transform .12s; }
        .score-team:active { transform:scale(.992); }
        .score-team.leading { border:2px solid #5d9df2; }
        .score-team strong { display:block; font-size:1.05rem; }
        .score-number { position:relative; z-index:3; display:block; margin-top:.2rem; font-size:4rem; font-weight:650; line-height:1; font-variant-numeric:tabular-nums; }
        .shared-court-stage { min-height:180px; display:grid; place-items:center; margin:-.35rem 0 .15rem; }
        .score-court { position:relative; display:block; width:min(94%,520px); height:155px; overflow:visible; border:4px solid #e6fff0; border-radius:10px; background:linear-gradient(90deg,#43a777 0 31%,#e58a55 31% 69%,#4db181 69%); box-shadow:0 8px 0 #286e50,0 13px 22px rgb(15 70 45 / .25),inset 0 0 0 2px rgb(255 255 255 / .18); transition:filter .2s,box-shadow .2s; }
        .score-court:before { content:""; position:absolute; top:0; bottom:0; left:50%; width:2px; background:rgb(255 255 255 / .88); box-shadow:-98px 0 rgb(255 255 255 / .72),98px 0 rgb(255 255 255 / .72); transform:translateX(-50%); }
        .score-court:after { content:""; position:absolute; z-index:2; top:-4px; bottom:-4px; left:50%; width:5px; border-radius:3px; background:repeating-linear-gradient(0deg,#fff 0 3px,#9cabb8 3px 5px); box-shadow:-5px 0 0 #263d51,5px 0 0 #263d51,2px 2px 5px rgb(15 23 42 / .32); transform:translateX(-50%); }
        .score-scene-player { --player-color:#2189dd; position:absolute; z-index:4; display:grid; width:24px; height:31px; place-items:center; border:2px solid rgb(255 255 255 / .8); border-radius:2px; background:var(--player-color); color:#fff; font-size:.52rem; font-weight:950; line-height:1; box-shadow:inset -5px -4px rgb(0 0 0 / .14),0 6px 5px rgb(15 23 42 / .3); transform:translateY(0); transform-origin:center bottom; }
        .score-scene-player:before { content:""; position:absolute; top:-16px; left:2px; width:14px; height:14px; border:2px solid #fff; border-radius:2px; background:linear-gradient(135deg,#553825 0 27%,#e3a474 28%); box-shadow:inset -3px -2px rgb(79 44 27 / .2); }
        .score-scene-player:after { content:""; position:absolute; bottom:-11px; left:1px; width:7px; height:11px; background:#24384e; box-shadow:11px 0 #24384e; }
        .score-scene-player.team-a-player:nth-child(1) { top:27px; left:15%; }
        .score-scene-player.team-a-player:nth-child(2) { top:82px; left:34%; }
        .score-scene-player.team-b-player:nth-child(3) { top:27px; right:34%; }
        .score-scene-player.team-b-player:nth-child(4) { top:82px; right:15%; }
        .score-court-ball { position:absolute; z-index:5; top:70px; left:calc(50% - 5px); width:11px; height:11px; border:2px solid #e4a700; border-radius:50%; background:#f8d837; box-shadow:2px 3px 3px rgb(92 72 0 / .3); transform:translate(0,0); }
        .score-cheer { position:absolute; z-index:5; top:44%; left:50%; padding:.35rem .65rem; border-radius:99px; background:#fff; color:#176fc5; font-size:.82rem; font-weight:900; opacity:0; pointer-events:none; box-shadow:0 6px 18px rgb(15 23 42 / .2); transform:translate(-50%,-15%) scale(.6); }
        .score-team.celebrate { border-color:#f5bb22; box-shadow:0 0 0 4px rgb(245 187 34 / .17),0 12px 28px rgb(30 100 65 / .16); }
        .score-court.celebrate-a { filter:saturate(1.15) brightness(1.05); box-shadow:0 12px 0 #27684c,0 20px 30px rgb(33 137 221 / .32),inset 0 0 0 3px rgb(93 157 242 / .35); }
        .score-court.celebrate-b { filter:saturate(1.15) brightness(1.05); box-shadow:0 12px 0 #27684c,0 20px 30px rgb(239 123 23 / .32),inset 0 0 0 3px rgb(239 123 23 / .35); }
        .score-court.celebrate-a .team-a-player,.score-court.celebrate-b .team-b-player { animation:player-jump .58s cubic-bezier(.2,.8,.3,1); }
        .score-court.celebrate-a .team-a-player:nth-child(2),.score-court.celebrate-b .team-b-player:nth-child(4) { animation-delay:.08s; }
        .score-court.celebrate-a .score-court-ball { animation:ball-to-team-a .78s ease-out; }
        .score-court.celebrate-b .score-court-ball { animation:ball-to-team-b .78s ease-out; }
        .score-team.celebrate .score-cheer { animation:score-cheer .75s ease-out; }
        @keyframes player-jump { 0%,100%{transform:translateY(0) scale(1)} 38%{transform:translateY(-24px) scale(1.1)} 64%{transform:translateY(-10px) scale(1.04)} }
        @keyframes ball-to-team-a { 0%{transform:translate(0,0) scale(1)} 28%{transform:translate(-35px,-28px) scale(1.25)} 52%{transform:translate(-70px,2px) scale(1)} 73%{transform:translate(-92px,-15px) scale(1.12)} 100%{transform:translate(-112px,2px) scale(1)} }
        @keyframes ball-to-team-b { 0%{transform:translate(0,0) scale(1)} 28%{transform:translate(35px,-28px) scale(1.25)} 52%{transform:translate(70px,2px) scale(1)} 73%{transform:translate(92px,-15px) scale(1.12)} 100%{transform:translate(112px,2px) scale(1)} }
        @keyframes score-cheer { 0%{opacity:0;transform:translate(-50%,10%) scale(.6)} 30%{opacity:1;transform:translate(-50%,-55%) scale(1.08)} 75%{opacity:1} 100%{opacity:0;transform:translate(-50%,-105%) scale(.9)} }
        .scoreboard-grid { position:relative; display:grid; grid-template-columns:1fr 1fr; gap:.8rem; }
        .scoreboard-grid:after { content:"VS"; position:absolute; z-index:6; top:50%; left:50%; display:grid; width:30px; height:30px; place-items:center; border:1px solid #dce2ea; border-radius:50%; background:var(--bg); color:#8792a3; font-size:.58rem; font-weight:850; transform:translate(-50%,-50%); }
        .score-hint { margin:.65rem 0 0; color:#8a95a6; text-align:center; font-size:.65rem; }
        .match-detail-actions { display:grid; grid-template-columns:1fr 1.25fr; gap:.65rem; margin-top:1rem; padding-top:.65rem; border-top:1px solid #e4e8ee; }
        .match-detail-actions button { min-height:50px; border:0; border-radius:12px; cursor:pointer; font-weight:750; }
        .end-unrecorded { background:transparent; color:#5e697a; }
        .end-recorded { background:var(--blue); color:#fff; }
        @media (prefers-reduced-motion:reduce) { .score-team,.score-court,.score-court .score-scene-player,.score-court .score-court-ball,.score-team.celebrate .score-cheer { animation:none!important; transition:none!important; } .score-team.celebrate .score-cheer { opacity:1; transform:translate(-50%,-50%); } }
        .end-dialog { position:fixed; z-index:50; inset:0; display:flex; align-items:center; justify-content:center; padding:1rem; background:rgb(15 18 24 / .48); }
        .end-dialog.selector { align-items:flex-end; padding:0; }
        .end-dialog-panel { width:min(100%,320px); overflow:hidden; border-radius:26px; background:rgb(43 43 46 / .96); color:#fff; box-shadow:0 18px 50px rgb(0 0 0 / .3); }
        .end-dialog.selector .end-dialog-panel { width:min(100%,720px); border-radius:18px 18px 0 0; background:#fff; color:var(--ink); }
        .end-dialog-copy { padding:1.15rem 1.25rem .85rem; }
        .end-dialog-copy h3 { margin:0 0 .35rem; font-size:1rem; }
        .end-dialog-copy p { margin:0; color:#c6c7cb; font-size:.78rem; line-height:1.4; }
        .end-dialog.selector .end-dialog-copy { border-bottom:1px solid #e5e8ed; }
        .end-dialog.selector .end-dialog-copy p { color:var(--muted); }
        .confirm-actions { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; padding:0 .85rem .9rem; }
        .confirm-actions button { min-height:44px; border:0; border-radius:99px; background:#5a5a5d; color:#fff; cursor:pointer; font-weight:750; }
        .winner-options button { width:100%; padding:1rem 1.2rem; border:0; border-bottom:1px solid #e7eaf0; background:#fff; color:var(--ink); cursor:pointer; text-align:left; }
        .winner-options .no-winner { color:#dc3535; }
        .winner-cancel-wrap { padding:.55rem 1.15rem calc(.8rem + env(safe-area-inset-bottom)); }
        .winner-cancel { width:100%; min-height:44px; border:0; border-radius:11px; background:#f1f3f8; cursor:pointer; font-weight:750; }
        .player-picker { position:fixed; z-index:55; inset:0; display:flex; align-items:flex-end; justify-content:center; background:rgb(15 18 24 / .48); }
        .player-picker-sheet { width:min(100%,720px); max-height:88dvh; overflow:auto; border-radius:18px 18px 0 0; background:#fff; box-shadow:0 -12px 35px rgb(0 0 0 / .18); }
        .player-picker-head { position:sticky; z-index:2; top:0; padding:1rem 1.15rem .7rem; border-bottom:1px solid #e7eaf0; background:#fff; }
        .player-picker-head h3 { margin:0 0 .15rem; font-size:1rem; }
        .player-picker-head p { margin:0; color:var(--muted); font-size:.72rem; }
        .clear-manual-slot { width:100%; padding:.8rem 1.15rem; border:0; border-bottom:1px solid #e7eaf0; background:#fff8f7; color:#dc3535; cursor:pointer; text-align:left; font-weight:750; }
        .picker-label { display:block; padding:.7rem 1.15rem .35rem; color:#697588; font-size:.62rem; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
        .picker-player { display:grid; grid-template-columns:32px minmax(0,1fr) auto; align-items:center; gap:.65rem; width:100%; min-height:53px; padding:.5rem 1.15rem; border:0; border-bottom:1px solid #e7eaf0; background:#fff; cursor:pointer; text-align:left; }
        .picker-player .waiting-avatar { width:29px; height:29px; }
        .picker-player strong { display:block; overflow:hidden; font-size:.78rem; text-overflow:ellipsis; white-space:nowrap; }
        .picker-player small { color:var(--muted); font-size:.64rem; }
        .picker-wait { color:#687487; font-size:.7rem; }
        .picker-cancel-wrap { position:sticky; bottom:0; padding:.55rem 1.15rem calc(.8rem + env(safe-area-inset-bottom)); background:#fff; }
        .player-action-sheet { position:fixed; z-index:56; inset:0; display:flex; align-items:flex-end; justify-content:center; background:rgb(15 18 24 / .48); }
        .player-action-panel { width:min(100%,720px); max-height:88dvh; overflow:auto; border-radius:18px 18px 0 0; background:#fff; box-shadow:0 -12px 35px rgb(0 0 0 / .18); }
        .player-action-head { padding:1rem 1.15rem .75rem; border-bottom:1px solid #e7eaf0; }
        .player-action-head h3 { margin:0 0 .15rem; font-size:1rem; }
        .player-action-head p { margin:0; color:var(--muted); font-size:.7rem; }
        .player-action { width:100%; padding:1rem 1.15rem; border:0; border-bottom:1px solid #e7eaf0; background:#fff; color:var(--ink); cursor:pointer; text-align:left; }
        .player-action.danger { color:#dc3535; }
        .standings-dialog { position:fixed; z-index:58; inset:0; display:flex; align-items:center; justify-content:center; padding:1rem; background:rgb(15 18 24 / .55); }
        .standings-panel { width:min(100%,560px); max-height:88dvh; overflow:auto; border-radius:18px; background:#fff; box-shadow:0 20px 55px rgb(0 0 0 / .25); }
        .standings-panel.team-standings-large { width:min(94vw,860px); }
        .team-standings-large .standings-head { padding:1.35rem 1.5rem 1.1rem; }
        .team-standings-large .standings-head h3 { font-size:1.55rem; }
        .team-standings-large .standings-head p { font-size:.9rem; }
        .team-standings-large .summary-table-head,.team-standings-large .summary-player { grid-template-columns:4.2rem minmax(0,1fr) 4.3rem 4.3rem 4.8rem 4.8rem; padding:.9rem 1rem; gap:.55rem; }
        .team-standings-large .summary-table-head { font-size:.72rem; }
        .team-standings-large .summary-player { font-size:1.05rem; font-weight:750; }
        .team-standings-large .summary-player small { font-size:.78rem!important; }
        .team-standings-large .standings-back { min-height:58px; font-size:1.05rem; }
        .standings-head { position:sticky; z-index:2; top:0; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1rem 1.15rem .8rem; border-bottom:1px solid #e7eaf0; background:#fff; }
        .standings-head h3 { margin:0 0 .15rem; font-size:1.05rem; }
        .standings-head p { margin:0; color:var(--muted); font-size:.72rem; }
        .standings-close { border:0; background:none; color:#8a95a6; cursor:pointer; font-size:1.25rem; }
        .standings-note { padding:.65rem 1.15rem; background:#f6f8fb; color:#8792a3; font-size:.62rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .standing-row { display:grid; grid-template-columns:30px minmax(0,1fr) 48px; align-items:center; gap:.55rem; min-height:72px; padding:.65rem 1.15rem; border-bottom:1px solid #e7eaf0; }
        .standing-row.top-1 { background:linear-gradient(90deg,#fff8d9,#fff 55%); box-shadow:inset 4px 0 #f2bc24; }
        .standing-row.top-2 { background:linear-gradient(90deg,#f1f5f9,#fff 55%); box-shadow:inset 4px 0 #9caabd; }
        .standing-row.top-3 { background:linear-gradient(90deg,#fff0e5,#fff 55%); box-shadow:inset 4px 0 #cf7c43; }
        .standing-rank { color:#9aa4b2; font-size:.72rem; font-weight:800; text-align:center; }
        .standing-rank.qualified { color:#fff; width:25px; height:25px; display:grid; place-items:center; border-radius:50%; background:#357fd3; }
        .standing-rank.leader { display:grid; width:26px; height:26px; place-items:center; border-radius:50%; color:#fff; font-size:.7rem; box-shadow:0 2px 7px rgb(30 41 59 / .16); }
        .top-1 .standing-rank.leader { background:linear-gradient(135deg,#f8cf49,#d99a00); }
        .top-2 .standing-rank.leader { background:linear-gradient(135deg,#cbd5e1,#7f8ea3); }
        .top-3 .standing-rank.leader { background:linear-gradient(135deg,#e8a16f,#ad5b28); }
        .standing-name { display:flex; align-items:center; gap:.35rem; font-size:.8rem; font-weight:750; }
        .standing-stars { color:#e5a000; font-size:.65rem; letter-spacing:.01em; }
        .standing-status { padding:.18rem .35rem; border-radius:4px; background:#dff7e7; color:#169253; font-size:.56rem; font-weight:700; }
        .standing-status.resting { background:#fff0cf; color:#aa6500; }
        .standing-status.away { background:#fde8e7; color:#bd3b35; }
        .standing-meta { margin-top:.15rem; color:#8792a2; font-size:.65rem; line-height:1.35; }
        .standing-wins { color:#16a05a; font-size:1rem; font-weight:850; text-align:center; }
        .standing-wins small { display:block; margin-top:.05rem; color:#a0a9b6; font-size:.52rem; font-weight:700; text-transform:uppercase; }
        .standings-actions { position:sticky; bottom:0; padding:.65rem 1.15rem calc(.8rem + env(safe-area-inset-bottom)); background:#fff; }
        .standings-back { width:100%; min-height:48px; border:0; border-radius:11px; background:#eef1f5; color:#344054; cursor:pointer; font-weight:750; }
        .queue-stats { margin-top:1rem; padding:1.15rem; border:1px solid var(--line); border-radius:15px; background:#fff; box-shadow:0 3px 12px rgb(25 39 64 / .05); }
        .queue-stats h3 { margin:0 0 1rem; color:#738096; font-size:.65rem; letter-spacing:.16em; text-transform:uppercase; }
        .queue-stat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; text-align:center; }
        .queue-stat-grid strong { display:block; font-size:1.65rem; line-height:1; }
        .queue-stat-grid span { display:block; margin-top:.35rem; color:#7d889b; font-size:.68rem; }
        .summary-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; }
        .summary-header h2 { margin:0; font-size:1.7rem; letter-spacing:-.04em; }
        .summary-block-title { margin:1.35rem 0 .5rem; color:#596578; font-size:.65rem; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
        .summary-name { margin:0; font-size:1.25rem; }
        .summary-date { margin:.15rem 0 .8rem; color:var(--muted); font-size:.72rem; }
        .summary-metrics { display:grid; grid-template-columns:repeat(4,1fr); gap:.5rem; }
        .summary-metric { padding:.8rem .35rem; border:1px solid #d9dfe8; border-radius:11px; background:#fff; text-align:center; }
        .summary-metric strong { display:block; font-size:1.05rem; }
        .summary-metric span { display:block; margin-top:.2rem; color:#7e899a; font-size:.58rem; }
        .summary-table { overflow:hidden; border:1px solid #dbe1e9; border-radius:11px; background:#fff; }
        .summary-table-head,.summary-player { display:grid; grid-template-columns:1.35rem minmax(0,1fr) 2.7rem 1.7rem 1.7rem 3rem; align-items:center; gap:.25rem; padding:.55rem .65rem; }
        .summary-table-head { background:#f6f8fb; color:#758195; font-size:.58rem; font-weight:850; letter-spacing:.08em; text-transform:uppercase; }
        .summary-player { border-top:1px solid #e7eaf0; font-size:.7rem; }
        .summary-player strong { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .summary-player span,.summary-table-head span { text-align:center; }
        #waiting .summary-table .summary-table-head,#waiting .summary-table .summary-player { grid-template-columns:minmax(0,1fr) 3rem 3.2rem 3.4rem 5rem; }
        .summary-player-number { color:#8b96a7; font-weight:800; }
        .summary-rating { margin-left:.25rem; color:#d49400; font-size:.57rem; }
        .summary-done-wrap { position:sticky; bottom:0; margin:1.5rem -1.25rem -6rem; padding:.65rem 1.25rem calc(.8rem + env(safe-area-inset-bottom)); border-top:1px solid #e1e5ec; background:rgb(246 247 251 / .96); }
        .summary-done { width:100%; min-height:50px; border:0; border-radius:12px; background:var(--blue); color:#fff; cursor:pointer; font-weight:750; }
        html[data-theme="dark"] { color-scheme:dark; --ink:#edf2f8; --muted:#9ba8bb; --line:#374151; --bg:#111827; --card:#182231; --soft:#253142; --disabled:#4b5563; }
        html[data-theme="dark"] body { background:#080d14; }
        html[data-theme="dark"] .app,html[data-theme="dark"] .screen { background:var(--bg); }
        html[data-theme="dark"] .theme-toggle,html[data-theme="dark"] .session-card,html[data-theme="dark"] .form-card,html[data-theme="dark"] .match-card,html[data-theme="dark"] .player,html[data-theme="dark"] .field,html[data-theme="dark"] .option,html[data-theme="dark"] .circle-button,html[data-theme="dark"] .pill,html[data-theme="dark"] .session-toolbar button,html[data-theme="dark"] .waiting-list,html[data-theme="dark"] button.waiting-player,html[data-theme="dark"] .match-history-card,html[data-theme="dark"] .queue-stats,html[data-theme="dark"] .summary-metric,html[data-theme="dark"] .summary-table,html[data-theme="dark"] .score-team { background:#182231; color:var(--ink); }
        html[data-theme="dark"] .sticky-action,html[data-theme="dark"] .bottom-nav,html[data-theme="dark"] .summary-done-wrap { border-color:#374151; background:rgb(17 24 39 / .94); }
        html[data-theme="dark"] .rating-guide-sheet,html[data-theme="dark"] .rating-guide-actions,html[data-theme="dark"] .player-picker-sheet,html[data-theme="dark"] .player-picker-head,html[data-theme="dark"] .picker-cancel-wrap,html[data-theme="dark"] .player-action-panel,html[data-theme="dark"] .player-action-head,html[data-theme="dark"] .player-action,html[data-theme="dark"] .standings-panel,html[data-theme="dark"] .standings-head,html[data-theme="dark"] .standings-actions,html[data-theme="dark"] .end-dialog.selector .end-dialog-panel,html[data-theme="dark"] .winner-options button { background:#182231; color:var(--ink); border-color:#374151; }
        html[data-theme="dark"] .waiting-player,html[data-theme="dark"] .history-match,html[data-theme="dark"] .standing-row,html[data-theme="dark"] .summary-player { border-color:#374151; }
        html[data-theme="dark"] .waiting-player:hover,html[data-theme="dark"] .unavailable-section .waiting-player,html[data-theme="dark"] .summary-table-head,html[data-theme="dark"] .standings-note { background:#202c3c; }
        html[data-theme="dark"] .standing-row.top-1 { background:linear-gradient(90deg,#3c3212,#182231 58%); }
        html[data-theme="dark"] .standing-row.top-2 { background:linear-gradient(90deg,#293342,#182231 58%); }
        html[data-theme="dark"] .standing-row.top-3 { background:linear-gradient(90deg,#3a281d,#182231 58%); }
        html[data-theme="dark"] .history-match { background:linear-gradient(90deg,#183326,#182231 42%); }
        html[data-theme="dark"] .history-match.no-winner-row { background:linear-gradient(90deg,#392d14,#182231 42%); }
        html[data-theme="dark"] .up-next-player { color:#1d3147; }
        html[data-theme="dark"] textarea,html[data-theme="dark"] .waiting,html[data-theme="dark"] .waiting-empty { background:#202c3c; color:var(--ink); }
        .bottom-nav { position:fixed; z-index:9; right:0; bottom:0; left:0; display:grid; grid-template-columns:repeat(3,1fr); width:min(100%,720px); margin:auto; padding:.65rem 1rem calc(.65rem + env(safe-area-inset-bottom)); border-top:1px solid #dfe4ec; background:rgb(246 247 251 / .96); backdrop-filter:blur(12px); }
        .nav-button { display:grid; gap:.22rem; place-items:center; border:0; background:none; color:#667186; cursor:pointer; font-size:.72rem; }
        .nav-button.active { color:var(--blue); }
        .nav-icon { font-size:1.1rem; line-height:1; }
        .toast { position:fixed; z-index:20; top:calc(1rem + env(safe-area-inset-top)); left:50%; padding:.7rem 1rem; border-radius:99px; background:#17202c; color:#fff; opacity:0; pointer-events:none; transform:translate(-50%,-10px); transition:.2s; font-size:.8rem; box-shadow:var(--shadow); }
        .toast.show { opacity:1; transform:translate(-50%,0); }
        .rating-guide { position:fixed; z-index:30; inset:0; display:flex; align-items:flex-end; justify-content:center; padding-top:2rem; border:0; background:rgb(15 23 42 / .48); opacity:0; pointer-events:none; transition:opacity .2s ease; }
        .rating-guide.open { opacity:1; pointer-events:auto; }
        .rating-guide-sheet { width:min(100%,720px); max-height:calc(100dvh - 2rem); overflow:auto; border-radius:18px 18px 0 0; background:#fff; box-shadow:0 -12px 36px rgb(15 23 42 / .16); transform:translateY(24px); transition:transform .22s ease; }
        .rating-guide.open .rating-guide-sheet { transform:translateY(0); }
        .rating-guide-head { padding:1rem 1.15rem .75rem; }
        .rating-guide-head h3 { margin:0 0 .25rem; font-size:1.05rem; }
        .rating-guide-head p { margin:0; color:var(--muted); font-size:.78rem; line-height:1.4; }
        .rating-level { display:grid; grid-template-columns:44px 1fr; align-items:start; gap:.75rem; padding:.75rem 1.15rem; border-top:1px solid #e7eaf0; }
        .rating-badge { display:grid; min-height:32px; place-items:center; border-radius:8px; color:#fff; font-weight:800; box-shadow:0 3px 8px rgb(24 32 43 / .12); }
        .rating-level[data-rating="1"] .rating-badge { background:linear-gradient(135deg,#fb7185,#e11d48); }
        .rating-level[data-rating="2"] .rating-badge { background:linear-gradient(135deg,#fb923c,#ea580c); }
        .rating-level[data-rating="3"] .rating-badge { background:linear-gradient(135deg,#facc15,#d97706); }
        .rating-level[data-rating="4"] .rating-badge { background:linear-gradient(135deg,#84cc16,#16a34a); }
        .rating-level[data-rating="5"] .rating-badge { background:linear-gradient(135deg,#2dd4bf,#1684d8); }
        .rating-level strong { display:block; margin-bottom:.12rem; font-size:.87rem; }
        .rating-level p { margin:0; color:var(--muted); font-size:.74rem; line-height:1.35; }
        .rating-guide-actions { position:sticky; bottom:0; padding:.55rem 1.15rem calc(.8rem + env(safe-area-inset-bottom)); border-top:1px solid #e7eaf0; background:#fff; }
        .rating-guide-close { width:100%; min-height:44px; border:0; border-radius:11px; background:#f1f3f8; cursor:pointer; font-weight:750; }
        body.guide-open { overflow:hidden; }
        @media (min-width:760px) { .app { width:min(100%,1440px); box-shadow:0 0 50px rgb(25 39 64 / .12); } .screen { padding-inline:clamp(2.5rem,5vw,5rem); } .content { width:min(100%,1280px); } .match-detail-content { width:min(100%,980px); } .court-grid { grid-template-columns:repeat(2,1fr); } .sticky-action { right:calc((100vw - min(100vw,1440px))/2); left:calc((100vw - min(100vw,1440px))/2); padding-inline:clamp(2.5rem,5vw,5rem); } }
        @media (max-width:370px) { .screen { padding-inline:1rem; } .skills { gap:.25rem; } .skill { font-size:.78rem; } }
    </style>
</head>
<body>
<main class="app">
    <section class="screen active" data-screen="home">
        <div class="content home-content">
            <div class="home-actions"><a class="dashboard-back" href="/admin" aria-label="Back to dashboard">← Back to dashboard</a><button class="theme-toggle" id="theme-toggle" type="button" aria-label="Switch to dark theme">☾</button></div>
            <div class="brand"><span class="ball" aria-hidden="true"></span> PICKLEBALL</div>
            <h1>CaPikol Ballers</h1>
            <p class="subtitle">Open play, organized.</p>
            <button class="primary-card" type="button" data-go="setup"><span><strong>New session</strong><small>Auto-match rotating players across courts</small></span><span class="arrow">→</span></button>
            <div id="queue-stats"></div>
            <div id="recent-session"></div>
            <div id="home-session-history"></div>
            <div style="flex:1"></div>
        </div>
        <nav class="bottom-nav" aria-label="Open play navigation">
            <button class="nav-button active" data-tab="home"><span class="nav-icon">♙</span>Players</button>
            <button class="nav-button" data-tab="history"><span class="nav-icon">◷</span>History</button>
            <button class="nav-button" data-tab="settings"><span class="nav-icon">⚙</span>Settings</button>
        </nav>
    </section>

    <section class="screen" data-screen="setup">
        <div class="content">
            <header class="topbar"><button class="back" data-go="home" aria-label="Back">‹</button><div><h2>New session</h2></div></header>
            <label class="section-label" for="session-name">Session name</label>
            <input class="field" id="session-name" placeholder="Optional (e.g. Wednesday Night)" maxlength="50">
            <span class="section-label">Event dates</span>
            <div class="date-range"><label><span>Start date</span><input class="field" id="event-date" type="date"></label><label><span>End date</span><input class="field" id="event-end-date" type="date"></label></div>
            <span class="section-label">Courts</span>
            <div class="stepper"><button class="circle-button" id="court-minus">−</button><output class="stepper-output" id="court-count">2</output><button class="circle-button" id="court-plus">＋</button></div>
            <span class="section-label">Matchmaking</span>
            <button class="option selected" data-mode="balanced" type="button" role="radio" aria-checked="true"><strong>Balanced</strong><small>Even teams, courts grouped by player rating. Recording a winner is optional.</small><span class="radio" aria-hidden="true"></span></button>
            <button class="option" data-mode="winners" type="button" role="radio" aria-checked="false"><strong>Winners & Losers</strong><small>Winners play winners, losers play losers. A winner is required each game.</small><span class="radio" aria-hidden="true"></span></button>
            <button class="option" data-mode="team" type="button" role="radio" aria-checked="false"><strong>Team Match</strong><small>Set up the session around a fixed number of teams.</small><span class="radio" aria-hidden="true"></span></button>
            <div id="team-count-field" hidden>
                <label class="section-label" for="team-count">Number of teams</label>
                <input class="field" id="team-count" type="number" min="2" max="64" step="1" inputmode="numeric" placeholder="Required for team match">
                <label class="section-label" for="team-size">Players per team</label>
                <input class="field" id="team-size" type="number" min="1" max="64" step="1" inputmode="numeric" placeholder="Required for team match">
            </div>
            <p class="meta">You can switch this any time during the session.</p>
            <span class="section-label">Game format</span>
            <div id="games-per-player-field"><label class="section-label" for="games-per-player">Games required per player</label>
            <input class="field" id="games-per-player" type="number" min="1" max="20" step="1" inputmode="numeric" value="4">
            <p class="hint">Each registered player can play this exact maximum number of games.</p></div>
            <div class="row-between"><div><strong style="font-size:.82rem">Point target</strong><p class="meta">A label for the score players are using.</p></div><button class="toggle on" id="point-toggle" aria-label="Toggle point target"></button></div>
            <div class="pills" style="margin-top:.65rem" id="point-pills"><button class="pill selected">11</button><button class="pill">15</button><button class="pill">21</button></div>
            <div class="row-between" style="margin-top:1.25rem"><div><strong style="font-size:.82rem">Match timer</strong><p class="meta">Optional countdown per match</p></div><button class="toggle" id="timer-toggle" aria-label="Toggle match timer"></button></div>
        </div>
        <div class="sticky-action"><button class="main-button" id="continue-checkin">Continue to check-in</button></div>
    </section>

    <section class="screen" data-screen="checkin">
        <div class="content">
            <header class="topbar"><button class="back" data-go="setup" aria-label="Back">‹</button><div><h2>Check-in</h2><p>Add players before starting</p></div></header>
            <div class="form-card">
                <input class="field" id="player-name" placeholder="Player name" maxlength="40" autocomplete="off">
                <div id="rating-controls"><div class="skill-head"><span class="section-label">Star rating</span><button class="help" id="skill-help">ⓘ What do these mean?</button></div>
                <div class="skills" id="skills"></div></div>
                <button class="add-button" id="add-player" disabled>Add player</button>
                <button class="text-button" id="bulk-toggle">＋ Bulk paste</button>
                <div id="bulk-area" hidden><textarea id="bulk-input" placeholder="One player name per line:&#10;Alex&#10;Jordan&#10;Casey"></textarea><p class="hint" id="bulk-rating-hint">All pasted players will receive the selected <span id="default-skill">4-star</span> rating.</p><p class="hint" id="bulk-team-hint" hidden>Assign each pasted player to a team below.</p><button class="add-button" id="add-list" disabled>Add list</button></div>
            </div>
            <div class="players" id="player-list"></div>
            <div class="team-rosters" id="team-rosters" hidden></div>
            <datalist id="team-color-options"><option value="#e05252" label="Red"></option><option value="#ef8a32" label="Orange"></option><option value="#47a86e" label="Green"></option><option value="#8a6348" label="Brown"></option><option value="#3c82d0" label="Blue"></option><option value="#8564c6" label="Purple"></option><option value="#d66f9e" label="Pink"></option><option value="#319d9d" label="Teal"></option></datalist>
        </div>
        <div class="sticky-action"><button class="main-button" id="start-session" type="button" disabled>Need 4 more ready</button></div>
    </section>

    <section class="screen" data-screen="active">
        <div class="content">
            <header class="topbar"><button class="back" data-go="home" aria-label="Back">‹</button><div><h2 id="active-title">Open play</h2><p id="active-summary"></p></div></header>
            <div class="status">Session in progress</div>
            <div class="session-toolbar"><button id="add-more">＋ Add players</button><button class="standings-button" id="show-standings">🏆 Standings</button><button class="end-session-button" id="end-session">End session</button></div>
            <div class="court-grid" id="matches"></div>
            <button class="add-court" id="add-court" type="button">＋ Add court</button>
            <div id="up-next"></div>
            <div id="waiting"></div>
            <div id="match-history"></div>
        </div>
    </section>

    <section class="screen" data-screen="match-detail">
        <div class="match-detail-content">
            <header class="match-detail-head"><div><h2 id="detail-court">Court</h2><p id="detail-round">Round 1</p></div><button class="match-cancel" id="match-cancel" type="button">Cancel</button></header>
            <div class="match-clock" id="match-clock">0:00</div>
            <p class="match-target" id="match-target">Game to 11</p>
            <div class="shared-court-stage" aria-hidden="true"><span class="score-court" id="match-court-scene"></span></div>
            <div class="scoreboard-grid">
                <button class="score-team" id="score-team-a" type="button"><strong id="team-a-names"></strong><span class="score-number" id="score-a">0</span><span class="score-cheer" aria-hidden="true">+1 ✨</span></button>
                <button class="score-team" id="score-team-b" type="button"><strong id="team-b-names"></strong><span class="score-number" id="score-b">0</span><span class="score-cheer" aria-hidden="true">+1 ✨</span></button>
            </div>
            <p class="score-hint">Tap a team to score · hold to undo</p>
            <div class="match-detail-actions"><button class="end-unrecorded" id="end-unrecorded" type="button">End without recording</button><button class="end-recorded" id="end-recorded" type="button">End match</button></div>
        </div>
    </section>

    <section class="screen" data-screen="session-summary">
        <div class="content">
            <header class="summary-header"><h2>Session summary</h2></header>
            <div id="session-summary-content"></div>
            <div class="summary-done-wrap"><button class="summary-done" id="summary-done" type="button">Done</button></div>
        </div>
    </section>

    <section class="screen" data-screen="history"><div class="content"><header class="topbar"><button class="back" data-go="home">‹</button><div><h2>History</h2><p>Your completed open-play sessions</p></div></header><div id="history-list"></div></div></section>
    <section class="screen" data-screen="settings"><div class="content"><header class="topbar"><button class="back" data-go="home">‹</button><div><h2>Settings</h2><p>CaPikol Ballers preferences</p></div></header><div class="form-card"><div class="row-between"><div><strong>Keep session history</strong><p class="meta">Saved to your account and available across browsers.</p></div><button class="toggle on" id="history-toggle"></button></div><p class="hint" style="margin-top:1rem">Your active tournament, players, courts, results, standings, and preferences are securely stored in the system database.</p><button class="text-button" id="clear-data" style="color:#c2413c;margin-top:2rem">Clear all CaPikol Ballers data</button></div></div></section>
</main>
<div class="toast" id="toast" role="status"></div>
<div class="rating-guide" id="rating-guide" role="dialog" aria-modal="true" aria-labelledby="rating-guide-title" hidden>
    <div class="rating-guide-sheet">
        <div class="rating-guide-head">
            <h3 id="rating-guide-title">Star rating guide</h3>
            <p>A simple self-rating used to create balanced games. Pick the level that feels closest.</p>
        </div>
        <div class="rating-level" data-rating="1"><span class="rating-badge">1 ★</span><div><strong>Newcomer</strong><p>New to pickleball and learning the rules, serving, scoring, and basic shots.</p></div></div>
        <div class="rating-level" data-rating="2"><span class="rating-badge">2 ★</span><div><strong>Beginner</strong><p>Can keep a short rally going and is becoming comfortable with court positioning.</p></div></div>
        <div class="rating-level" data-rating="3"><span class="rating-badge">3 ★</span><div><strong>Intermediate</strong><p>Understands the kitchen rules and can place serves, returns, and basic dinks.</p></div></div>
        <div class="rating-level" data-rating="4"><span class="rating-badge">4 ★</span><div><strong>Advanced</strong><p>Uses consistent placement, pace, and strategy while handling pressure well.</p></div></div>
        <div class="rating-level" data-rating="5"><span class="rating-badge">5 ★</span><div><strong>Expert</strong><p>Tournament-level play with refined shot selection, control, and tactical awareness.</p></div></div>
        <div class="rating-guide-actions"><button class="rating-guide-close" id="rating-guide-close" type="button">Done</button></div>
    </div>
</div>
<div class="end-dialog" id="end-dialog" role="dialog" aria-modal="true" aria-labelledby="end-dialog-title" hidden>
    <div class="end-dialog-panel" id="end-dialog-panel"></div>
</div>
<div class="player-picker" id="player-picker" role="dialog" aria-modal="true" aria-labelledby="player-picker-title" hidden>
    <div class="player-picker-sheet" id="player-picker-sheet"></div>
</div>
<div class="player-action-sheet" id="player-action-sheet" role="dialog" aria-modal="true" aria-labelledby="player-action-title" hidden>
    <div class="player-action-panel" id="player-action-panel"></div>
</div>
<div class="standings-dialog" id="standings-dialog" role="dialog" aria-modal="true" aria-labelledby="standings-title" hidden>
    <div class="standings-panel" id="standings-panel"></div>
</div>

<script>
(() => {
    const initial = { screen:'home', setup:{ name:'', eventDate:'', eventEndDate:'', courts:2, mode:'balanced', teamCount:null, teamSize:4, teamNames:[], teamColors:[], gamesPerPlayer:4, points:11, pointsOn:true, timer:false }, players:[], rating:4, current:null, history:[], keepHistory:true };
    const databaseState = {{ Illuminate\Support\Js::from($openPlayState ?? null) }};
    const offlineState = (()=>{try{return JSON.parse(localStorage.getItem('pb-queue-offline-state')||'null');}catch{return null;}})();
    let state = {...initial,...((navigator.onLine?databaseState:offlineState)||databaseState||offlineState||{})};
    state.setup = { ...initial.setup, ...(state.setup || {}) };
    state.setup.name = typeof state.setup.name === 'string' ? state.setup.name : '';
    state.setup.eventDate = /^\d{4}-\d{2}-\d{2}$/.test(state.setup.eventDate || '') ? state.setup.eventDate : '';
    state.setup.eventEndDate = /^\d{4}-\d{2}-\d{2}$/.test(state.setup.eventEndDate || '') ? state.setup.eventEndDate : '';
    state.setup.teamCount = Number.isInteger(Number(state.setup.teamCount)) && Number(state.setup.teamCount) >= 2 ? Number(state.setup.teamCount) : null;
    state.setup.teamSize = Number.isInteger(Number(state.setup.teamSize)) && Number(state.setup.teamSize) >= 1 ? Number(state.setup.teamSize) : initial.setup.teamSize;
    state.setup.teamNames = Array.isArray(state.setup.teamNames) ? state.setup.teamNames : [];
    state.setup.teamColors = Array.isArray(state.setup.teamColors) ? state.setup.teamColors : [];
    state.setup.gamesPerPlayer = Number.isInteger(Number(state.setup.gamesPerPlayer)) && Number(state.setup.gamesPerPlayer) >= 1 ? Number(state.setup.gamesPerPlayer) : initial.setup.gamesPerPlayer;
    state.setup.courts = Math.min(8, Math.max(1, Number(state.setup.courts) || initial.setup.courts));
    state.players = state.players || []; state.history = state.history || [];
    if(state.current){state.current.upNext=Array.isArray(state.current.upNext)?state.current.upNext:[];state.current.matches?.forEach(match=>{delete match.nextQueue;delete match.nextQueueEnabled;});}
    state.rating = Math.min(5, Math.max(1, Number(state.rating ?? state.skill ?? 4)));
    state.theme = state.theme || (window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');
    document.documentElement.dataset.theme=state.theme;
    let updatingSession = false;
    let openMatchId = null;
    let pickerMatchId = null, pickerSlotIndex = null, pickerWasStaged = false, actionPlayerId = null;
    const $ = (s, root=document) => root.querySelector(s);
    const $$ = (s, root=document) => [...root.querySelectorAll(s)];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let saveTimer;
    const persistOffline = persistedState => localStorage.setItem('pb-queue-offline-state',JSON.stringify(persistedState));
    const syncState = persistedState => fetch({{ Illuminate\Support\Js::from(route('open-play.state.save')) }},{method:'PUT',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({state:persistedState})}).then(response=>{if(!response.ok)throw new Error('Unable to save Open Play');});
    const save = () => {clearTimeout(saveTimer);saveTimer=setTimeout(()=>{const persistedState={...state,history:[]};persistOffline(persistedState);if(!navigator.onLine){toast('Saved on this device · will sync when online');return;}syncState(persistedState).catch(()=>toast('Saved on this device · will sync when online'));},250);};
    window.addEventListener('online',()=>{const persistedState={...state,history:[]};persistOffline(persistedState);syncState(persistedState).then(()=>toast('Open Play synced')).catch(()=>{});});
    if('serviceWorker' in navigator) window.addEventListener('load',()=>navigator.serviceWorker.register('/pwa-sw.js'));
    const archiveSession = session => fetch({{ Illuminate\Support\Js::from(route('open-play.sessions.archive')) }},{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({session})}).then(response=>{if(!response.ok)throw new Error('Unable to archive session');return response.json();}).catch(()=>toast('Session history could not be saved'));
    function renderTheme(){const dark=state.theme==='dark';document.documentElement.dataset.theme=state.theme;$('#theme-toggle').textContent=dark?'☀':'☾';$('#theme-toggle').setAttribute('aria-label',dark?'Switch to light theme':'Switch to dark theme');}
    $('#theme-toggle').onclick=()=>{state.theme=state.theme==='dark'?'light':'dark';renderTheme();save();};
    renderTheme();
    const toast = message => { const el=$('#toast'); el.textContent=message; el.classList.add('show'); clearTimeout(toast.t); toast.t=setTimeout(()=>el.classList.remove('show'),1800); };
    const escapeHtml = value => String(value).replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
    const ago = iso => { const mins=Math.max(1,Math.floor((Date.now()-new Date(iso))/60000)); return mins<60?`${mins}m ago`:mins<1440?`${Math.floor(mins/60)}h ago`:`${Math.floor(mins/1440)}d ago`; };
    function show(name) {
        $$('.screen').forEach(x=>x.classList.toggle('active',x.dataset.screen===name));
        state.screen=name; save(); window.scrollTo(0,0);
        if(name==='home') renderHome(); if(name==='history') renderHistory(); if(name==='active') renderActive(); if(name==='match-detail') renderMatchDetail(); if(name==='session-summary'){renderSessionSummary();renderSummaryPlayerStats();renderSummaryMatchDurations();if(state.summary?.mode==='team')renderTeamSummary();}
    }
    $$('[data-go]').forEach(b=>b.addEventListener('click',()=>show(b.dataset.go)));
    $$('[data-tab]').forEach(b=>b.addEventListener('click',()=>show(b.dataset.tab)));
    function renderHome() {
        const sessions=state.history||[],totalMatches=sessions.reduce((sum,s)=>sum+(s.played||s.completedMatches?.length||0),0),totalPlayers=sessions.reduce((sum,s)=>sum+(s.players?.length||0),0);
        $('#queue-stats').innerHTML=sessions.length?`<section class="queue-stats"><h3>Your Capikol</h3><div class="queue-stat-grid"><div><strong>${sessions.length}</strong><span>${sessions.length===1?'session':'sessions'}</span></div><div><strong>${totalMatches}</strong><span>${totalMatches===1?'match':'matches'}</span></div><div><strong>${totalPlayers}</strong><span>players</span></div></div></section>`:'';
        const item = state.current || state.history[0]; const el=$('#recent-session');
        const historyEl=$('#home-session-history');
        if(!item) { el.innerHTML='<p class="empty">Create a session to start rotating players.</p>';historyEl.innerHTML='';return; }
        const matches=item.played||item.completedMatches?.length||0;
        el.innerHTML=`<article class="session-card"><span class="section-label" style="margin:0">${state.current?'Active session':'Last session'} · ${ago(item.startedAt)}</span><h3>${escapeHtml(item.name||'Open Play')}</h3><p class="meta">${item.players?.length||0} players&nbsp; · &nbsp;${item.courts||item.matches?.length||0} courts&nbsp; · &nbsp;${matches} ${matches===1?'match':'matches'}</p><button class="card-link" id="open-recent">${state.current?'Open':'View'} →</button></article>`;
        $('#open-recent').onclick=()=>{if(state.current){show('active');return;}if(!item.endedAt)item.endedAt=item.startedAt;state.summary=item;save();show('session-summary');};
        const olderSessions=state.current?sessions:sessions.slice(1),offset=state.current?0:1;
        historyEl.innerHTML=olderSessions.length?`<h3 class="summary-block-title">Session history · ${sessions.length}</h3>${olderSessions.map((session,index)=>{const count=session.played||session.completedMatches?.length||0;return `<article class="session-card"><span class="section-label" style="margin:0">${ago(session.startedAt)}</span><h3>${escapeHtml(session.name||'Open Play')}</h3><p class="meta">${session.players?.length||0} players&nbsp; · &nbsp;${session.courts||session.matches?.length||0} courts&nbsp; · &nbsp;${count} ${count===1?'match':'matches'}</p><button class="card-link" data-home-summary="${index+offset}" type="button">View summary →</button></article>`}).join('')}`:'';
        $$('[data-home-summary]',historyEl).forEach(button=>button.onclick=()=>{const session=sessions[Number(button.dataset.homeSummary)];if(!session)return;if(!session.endedAt)session.endedAt=session.startedAt;state.summary=session;save();show('session-summary');});
    }
    $('#session-name').value=state.setup.name;
    $('#session-name').oninput=e=>{state.setup.name=e.target.value;save();};
    $('#event-date').value=state.setup.eventDate;
    $('#event-date').oninput=e=>{state.setup.eventDate=e.target.value;$('#event-end-date').min=e.target.value;save();};
    $('#event-end-date').value=state.setup.eventEndDate;
    $('#event-end-date').min=state.setup.eventDate;
    $('#event-end-date').oninput=e=>{state.setup.eventEndDate=e.target.value;save();};
    function renderSetup(){
        if(!['balanced','winners','team'].includes(state.setup.mode)) state.setup.mode='balanced';
        $('#event-date').value=state.setup.eventDate;
        $('#event-end-date').value=state.setup.eventEndDate;
        $('#event-end-date').min=state.setup.eventDate;
        $('#games-per-player').value=state.setup.gamesPerPlayer;
        $('#court-count').value=$('#court-count').textContent=state.setup.courts;
        $$('.option').forEach(o=>{const selected=o.dataset.mode===state.setup.mode;o.classList.toggle('selected',selected);o.setAttribute('aria-checked',String(selected));});
        $('#team-count-field').hidden=state.setup.mode!=='team';
        $('#games-per-player-field').hidden=state.setup.mode!=='team';
        $('#team-count').required=state.setup.mode==='team';
        $('#team-count').value=state.setup.teamCount ?? '';
        $('#team-size').required=state.setup.mode==='team';
        $('#team-size').value=state.setup.teamSize ?? '';
    }
    $('#court-minus').onclick=()=>{state.setup.courts=Math.max(1,state.setup.courts-1);renderSetup();save();};
    $('#court-plus').onclick=()=>{state.setup.courts=Math.min(8,state.setup.courts+1);renderSetup();save();};
    $$('.option').forEach(o=>o.onclick=()=>{state.setup.mode=o.dataset.mode;renderSetup();save();});
    $('#team-count').oninput=e=>{const value=Number(e.target.value);state.setup.teamCount=Number.isInteger(value)&&value>=2&&value<=64?value:null;save();};
    $('#team-size').oninput=e=>{const value=Number(e.target.value);state.setup.teamSize=Number.isInteger(value)&&value>=1&&value<=64?value:null;save();renderPlayers();};
    $('#games-per-player').value=state.setup.gamesPerPlayer;
    $('#games-per-player').oninput=e=>{const value=Number(e.target.value);state.setup.gamesPerPlayer=Number.isInteger(value)&&value>=1&&value<=20?value:initial.setup.gamesPerPlayer;save();};
    function bindToggle(id,field){ const el=$(id); el.classList.toggle('on',state.setup[field]); el.onclick=()=>{state.setup[field]=!state.setup[field];el.classList.toggle('on',state.setup[field]);if(field==='pointsOn')$('#point-pills').hidden=!state.setup[field];save();}; }
    bindToggle('#point-toggle','pointsOn'); bindToggle('#timer-toggle','timer'); $('#point-pills').hidden=!state.setup.pointsOn;
    $$('#point-pills .pill').forEach(p=>{p.classList.toggle('selected',+p.textContent===state.setup.points);p.onclick=()=>{state.setup.points=+p.textContent;$$('#point-pills .pill').forEach(x=>x.classList.toggle('selected',x===p));save();};});
    const teamCountIsValid = () => state.setup.mode!=='team'||(Number.isInteger(state.setup.teamCount)&&state.setup.teamCount>=2&&state.setup.teamCount<=64&&Number.isInteger(state.setup.teamSize)&&state.setup.teamSize>=1&&state.setup.teamSize<=64);
    $('#continue-checkin').onclick=()=>{if(!teamCountIsValid()){toast('Enter the number of teams and players per team');$('#team-count').focus();return;}show('checkin');renderPlayers();};
    const ratings=[1,2,3,4,5];
    function renderSkills(){ $('#default-skill').textContent=`${state.rating}-star`; $('#skills').innerHTML=ratings.map(r=>`<button class="skill ${r===state.rating?'selected':''}" data-rating="${r}" aria-label="${r} star rating">${r}<small>${'★'.repeat(r)}</small></button>`).join(''); $$('#skills .skill').forEach(b=>b.onclick=()=>{state.rating=+b.dataset.rating;renderSkills();save();}); }
    const cleanPlayerName = name => name.trim().replace(/\s+/g,' ');
    const playerNameKey = name => cleanPlayerName(name).toLocaleLowerCase();
    const playerNameExists = name => state.players.some(player=>playerNameKey(player.name)===playerNameKey(name));
    const updateAdd=()=>$('#add-player').disabled=!$('#player-name').value.trim();
    $('#player-name').oninput=updateAdd; $('#player-name').onkeydown=e=>{if(e.key==='Enter'&&!$('#add-player').disabled)addPlayer();};
    function addPlayer(){ const input=$('#player-name'),name=cleanPlayerName(input.value);if(!name)return;if(playerNameExists(name)){toast(`${name} is already added`);input.focus();input.select();return;}state.players.push({id:crypto.randomUUID(),name,rating:state.rating,status:'ready',teamIndex:state.setup.mode==='team'?nextAvailableTeam():null});input.value='';updateAdd();renderPlayers();save(); }
    $('#add-player').onclick=addPlayer;
    const ratingGuide=$('#rating-guide'), ratingGuideClose=$('#rating-guide-close');
    function openRatingGuide(){ ratingGuide.hidden=false; document.body.classList.add('guide-open'); requestAnimationFrame(()=>ratingGuide.classList.add('open')); ratingGuideClose.focus(); }
    function closeRatingGuide(){ ratingGuide.classList.remove('open'); document.body.classList.remove('guide-open'); setTimeout(()=>ratingGuide.hidden=true,200); $('#skill-help').focus(); }
    $('#skill-help').onclick=openRatingGuide;
    ratingGuideClose.onclick=closeRatingGuide;
    ratingGuide.onclick=e=>{if(e.target===ratingGuide)closeRatingGuide();};
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&ratingGuide.classList.contains('open'))closeRatingGuide();});
    $('#bulk-toggle').onclick=()=>{const area=$('#bulk-area');area.hidden=!area.hidden;$('#bulk-toggle').textContent=area.hidden?'＋ Bulk paste':'− Hide bulk paste';};
    $('#bulk-input').oninput=e=>$('#add-list').disabled=!e.target.value.trim();
    $('#add-list').onclick=()=>{const names=$('#bulk-input').value.split(/\n/).map(cleanPlayerName).filter(Boolean),seen=new Set(state.players.map(p=>playerNameKey(p.name))),added=[];names.forEach(name=>{const key=playerNameKey(name);if(seen.has(key))return;seen.add(key);const player={id:crypto.randomUUID(),name,rating:state.rating,status:'ready',teamIndex:null};if(state.setup.mode==='team'){const assigned=[...state.players,...added],teamCount=state.setup.teamCount||0,teamSize=state.setup.teamSize||1;player.teamIndex=Array.from({length:teamCount},(_,index)=>index).find(index=>assigned.filter(item=>item.teamIndex===index).length<teamSize)??null;}added.push(player);});state.players.push(...added);$('#bulk-input').value='';$('#add-list').disabled=true;renderPlayers();save();const skipped=names.length-added.length;toast(`${added.length} added${skipped?` · ${skipped} duplicate${skipped===1?'':'s'} skipped`:''}`); };
    const playerRating = player => Math.round(Math.min(5, Math.max(1, Number(player.rating ?? player.skill ?? 4))));
    const playerStatuses=['ready','queued','playing','resting','completed','unavailable'];
    const playerStatus = player => playerStatuses.includes(player.status) ? player.status : 'ready';
    const playerStatusLabel = player => playerStatus(player)==='ready' ? 'available' : playerStatus(player);
    const isAvailable = player => playerStatus(player)==='ready';
    const canPlayAnotherGame = (player, session=state.current) => isAvailable(player) && (player.gamesPlayed||0) < (session?.gamesPerPlayer||initial.setup.gamesPerPlayer);
    const playerInitials = player => player.name.trim().split(/\s+/).slice(0,2).map(part=>part[0]).join('').toUpperCase();
    const teamPalette=[['Red Team','#e05252'],['Orange Team','#ef8a32'],['Green Team','#47a86e'],['Brown Team','#8a6348'],['Blue Team','#3c82d0'],['Purple Team','#8564c6'],['Pink Team','#d66f9e'],['Teal Team','#319d9d']];
    function teamName(index){return String(state.setup.teamNames[index]||teamPalette[index]?.[0]||`Team ${index+1}`).trim()||`Team ${index+1}`;}
    function teamColor(index){return state.setup.teamColors[index]||teamPalette[index]?.[1]||'#1673d1';}
    function playerTeamColor(player,session=state.current){const source=session?.mode==='team'?session:state.setup,color=source?.teamColors?.[player?.teamIndex]||teamPalette[player?.teamIndex]?.[1];return source?.mode==='team'&&Number.isInteger(player?.teamIndex)&&/^#[0-9a-f]{6}$/i.test(color||'')?color:null;}
    function nextAvailableTeam(){for(let index=0;index<(state.setup.teamCount||0);index++)if(state.players.filter(player=>player.teamIndex===index).length<(state.setup.teamSize||1))return index;return null;}
    function playerCard(p,teamPicker=false){const games=p.gamesPlayed||0,wins=p.wins||0,losses=p.losses||0,color=playerTeamColor(p,state.setup),avatarStyle=color?` style="background:${color};color:#fff"`:'' ,details=teamPicker?`${playerStatusLabel(p)} · ${games} games · ${wins}W-${losses}L`:`<span class="rating-stars" aria-hidden="true">${'★'.repeat(playerRating(p))}<span class="empty-star">${'☆'.repeat(5-playerRating(p))}</span></span> · ${playerRating(p)}-star rating`;return `<div class="player"><span class="avatar"${avatarStyle}>${escapeHtml(p.name[0].toUpperCase())}</span><span class="player-info"><strong>${escapeHtml(p.name)}</strong><small>${details}</small></span>${teamPicker?`<select class="team-picker" data-player-status="${p.id}" aria-label="Set ${escapeHtml(p.name)} status">${playerStatuses.map(status=>`<option value="${status}" ${playerStatus(p)===status?'selected':''}>${status==='ready'?'Available':status[0].toUpperCase()+status.slice(1)}</option>`).join('')}</select><select class="team-picker" data-player-team="${p.id}" aria-label="Assign ${escapeHtml(p.name)} to a team"><option value="">Unassigned</option>${Array.from({length:state.setup.teamCount},(_,index)=>{const full=state.players.filter(player=>player.teamIndex===index).length>=state.setup.teamSize&&p.teamIndex!==index;return `<option value="${index}" ${p.teamIndex===index?'selected':''} ${full?'disabled':''}>${escapeHtml(teamName(index))}</option>`;}).join('')}</select><button class="remove" data-edit-player="${p.id}" aria-label="Edit ${escapeHtml(p.name)}">✎</button>`:''}<button class="remove" data-id="${p.id}" aria-label="Remove ${escapeHtml(p.name)}">×</button></div>`;}
    function renderPlayers(){const isTeam=state.setup.mode==='team',list=$('#player-list'),rosters=$('#team-rosters'),start=$('#start-session');$('#rating-controls').hidden=isTeam;$('#bulk-rating-hint').hidden=isTeam;$('#bulk-team-hint').hidden=!isTeam;if(!isTeam){rosters.hidden=true;list.hidden=false;list.innerHTML=state.players.map(p=>playerCard(p)).join('');const need=Math.max(0,4-state.players.length);start.disabled=need>0;start.textContent=updatingSession?'Update session':(need?`Need ${need} more ready`:'Start session');}else{list.hidden=true;rosters.hidden=false;const teamCount=state.setup.teamCount||0,teamSize=state.setup.teamSize||0;rosters.innerHTML=Array.from({length:teamCount},(_,index)=>{const members=state.players.filter(player=>player.teamIndex===index);return `<section class="team-roster" style="--team-color:${teamColor(index)}"><div class="team-roster-head"><input class="team-roster-name" data-team-name="${index}" value="${escapeHtml(teamName(index))}" maxlength="40" aria-label="Team ${index+1} name"><span class="team-roster-count">${members.length}/${teamSize} players</span></div>${members.map(player=>playerCard(player,true)).join('')||'<p class="hint" style="padding:.7rem .85rem">Assign players to this team.</p>'}</section>`;}).join('')+`<section class="team-roster"><div class="team-roster-head"><strong>UNASSIGNED PLAYERS</strong><span class="team-roster-count">${state.players.filter(player=>player.teamIndex===null||player.teamIndex===undefined).length}</span></div>${state.players.filter(player=>player.teamIndex===null||player.teamIndex===undefined).map(player=>playerCard(player,true)).join('')||'<p class="hint" style="padding:.7rem .85rem">All players have been assigned.</p>'}</section>`;const required=teamCount*teamSize,assigned=state.players.filter(player=>Number.isInteger(player.teamIndex)&&player.teamIndex>=0&&player.teamIndex<teamCount).length,complete=teamCount>0&&teamSize>0&&assigned===required&&state.players.length===required;start.disabled=!complete;start.textContent=updatingSession?'Update session':(complete?'Start session':`Assign ${Math.max(0,required-assigned)} more player${required-assigned===1?'':'s'}`);}$$('[data-id]').forEach(b=>b.onclick=()=>{state.players=state.players.filter(p=>p.id!==b.dataset.id);renderPlayers();save();});$$('[data-edit-player]').forEach(b=>b.onclick=()=>{const player=state.players.find(p=>p.id===b.dataset.editPlayer),name=player&&window.prompt('Player name',player.name);if(player&&name?.trim()&&!playerNameExists(name.trim())){player.name=cleanPlayerName(name);renderPlayers();save();}});$$('[data-player-status]').forEach(select=>select.onchange=()=>{const player=state.players.find(p=>p.id===select.dataset.playerStatus);if(!player)return;player.status=select.value;renderPlayers();save();});$$('[data-player-team]').forEach(select=>select.onchange=()=>{const player=state.players.find(p=>p.id===select.dataset.playerTeam);if(!player)return;player.teamIndex=select.value===''?null:Number(select.value);renderPlayers();save();});$$('[data-team-name]').forEach(input=>input.oninput=()=>{state.setup.teamNames[Number(input.dataset.teamName)]=input.value;save();}); }
    const teamColorOptions=teamPalette.map(([name,color])=>({name:name.replace(' Team',''),color}));const teamColorObserver=new MutationObserver(()=>{if(state.setup.mode!=='team')return;$$('.team-roster-name').forEach(input=>{const index=Number(input.dataset.teamName),head=input.parentElement;if(head.querySelector('[data-team-color]'))return;head.insertAdjacentHTML('afterbegin',`<select class="team-color-picker" data-team-color="${index}" aria-label="Choose ${escapeHtml(teamName(index))} color">${teamColorOptions.map(option=>`<option value="${option.color}" ${teamColor(index)===option.color?'selected':''}>${option.name}</option>`).join('')}</select>`);});});teamColorObserver.observe($('#team-rosters'),{childList:true,subtree:true});document.addEventListener('change',event=>{const input=event.target;if(!input.matches?.('[data-team-color]'))return;state.setup.teamColors[Number(input.dataset.teamColor)]=input.value;save();renderPlayers();});
    function shuffled(list){ return [...list].sort(()=>Math.random()-.5); }
    const emptyCourts = courts => Array.from({length:courts},(_,i)=>({id:crypto.randomUUID(),court:i+1,teamA:[],teamB:[],started:false,done:false}));
    const matchupKey = (a,b) => [a,b].sort((left,right)=>left-right).join('-');
    function stageTeamCourt(match){const s=state.current;if(!s||match.teamA.length||match.manualSlots)return;const now=Date.now(),eligible=s.waiting.filter(player=>isAvailable(player)&&(player.gamesPlayed||0)<s.gamesPerPlayer),groups=Array.from({length:s.teamCount||0},(_,index)=>({index,players:eligible.filter(player=>player.teamIndex===index)})).filter(group=>group.players.length>=2),chooseTwo=(players,opponentTeam)=>{const choices=[];for(let first=0;first<players.length;first++)for(let second=first+1;second<players.length;second++){const a=players[first],b=players[second],games=(a.gamesPlayed||0)+(b.gamesPlayed||0),remaining=(s.gamesPerPlayer-(a.gamesPlayed||0))+(s.gamesPerPlayer-(b.gamesPlayed||0)),rest=((now-new Date(a.queuedAt||s.startedAt).getTime())+(now-new Date(b.queuedAt||s.startedAt).getTime()))/60000,repeatTeammate=a.lastTeammateId===b.id||b.lastTeammateId===a.id?1:0,repeatOpponent=(a.lastOpponentTeam===opponentTeam?1:0)+(b.lastOpponentTeam===opponentTeam?1:0);choices.push({players:[a,b],score:games*30-remaining*2-rest*.02+repeatTeammate*25+repeatOpponent*10+Math.random()*3});}choices.sort((a,b)=>a.score-b.score);return choices[0].players;},pairs=[];for(let left=0;left<groups.length;left++)for(let right=left+1;right<groups.length;right++){const a=groups[left],b=groups[right],key=matchupKey(a.index,b.index),teamA=chooseTwo(a.players,b.index),teamB=chooseTwo(b.players,a.index),games=[...teamA,...teamB].reduce((sum,player)=>sum+(player.gamesPlayed||0),0),remaining=[...teamA,...teamB].reduce((sum,player)=>sum+s.gamesPerPlayer-(player.gamesPlayed||0),0);pairs.push({a,b,key,teamA,teamB,score:(s.matchupCounts?.[key]||0)*100+games*15-remaining+Math.random()*5});}if(!pairs.length){toast('Need two available players from two different teams');return;}pairs.sort((first,second)=>first.score-second.score);const pair=pairs[0],ids=new Set([...pair.teamA,...pair.teamB].map(player=>player.id));pair.teamA.forEach(player=>{player.lastTeammateId=pair.teamA.find(teammate=>teammate.id!==player.id)?.id||null;player.lastOpponentTeam=pair.b.index;});pair.teamB.forEach(player=>{player.lastTeammateId=pair.teamB.find(teammate=>teammate.id!==player.id)?.id||null;player.lastOpponentTeam=pair.a.index;});match.teamA=pair.teamA;match.teamB=pair.teamB;match.teamAIndex=pair.a.index;match.teamBIndex=pair.b.index;match.matchupKey=pair.key;s.matchupCounts=s.matchupCounts||{};s.matchupCounts[pair.key]=(s.matchupCounts[pair.key]||0)+1;s.waiting=s.waiting.filter(player=>!ids.has(player.id));save();renderActive();}
    const playerPairKey = (first,second) => [first.id,second.id].sort().join('|');
    const twoPlayerChoices = players => players.flatMap((first,index)=>players.slice(index+1).map(second=>[first,second]));
    function stageTeamCourtWithHistory(match){const s=state.current;if(!s||match.teamA.length||match.manualSlots)return;const now=Date.now(),eligible=s.waiting.filter(player=>isAvailable(player)&&(player.gamesPlayed||0)<s.gamesPerPlayer),groups=Array.from({length:s.teamCount||0},(_,index)=>({index,players:eligible.filter(player=>player.teamIndex===index)})).filter(group=>group.players.length>=2),candidates=[];for(let left=0;left<groups.length;left++)for(let right=left+1;right<groups.length;right++)for(const teamA of twoPlayerChoices(groups[left].players))for(const teamB of twoPlayerChoices(groups[right].players)){const players=[...teamA,...teamB],games=players.reduce((sum,player)=>sum+(player.gamesPlayed||0),0),rest=players.reduce((sum,player)=>sum+(now-new Date(player.queuedAt||s.startedAt).getTime())/60000,0),teammate=(s.teammateHistory?.[playerPairKey(teamA[0],teamA[1])]||0)+(s.teammateHistory?.[playerPairKey(teamB[0],teamB[1])]||0),opponents=teamA.reduce((sum,a)=>sum+teamB.reduce((inner,b)=>inner+(s.opponentHistory?.[playerPairKey(a,b)]||0),0),0),matchup=s.matchupCounts?.[matchupKey(groups[left].index,groups[right].index)]||0;candidates.push({teamA,teamB,a:groups[left].index,b:groups[right].index,score:matchup*100+teammate*30+opponents*12+games*25-rest*.02+Math.random()*4});}if(!candidates.length){toast('Need two available players from two different teams');return;}candidates.sort((a,b)=>a.score-b.score);const chosen=candidates[0],ids=new Set([...chosen.teamA,...chosen.teamB].map(player=>player.id));s.teammateHistory=s.teammateHistory||{};s.opponentHistory=s.opponentHistory||{};[[chosen.teamA[0],chosen.teamA[1]],[chosen.teamB[0],chosen.teamB[1]]].forEach(pair=>{const key=playerPairKey(...pair);s.teammateHistory[key]=(s.teammateHistory[key]||0)+1;});chosen.teamA.forEach(a=>chosen.teamB.forEach(b=>{const key=playerPairKey(a,b);s.opponentHistory[key]=(s.opponentHistory[key]||0)+1;}));const key=matchupKey(chosen.a,chosen.b);s.matchupCounts=s.matchupCounts||{};s.matchupCounts[key]=(s.matchupCounts[key]||0)+1;match.teamA=chosen.teamA;match.teamB=chosen.teamB;match.teamAIndex=chosen.a;match.teamBIndex=chosen.b;match.matchupKey=key;s.waiting=s.waiting.filter(player=>!ids.has(player.id));save();renderActive();}
    function teamRostersComplete(){const count=state.setup.teamCount||0,size=state.setup.teamSize||0;return state.setup.mode!=='team'||(state.players.length===count*size&&Array.from({length:count},(_,index)=>state.players.filter(player=>player.teamIndex===index).length===size).every(Boolean));}
    $('#start-session').onclick=()=>{ if(updatingSession&&state.current){const existingIds=new Set(state.current.players.map(p=>p.id));const added=state.players.filter(p=>!existingIds.has(p.id)).map(p=>({...p,queuedAt:new Date().toISOString()}));state.current.players.push(...added);state.current.waiting.push(...added);updatingSession=false;save();show('active');if(added.length)toast(`${added.length} ${added.length===1?'player':'players'} added to waiting`);return;}if(!teamCountIsValid()){toast('Enter the number of teams and players per team');show('setup');return;}if(!teamRostersComplete()){toast('Fill every team before starting the session');return;}if(state.players.length<4){toast(`Add ${4-state.players.length} more ready ${state.players.length===3?'player':'players'}`);return;}const mode=['balanced','winners','team'].includes(state.setup.mode)?state.setup.mode:'balanced',sessionName=String(state.setup.name??'').trim()||'Open Play',queuedAt=new Date().toISOString(),players=state.players.map(p=>({...p,queuedAt}));state.setup.mode=mode;state.setup.name=String(state.setup.name??'');state.current={...state.setup,id:crypto.randomUUID(),mode,name:sessionName,players,startedAt:queuedAt,round:1,played:0,matches:emptyCourts(state.setup.courts),waiting:[...players]};save();show('active'); };
    function stageCourt(match){ const s=state.current,available=s.waiting.filter(p=>(p.status||'ready')==='ready'),queued=(match.nextQueue||[]).filter(p=>(p.status||'ready')==='ready'),source=queued.length>=4?queued:available;if(match.teamA.length||match.manualSlots||source.length<4)return;const ordered=s.mode==='balanced'?[...source].sort((a,b)=>playerRating(b)-playerRating(a)):shuffled(source),four=[];for(const player of ordered){if(four.length>=4||four.some(p=>p.id===player.id))continue;const partner=ordered.find(p=>p.id===player.partnerId);if(partner&&four.length<=2&&!four.some(p=>p.id===partner.id))four.push(player,partner);else four.push(player);}four.splice(4);const pair=four.filter(p=>four.some(x=>x.id===p.partnerId));if(pair.length>=2){const first=pair[0],partner=four.find(p=>p.id===first.partnerId),rest=four.filter(p=>p.id!==first.id&&p.id!==partner.id);match.teamA=[first,partner];match.teamB=rest;}else{match.teamA=[four[0],four[3]];match.teamB=[four[1],four[2]];}const ids=new Set(four.map(p=>p.id));s.waiting=s.waiting.filter(p=>!ids.has(p.id));match.nextQueue=(match.nextQueue||[]).filter(p=>!ids.has(p.id));match.started=false;match.startedAt=null;match.completedAt=null;match.done=false;save();renderActive(); }
    function unstageCourt(match){ const s=state.current;const queuedAt=new Date().toISOString();[...match.teamA,...match.teamB].forEach(p=>p.queuedAt=queuedAt);s.waiting.push(...match.teamA,...match.teamB);match.teamA=[];match.teamB=[];match.started=false;match.startedAt=null;match.completedAt=null;match.done=false;save();renderActive(); }
    function shuffleCourt(match){ const four=shuffled([...match.teamA,...match.teamB]);match.teamA=[four[0],four[1]];match.teamB=[four[2],four[3]];save();renderActive(); }
    function preferredAnnouncementVoice(){const voices=speechSynthesis.getVoices(),english=voices.filter(voice=>voice.lang?.toLowerCase().startsWith('en'));return english.find(voice=>/google.*(us|uk).*english|microsoft.*(aria|jenny|zira|susan)|samantha|serena/i.test(voice.name))||english.find(voice=>voice.default)||english[0]||voices.find(voice=>voice.default)||null;}
    function callPlayers(match){const teamA=match.teamA.map(p=>p.name).join(', and '),teamB=match.teamB.map(p=>p.name).join(', and '),message=`${teamA}. Versus. ${teamB}. Please proceed to Court ${match.court}.`;toast(`Calling Court ${match.court}`);if('speechSynthesis'in window&&'SpeechSynthesisUtterance'in window){speechSynthesis.cancel();const announcement=new SpeechSynthesisUtterance(message),voice=preferredAnnouncementVoice();if(voice){announcement.voice=voice;announcement.lang=voice.lang;}else announcement.lang='en-US';announcement.rate=.9;announcement.pitch=1;speechSynthesis.speak(announcement);}else toast(message);}
    function courtAvatar(player){const color=playerTeamColor(player);return `<span class="court-avatar"${color?` style="--player-team-color:${color}"`:''}>${escapeHtml(playerInitials(player))}</span>`;}
    function courtPlayer(player){ return `<div class="court-slot">${state.current?.mode==='team'?'':`<span class="court-slot-rating">${playerRating(player)}.0</span>`}${courtAvatar(player)}<span class="court-player-name">${escapeHtml(player.name)}</span></div>`; }
    function editableCourtPlayer(player,index,matchId){return `<button class="manual-slot court-slot" data-edit-staged-slot="${index}" data-edit-staged-match="${matchId}" type="button" aria-label="Change ${escapeHtml(player.name)}">${state.current?.mode==='team'?'':`<span class="court-slot-rating">${playerRating(player)} ★</span>`}${courtAvatar(player)}<span class="court-player-name">${escapeHtml(player.name)}</span></button>`;}
    function manualSlot(player,index,matchId){return `<button class="manual-slot court-slot" data-pick-slot="${index}" data-pick-match="${matchId}" type="button" aria-label="${player?`Change ${escapeHtml(player.name)}`:'Pick a player'}">${player?`${state.current?.mode==='team'?'':`<span class="court-slot-rating">${playerRating(player)} ★</span>`}${courtAvatar(player)}<span class="court-player-name">${escapeHtml(player.name)}</span>`:'<span class="court-slot-rating"></span><span class="empty-slot-circle">＋</span><span class="empty-slot-label">Empty</span>'}</button>`;}
    function beginManual(match){match.manualSlots=[null,null,null,null];save();renderActive();}
    function finalizeManual(match){if(!match.manualSlots?.every(Boolean))return;match.teamA=[match.manualSlots[0],match.manualSlots[1]];match.teamB=[match.manualSlots[2],match.manualSlots[3]];delete match.manualSlots;match.started=false;match.done=false;save();renderActive();}
    function cancelManual(match){const queuedAt=new Date().toISOString();(match.manualSlots||[]).filter(Boolean).forEach(p=>{p.queuedAt=queuedAt;state.current.waiting.push(p);});delete match.manualSlots;save();renderActive();}
    function addUpNext(){const s=state.current,available=s.waiting.filter(p=>(p.status||'ready')==='ready');if(s.mode==='team'){const next={id:crypto.randomUUID(),teamA:[],teamB:[]};stageTeamCourtWithHistory(next);if(next.teamA.length===2&&next.teamB.length===2){s.upNext.push(next);save();renderActive();}return;}if(available.length<4)return;const picked=(s.mode==='balanced'?[...available].sort((a,b)=>playerRating(b)-playerRating(a)):shuffled(available)).slice(0,4),ids=new Set(picked.map(p=>p.id));s.waiting=s.waiting.filter(p=>!ids.has(p.id));s.upNext.push({id:crypto.randomUUID(),teamA:[picked[0],picked[3]],teamB:[picked[1],picked[2]]});save();renderActive();}
    function refillUpNext(next){const s=state.current,available=s.waiting.filter(p=>(p.status||'ready')==='ready');if((next.teamA.length+next.teamB.length)||available.length<4)return;if(s.mode==='team'){stageTeamCourtWithHistory(next);return;}const picked=(s.mode==='balanced'?[...available].sort((a,b)=>playerRating(b)-playerRating(a)):shuffled(available)).slice(0,4),ids=new Set(picked.map(p=>p.id));s.waiting=s.waiting.filter(p=>!ids.has(p.id));next.teamA=[picked[0],picked[3]];next.teamB=[picked[1],picked[2]];}
    function removeUpNextPlayer(nextId,playerId){const s=state.current,next=s.upNext.find(item=>item.id===nextId),player=next&&[...next.teamA,...next.teamB].find(p=>p.id===playerId);if(!player)return;player.queuedAt=new Date().toISOString();next.teamA=next.teamA.filter(p=>p.id!==playerId);next.teamB=next.teamB.filter(p=>p.id!==playerId);s.waiting.push(player);save();renderActive();}
    function removeUpNext(nextId){const s=state.current,next=s.upNext.find(item=>item.id===nextId);if(!next)return;[...next.teamA,...next.teamB].forEach(player=>{player.queuedAt=new Date().toISOString();s.waiting.push(player);});s.upNext=s.upNext.filter(item=>item.id!==nextId);save();renderActive();}
    function editUpNextPlayer(nextId,playerId){const s=state.current,next=s.upNext.find(item=>item.id===nextId),old=next&&[...next.teamA,...next.teamB].find(p=>p.id===playerId),available=s.waiting.filter(p=>(p.status||'ready')==='ready');if(!old||!available.length)return;const choices=available.map((p,index)=>`${index+1}. ${p.name}`).join('\n'),replacement=available[Number(window.prompt(`Replace ${old.name} with:\n${choices}`))-1];if(!replacement)return;const team=next.teamA.some(p=>p.id===playerId)?next.teamA:next.teamB,index=team.findIndex(p=>p.id===playerId);old.queuedAt=new Date().toISOString();s.waiting=s.waiting.filter(p=>p.id!==replacement.id);s.waiting.push(old);team[index]=replacement;save();renderActive();}
    function sendUpNext(nextId,courtId=null){const s=state.current,next=s.upNext.find(item=>item.id===nextId),court=courtId?s.matches.find(match=>match.id===courtId):s.matches.find(m=>!m.teamA?.length&&!m.manualSlots);if(!next||!court)return;if(next.teamA.length!==2||next.teamB.length!==2){toast('Each next match needs 4 players');return;}court.teamA=next.teamA;court.teamB=next.teamB;court.teamAIndex=next.teamAIndex;court.teamBIndex=next.teamBIndex;court.matchupKey=next.matchupKey;s.upNext=s.upNext.filter(item=>item.id!==nextId);save();renderActive();addUpNext();toast(`Next match sent to Court ${court.court}`);}
    function autoFillManual(match){const empty=match.manualSlots.map((p,i)=>p?null:i).filter(i=>i!==null),available=state.current.waiting.filter(p=>(p.status||'ready')==='ready');if(available.length<empty.length)return;const ordered=state.current.mode==='balanced'?[...available].sort((a,b)=>playerRating(b)-playerRating(a)):shuffled(available);const picked=ordered.slice(0,empty.length),ids=new Set(picked.map(p=>p.id));state.current.waiting=state.current.waiting.filter(p=>!ids.has(p.id));empty.forEach((slot,i)=>match.manualSlots[slot]=picked[i]);finalizeManual(match);}
    function removeCourt(match){const s=state.current;if(s.matches.length<=1||match.teamA.length)return;if(match.manualSlots)cancelManual(match);(match.nextQueue||[]).forEach(p=>{p.queuedAt=new Date().toISOString();s.waiting.push(p);});s.matches=s.matches.filter(m=>m.id!==match.id);s.matches.forEach((m,i)=>m.court=i+1);s.courts=s.matches.length;state.setup.courts=s.courts;save();renderActive();}
    function addCourt(){const s=state.current;if(!s||s.matches.length>=8)return;s.matches.push({id:crypto.randomUUID(),court:s.matches.length+1,teamA:[],teamB:[],started:false,done:false,nextQueue:[],nextQueueEnabled:false});s.courts=s.matches.length;state.setup.courts=s.courts;save();renderActive();toast(`Court ${s.courts} added`);}
    const elapsedMinutes = (startedAt,endedAt=null) => Math.max(0,Math.floor(((endedAt?new Date(endedAt).getTime():Date.now())-new Date(startedAt).getTime())/60000));
    const matchDuration = (startedAt,completedAt) => {if(!startedAt||!completedAt)return'—';const seconds=Math.max(0,Math.floor((new Date(completedAt).getTime()-new Date(startedAt).getTime())/1000));if(!Number.isFinite(seconds))return'—';const hours=Math.floor(seconds/3600),minutes=Math.floor(seconds%3600/60),remaining=String(seconds%60).padStart(2,'0');return hours?`${hours}:${String(minutes).padStart(2,'0')}:${remaining}`:`${minutes}:${remaining}`;};
    function updateMatchTimers(){ $$('[data-match-timer]').forEach(el=>{el.textContent=`${elapsedMinutes(el.dataset.startedAt,el.dataset.endedAt||null)}m`;}); }
    const playerPicker=$('#player-picker'),playerPickerSheet=$('#player-picker-sheet');
    function hidePlayerPicker(){playerPicker.hidden=true;pickerMatchId=null;pickerSlotIndex=null;pickerWasStaged=false;}
    function dismissPlayerPicker(){const match=state.current?.matches.find(m=>m.id===pickerMatchId),restore=pickerWasStaged;hidePlayerPicker();if(restore&&match)finalizeManual(match);}
    function showPlayerPicker(match,index,fromStaged=false){pickerMatchId=match.id;pickerSlotIndex=index;pickerWasStaged=fromStaged;const selected=match.manualSlots[index],s=state.current,available=s.waiting.filter(p=>(p.status||'ready')==='ready');playerPickerSheet.innerHTML=`<div class="player-picker-head"><h3 id="player-picker-title">Pick player</h3><p>Choose someone from the waiting list</p></div>${selected?'<button class="clear-manual-slot" id="clear-manual-slot">Clear this slot</button>':''}<span class="picker-label">Available · ${available.length}</span><div>${available.map(p=>{const mins=Math.max(0,Math.floor((Date.now()-new Date(p.queuedAt||s.startedAt))/60000));return `<button class="picker-player" data-picker-player="${p.id}" type="button"><span class="waiting-avatar">${escapeHtml(playerInitials(p))}</span><span><strong>${escapeHtml(p.name)}</strong><small>${'★'.repeat(playerRating(p))} · ${p.gamesPlayed||0} games</small></span><span class="picker-wait">${mins}m</span></button>`}).join('')}</div><div class="picker-cancel-wrap"><button class="winner-cancel" id="picker-cancel" type="button">Cancel</button></div>`;playerPicker.hidden=false;$$('[data-picker-player]',playerPickerSheet).forEach(b=>b.onclick=()=>selectManualPlayer(b.dataset.pickerPlayer));const clear=$('#clear-manual-slot');if(clear)clear.onclick=clearManualSlot;$('#picker-cancel').onclick=dismissPlayerPicker;}
    function editStagedPlayer(match,index){match.manualSlots=[...match.teamA,...match.teamB];match.teamA=[];match.teamB=[];save();showPlayerPicker(match,index,true);}
    function selectManualPlayer(playerId){const s=state.current,match=s.matches.find(m=>m.id===pickerMatchId),player=s.waiting.find(p=>p.id===playerId);if(!match||!player)return;const previous=match.manualSlots[pickerSlotIndex];if(previous){previous.queuedAt=new Date().toISOString();s.waiting.push(previous);}s.waiting=s.waiting.filter(p=>p.id!==playerId);match.manualSlots[pickerSlotIndex]=player;hidePlayerPicker();match.manualSlots.every(Boolean)?finalizeManual(match):(save(),renderActive());}
    function clearManualSlot(){const match=state.current?.matches.find(m=>m.id===pickerMatchId),player=match?.manualSlots[pickerSlotIndex];if(!match||!player)return;player.queuedAt=new Date().toISOString();state.current.waiting.push(player);match.manualSlots[pickerSlotIndex]=null;hidePlayerPicker();save();renderActive();}
    playerPicker.onclick=e=>{if(e.target===playerPicker)dismissPlayerPicker();};
    const playerActionSheet=$('#player-action-sheet'),playerActionPanel=$('#player-action-panel');
    const sessionPlayer = id => state.current?.players.find(p=>p.id===id);
    function playerCopies(id){const s=state.current;if(!s)return[];const copies=[s.players.find(p=>p.id===id),s.waiting.find(p=>p.id===id),...s.matches.flatMap(m=>[...(m.teamA||[]),...(m.teamB||[]),...(m.manualSlots||[]),...(m.nextQueue||[])]).filter(p=>p?.id===id)];return [...new Set(copies.filter(Boolean))];}
    function updatePlayer(id,changes){playerCopies(id).forEach(p=>Object.assign(p,changes));}
    function hidePlayerActions(){playerActionSheet.hidden=true;actionPlayerId=null;}
    function showPlayerActions(playerId){const p=sessionPlayer(playerId)||state.current?.waiting.find(x=>x.id===playerId);if(!p)return;actionPlayerId=playerId;const partner=sessionPlayer(p.partnerId),status=p.status||'ready';playerActionPanel.innerHTML=`<div class="player-action-head"><h3 id="player-action-title">${escapeHtml(p.name)}</h3><p>${'★'.repeat(playerRating(p))} · ${status}${partner?` · ↔ ${escapeHtml(partner.name)}`:''}</p></div><button class="player-action" id="link-partner">${partner?`Unlink from ${escapeHtml(partner.name)}`:'Link with partner…'}</button><button class="player-action" data-player-status="${status==='resting'?'ready':'resting'}">${status==='resting'?'Mark as ready':'Mark as resting'}</button><button class="player-action" data-player-status="${status==='away'?'ready':'away'}">${status==='away'?'Mark as ready':'Mark as away'}</button><button class="player-action danger" id="remove-session-player">Remove from session</button><div class="picker-cancel-wrap"><button class="winner-cancel" id="player-action-cancel">Cancel</button></div>`;playerActionSheet.hidden=false;$('#link-partner').onclick=()=>partner?unlinkPlayers(p,partner):showPartnerPicker(p);$$('[data-player-status]',playerActionPanel).forEach(b=>b.onclick=()=>{updatePlayer(p.id,{status:b.dataset.playerStatus});hidePlayerActions();save();renderActive();});$('#remove-session-player').onclick=()=>removeSessionPlayer(p);$('#player-action-cancel').onclick=hidePlayerActions;}
    function unlinkPlayers(player,partner){updatePlayer(player.id,{partnerId:null});updatePlayer(partner.id,{partnerId:null});hidePlayerActions();save();renderActive();}
    function showPartnerPicker(player){const candidates=state.current.players.filter(p=>p.id!==player.id);playerActionPanel.innerHTML=`<div class="player-action-head"><h3 id="player-action-title">Link ${escapeHtml(player.name)}</h3><p>Linked players are kept on the same team when both are staged.</p></div><span class="picker-label">Pick a partner · ${candidates.length}</span>${candidates.map(p=>`<button class="picker-player" data-link-player="${p.id}"><span class="waiting-avatar">${escapeHtml(playerInitials(p))}</span><span><strong>${escapeHtml(p.name)}</strong><small>${'★'.repeat(playerRating(p))}${p.partnerId?` · linked to ${escapeHtml(sessionPlayer(p.partnerId)?.name||'player')}`:''}</small></span></button>`).join('')}<div class="picker-cancel-wrap"><button class="winner-cancel" id="partner-cancel">Cancel</button></div>`;$$('[data-link-player]',playerActionPanel).forEach(b=>b.onclick=()=>linkPlayers(player,sessionPlayer(b.dataset.linkPlayer)));$('#partner-cancel').onclick=()=>showPlayerActions(player.id);}
    function linkPlayers(player,partner){if(!partner)return;const oldA=sessionPlayer(player.partnerId),oldB=sessionPlayer(partner.partnerId);if(oldA)updatePlayer(oldA.id,{partnerId:null});if(oldB)updatePlayer(oldB.id,{partnerId:null});updatePlayer(player.id,{partnerId:partner.id});updatePlayer(partner.id,{partnerId:player.id});hidePlayerActions();save();renderActive();toast(`${player.name} linked with ${partner.name}`);}
    function removeSessionPlayer(player){const s=state.current,partner=sessionPlayer(player.partnerId);if(partner)updatePlayer(partner.id,{partnerId:null});s.waiting=s.waiting.filter(p=>p.id!==player.id);s.matches.forEach(match=>match.nextQueue=(match.nextQueue||[]).filter(p=>p.id!==player.id));s.players=s.players.filter(p=>p.id!==player.id);state.players=state.players.filter(p=>p.id!==player.id);hidePlayerActions();save();renderActive();toast(`${player.name} removed`);}
    playerActionSheet.onclick=e=>{if(e.target===playerActionSheet)hidePlayerActions();};
    const standingsDialog=$('#standings-dialog'),standingsPanel=$('#standings-panel');
    function hideStandings(){standingsDialog.hidden=true;}
    function showStandings(){const s=state.current;if(!s)return;const playingIds=new Set(s.matches.flatMap(m=>[...(m.teamA||[]),...(m.teamB||[]),...(m.manualSlots||[])]).filter(Boolean).map(p=>p.id));const players=[...s.players].sort((a,b)=>{const gamesA=a.gamesPlayed||0,gamesB=b.gamesPlayed||0,rateA=gamesA?(a.wins||0)/gamesA:0,rateB=gamesB?(b.wins||0)/gamesB:0;return (b.wins||0)-(a.wins||0)||rateB-rateA||gamesB-gamesA||a.name.localeCompare(b.name);});standingsPanel.innerHTML=`<div class="standings-head"><div><h3 id="standings-title">Live Standings</h3><p>${escapeHtml(s.name)} · ${s.played||0} ${(s.played||0)===1?'game':'games'}</p></div><button class="standings-close" id="standings-close" aria-label="Close">×</button></div><div class="standings-note">Provisional · fewer than 5 games</div><div>${players.map((p,index)=>{const games=p.gamesPlayed||0,wins=p.wins||0,rate=games?Math.round(wins/games*100):0,playing=playingIds.has(p.id),status=playing?'playing':p.status||'ready',qualified=games>=5,isLeader=index<3;const badge=status==='ready'?'':`<span class="standing-status ${status}">${status[0].toUpperCase()+status.slice(1)}</span>`;return `<div class="standing-row ${isLeader?`top-${index+1}`:''}"><span class="standing-rank ${isLeader?'leader':qualified?'qualified':''}">${isLeader?index+1:qualified?index+1:'–'}</span><div><div class="standing-name">${escapeHtml(p.name)} <span class="standing-stars">${'★'.repeat(playerRating(p))}</span>${badge}</div><div class="standing-meta">${rate}% win rate · ${games} ${games===1?'game':'games'}${qualified?'':` · ${Math.max(0,5-games)} more to qualify`}</div></div><span class="standing-wins">${wins}<small>wins</small></span></div>`}).join('')}</div><div class="standings-actions"><button class="standings-back" id="standings-back">Back to Game</button></div>`;standingsDialog.hidden=false;$('#standings-close').onclick=hideStandings;$('#standings-back').onclick=hideStandings;}
    standingsDialog.onclick=e=>{if(e.target===standingsDialog)hideStandings();};
    const currentOpenMatch = () => state.current?.matches.find(m=>m.id===openMatchId);
    function updateMatchDetailClock(){ const m=currentOpenMatch();if(!m?.startedAt)return;const seconds=Math.max(0,Math.floor(((m.completedAt?new Date(m.completedAt).getTime():Date.now())-new Date(m.startedAt).getTime())/1000));const clock=$('#match-clock');if(clock)clock.textContent=`${Math.floor(seconds/60)}:${String(seconds%60).padStart(2,'0')}`; }
    const scoreCourtPlayers = (teamA,teamB) => `${teamA.map(player=>{const color=playerTeamColor(player);return `<span class="score-scene-player team-a-player"${color?` style="--player-color:${color}"`:''} title="${escapeHtml(player.name)}">${escapeHtml(playerInitials(player))}</span>`;}).join('')}${teamB.map(player=>{const color=playerTeamColor(player);return `<span class="score-scene-player team-b-player"${color?` style="--player-color:${color}"`:''} title="${escapeHtml(player.name)}">${escapeHtml(playerInitials(player))}</span>`;}).join('')}<span class="score-court-ball"></span>`;
    function renderMatchDetail(){ const m=currentOpenMatch(),s=state.current;if(!m||!s){show('active');return;}const teamANames=m.teamA.map(p=>p.name).join(' · '),teamBNames=m.teamB.map(p=>p.name).join(' · ');$('#detail-court').textContent=`Court ${m.court}`;$('#detail-round').textContent=`Round ${s.round}`;$('#match-target').textContent=s.pointsOn?`Game to ${s.points}`:'Open score';$('#team-a-names').textContent=teamANames;$('#team-b-names').textContent=teamBNames;$('#match-court-scene').innerHTML=scoreCourtPlayers(m.teamA,m.teamB);$('#score-a').textContent=m.scoreA||0;$('#score-b').textContent=m.scoreB||0;$('#score-team-a').setAttribute('aria-label',`${teamANames}, ${m.scoreA||0} points. Tap to score; hold to undo.`);$('#score-team-b').setAttribute('aria-label',`${teamBNames}, ${m.scoreB||0} points. Tap to score; hold to undo.`);$('#score-team-a').classList.toggle('leading',(m.scoreA||0)>(m.scoreB||0));$('#score-team-b').classList.toggle('leading',(m.scoreB||0)>(m.scoreA||0));updateMatchDetailClock(); }
    function celebrateScore(team){const button=$(team==='a'?'#score-team-a':'#score-team-b'),court=$('#match-court-scene'),courtClass=`celebrate-${team}`;clearTimeout(button.celebrationTimer);button.classList.remove('celebrate');court.classList.remove('celebrate-a','celebrate-b');void court.offsetWidth;button.classList.add('celebrate');court.classList.add(courtClass);if(navigator.vibrate)navigator.vibrate(18);button.celebrationTimer=setTimeout(()=>{button.classList.remove('celebrate');court.classList.remove(courtClass);},780);}
    function changeScore(team,amount){ const m=currentOpenMatch();if(!m||m.done)return;const key=team==='a'?'scoreA':'scoreB',previous=m[key]||0;m[key]=Math.max(0,previous+amount);save();renderMatchDetail();if(amount>0&&m[key]>previous)celebrateScore(team); }
    function bindScoreButton(id,team){ const button=$(id);let holdTimer,held=false;button.onpointerdown=()=>{held=false;holdTimer=setTimeout(()=>{held=true;changeScore(team,-1);},600);};button.onpointerup=button.onpointercancel=button.onpointerleave=()=>clearTimeout(holdTimer);button.onclick=()=>{if(held){held=false;return;}changeScore(team,1);}; }
    function renderActive(){
        const s=state.current;if(!s){show('home');return;}s.gamesPerPlayer=Math.min(20,Math.max(1,Number(s.gamesPerPlayer)||initial.setup.gamesPerPlayer));s.players.forEach(player=>{const completed=(player.gamesPlayed||0)>=s.gamesPerPlayer;if(completed&&playerStatus(player)!=='completed')updatePlayer(player.id,{status:'completed'});if(!completed&&playerStatus(player)==='completed')updatePlayer(player.id,{status:'ready'});});
        s.matches=s.matches||emptyCourts(s.courts);s.waiting=s.waiting||[];s.upNext=s.upNext||[];s.upNext.forEach(refillUpNext);
        const readyCount=s.waiting.filter(p=>(p.status||'ready')==='ready').length;
        $('#active-title').textContent=s.name;
        const played=s.played||s.matches.filter(m=>m.done).length;
        $('#active-summary').textContent=`Round ${s.round} · ${played} ${played===1?'match':'matches'} played`;
        $('#matches').innerHTML=s.matches.map(m=>{
            const staged=m.teamA?.length===2&&m.teamB?.length===2;
            const building=Array.isArray(m.manualSlots);
            const players=staged?[...m.teamA,...m.teamB]:[];
            const picked=building?m.manualSlots.filter(Boolean).length:0;
            const queuedReady=(m.nextQueue||[]).filter(p=>(p.status||'ready')==='ready').length,hasUpNext=s.upNext.some(item=>item.teamA?.length===2&&item.teamB?.length===2),canStage=hasUpNext||readyCount>=4||queuedReady>=4;
            const status=building?`Building · ${picked}/4`:!staged?'Open':'● Staged';
            const live=m.startedAt?`<span class="court-live"><span class="court-alert" aria-hidden="true">⚑</span><span class="match-timer ${m.done?'complete':''}" data-match-timer data-started-at="${m.startedAt}" data-ended-at="${m.completedAt||''}">${elapsedMinutes(m.startedAt,m.completedAt)}m</span></span>`:`<span>${status}</span>`;
            const courtContent=staged?(m.started?players.map(courtPlayer).join(''):players.map((player,index)=>editableCourtPlayer(player,index,m.id)).join('')):building?m.manualSlots.map((player,index)=>manualSlot(player,index,m.id)).join(''):'<div class="court-slot"></div>'.repeat(4);
            const teamAName=s.mode==='team'?(s.teamNames?.[m.teamAIndex]||teamPalette[m.teamAIndex]?.[0]||'Team 1'):'Team 1',teamBName=s.mode==='team'?(s.teamNames?.[m.teamBIndex]||teamPalette[m.teamBIndex]?.[0]||'Team 2'):'Team 2',teamAColor=s.mode==='team'?(s.teamColors?.[m.teamAIndex]||teamPalette[m.teamAIndex]?.[1]||'#1673d1'):null,teamBColor=s.mode==='team'?(s.teamColors?.[m.teamBIndex]||teamPalette[m.teamBIndex]?.[1]||'#f28b22'):null;
            const liveTeams=`<div class="live-team-actions"><button class="live-team-winner team-a" data-live-winner="a" data-live-match="${m.id}" type="button" ${teamAColor?`style="background:${teamAColor}"`:''}><span class="live-team-win">${escapeHtml(teamAName)} wins</span></button><button class="live-team-winner team-b" data-live-winner="b" data-live-match="${m.id}" type="button" ${teamBColor?`style="background:${teamBColor}"`:''}><span class="live-team-win">${escapeHtml(teamBName)} wins</span></button></div>`;
            const actions=staged?(m.started?liveTeams:`<div class="court-actions"><button class="start-match" data-start="${m.id}">Start match</button><button class="shuffle" data-shuffle="${m.id}" aria-label="Shuffle players">Shuffle</button><button class="call-players" data-call-players="${m.id}" aria-label="Call players">📣</button><button class="unstage" data-unstage="${m.id}" aria-label="Clear court">×</button></div>`):building?`<div class="building-actions"><button class="autofill" data-autofill="${m.id}" ${readyCount<4-picked?'disabled':''}>Auto-fill rest</button><span class="slots-needed">Need ${4-picked} more</span><button class="unstage" data-cancel-manual="${m.id}" aria-label="Cancel manual selection">×</button></div>`:`<div class="empty-court-actions"><button class="stage" data-stage="${m.id}" ${canStage?'':'disabled'}>${canStage?(hasUpNext?'Stage Up Next →':'Stage next →'):'Need 4 ready players'}</button><button class="manual-pick" data-manual="${m.id}">or pick players manually</button><button class="remove-court" data-remove-court="${m.id}" ${s.matches.length<=1?'disabled':''}>× Remove court</button></div>`;
            const queue=m.nextQueue||[];
            const nextQueue=m.nextQueueEnabled?`<section class="next-queue"><div class="next-queue-head"><span>Next queue · ${queue.length}</span><span class="next-queue-actions"><button data-queue-add="${m.id}" type="button">+ Add player</button><button class="queue-disable" data-queue-toggle="${m.id}" type="button">Hide</button></span></div>${queue.length?`<div class="next-queue-list">${queue.map(p=>`<div class="next-queue-player"><span><strong>${escapeHtml(p.name)}</strong><small>${'★'.repeat(playerRating(p))} · reserved for Court ${m.court}</small></span><button class="queue-player-edit" data-queue-edit="${m.id}" data-queue-player="${p.id}" type="button" aria-label="Edit ${escapeHtml(p.name)}">✎</button><button class="queue-player-remove" data-queue-remove="${m.id}" data-queue-player="${p.id}" type="button" aria-label="Remove ${escapeHtml(p.name)} from next queue">×</button></div>`).join('')}</div>`:'<p class="next-queue-empty">Optional — reserve players for this court’s next match.</p>'}</section>`:`<div class="next-queue-actions" style="margin-top:.55rem"><button data-queue-toggle="${m.id}" type="button">＋ Add next queue</button></div>`;
            return `<article class="match-card ${m.started&&!m.done?'playing open-match':''} ${building?'building':''}" ${m.started&&!m.done?`data-open-match="${m.id}" role="button" tabindex="0" aria-label="Open Court ${m.court} match"`:''}><div class="match-head"><strong>Court ${m.court}</strong>${live}</div><div class="court-preview ${staged||building?'':'empty'}" aria-label="${staged?'Staged players':building?'Choose players':'Empty court'}">${courtContent}</div>${actions}${nextQueue}</article>`;
        }).join('');
        $$('[data-stage]').forEach(b=>b.onclick=()=>{const match=s.matches.find(x=>x.id===b.dataset.stage),next=s.upNext.find(item=>item.teamA?.length===2&&item.teamB?.length===2);if(next){sendUpNext(next.id,match.id);return;}if(s.mode==='team')stageTeamCourtWithHistory(match);else stageCourt(match);});
        if(s.mode==='team')$$('.manual-pick').forEach(button=>button.hidden=true);
        $$('[data-start]').forEach(b=>b.onclick=()=>{const m=s.matches.find(x=>x.id===b.dataset.start);m.started=true;m.startedAt=new Date().toISOString();m.completedAt=null;m.scoreA=0;m.scoreB=0;save();renderActive();});
        $$('[data-live-winner]').forEach(b=>b.onclick=e=>{e.stopPropagation();const match=s.matches.find(m=>m.id===b.dataset.liveMatch);if(!match)return;const winner=b.dataset.liveWinner,team=winner==='a'?match.teamA:match.teamB,label=team.map(p=>p.name).join(' & ');openMatchId=match.id;closeOpenMatch(true,winner);toast(`${label} wins`);});
        $$('[data-manual]').forEach(b=>b.onclick=()=>beginManual(s.matches.find(x=>x.id===b.dataset.manual)));
        $$('[data-pick-slot]').forEach(b=>b.onclick=()=>{const match=s.matches.find(m=>m.id===b.dataset.pickMatch);if(match)showPlayerPicker(match,Number(b.dataset.pickSlot));});
        $$('[data-edit-staged-slot]').forEach(b=>b.onclick=()=>{const match=s.matches.find(m=>m.id===b.dataset.editStagedMatch);if(match)editStagedPlayer(match,Number(b.dataset.editStagedSlot));});
        $$('[data-autofill]').forEach(b=>b.onclick=()=>autoFillManual(s.matches.find(x=>x.id===b.dataset.autofill)));
        $$('[data-cancel-manual]').forEach(b=>b.onclick=()=>cancelManual(s.matches.find(x=>x.id===b.dataset.cancelManual)));
        $$('[data-remove-court]').forEach(b=>b.onclick=()=>removeCourt(s.matches.find(x=>x.id===b.dataset.removeCourt)));
        const openCourt=s.matches.find(m=>!m.teamA?.length&&!m.manualSlots),upNext=s.upNext||[];
        $('#up-next').innerHTML=`<section class="up-next"><div class="up-next-head"><h3>Matchmaking · Up next ${upNext.length}</h3><button class="up-next-add" id="add-up-next" type="button" ${readyCount<4?'disabled':''}>＋ Add match</button></div>${upNext.length?`<div class="up-next-grid">${upNext.map((item,index)=>`<article class="up-next-card"><div class="up-next-card-head"><span>Up Next ${index+1} · Auto</span><span>${item.teamA.length+item.teamB.length}/4</span></div><div class="up-next-teams">${[['TEAM 1',item.teamA],['TEAM 2',item.teamB]].map(([label,team])=>`<div class="up-next-team"><label>${label}</label>${team.map(p=>`<div class="up-next-player"><span>${escapeHtml(p.name)}</span><button data-up-next-edit="${item.id}" data-up-next-player="${p.id}" type="button" title="Replace player">✎</button><button data-up-next-remove="${item.id}" data-up-next-player="${p.id}" type="button" title="Remove player">×</button></div>`).join('')}</div>`).join('')}</div><div class="up-next-actions"><button class="send-up-next" data-send-up-next="${item.id}" type="button" ${!openCourt||item.teamA.length!==2||item.teamB.length!==2?'disabled':''}>${openCourt?`Send to Court ${openCourt.court}`:'No open court'}</button></div></article>`).join('')}</div>`:'<p class="hint">Optional — prepare the next matches before a court opens.</p>'}</section>`;
        if(s.mode==='team')$$('.up-next-card').forEach((card,index)=>{const item=upNext[index],labels=$$('label',card),panels=$$('.up-next-team',card),indices=[item.teamAIndex,item.teamBIndex];indices.forEach((teamIndex,side)=>{const color=s.teamColors?.[teamIndex]||teamPalette[teamIndex]?.[1]||'#1673d1',name=teamIndex===undefined?`Team ${side===0?'A':'B'}`:(s.teamNames?.[teamIndex]||teamPalette[teamIndex]?.[0]||`Team ${teamIndex+1}`);labels[side].textContent=name;labels[side].style.color=color;panels[side].style.backgroundColor=`${color}20`;panels[side].style.borderTop=`3px solid ${color}`;});card.style.borderColor=s.teamColors?.[item.teamAIndex]||teamPalette[item.teamAIndex]?.[1]||'#a7edc3';});
        $$('.up-next-card-head').forEach((head,index)=>head.insertAdjacentHTML('beforeend',`<button class="remove-up-next" data-remove-up-next="${upNext[index].id}" type="button" aria-label="Remove Up Next ${index+1}">×</button>`));
        const addUpNextButton=$('#add-up-next');if(addUpNextButton){addUpNextButton.disabled=readyCount<4;addUpNextButton.textContent='＋ Add match';addUpNextButton.onclick=addUpNext;}
        $$('[data-remove-up-next]').forEach(b=>b.onclick=()=>removeUpNext(b.dataset.removeUpNext));
        $$('[data-up-next-remove]').forEach(b=>b.onclick=()=>removeUpNextPlayer(b.dataset.upNextRemove,b.dataset.upNextPlayer));
        $$('[data-up-next-edit]').forEach(b=>b.onclick=()=>editUpNextPlayer(b.dataset.upNextEdit,b.dataset.upNextPlayer));
        $$('[data-send-up-next]').forEach(b=>b.onclick=()=>sendUpNext(b.dataset.sendUpNext));
        $$('[data-open-match]').forEach(card=>{const open=()=>{openMatchId=card.dataset.openMatch;show('match-detail');};card.onclick=open;card.onkeydown=e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();open();}};});
        $$('[data-shuffle]').forEach(b=>b.onclick=()=>{if(s.mode==='team'){toast('Team matches keep each team together');return;}shuffleCourt(s.matches.find(x=>x.id===b.dataset.shuffle));});
        $$('[data-call-players]').forEach(b=>b.onclick=()=>callPlayers(s.matches.find(x=>x.id===b.dataset.callPlayers)));
        $$('[data-unstage]').forEach(b=>b.onclick=()=>unstageCourt(s.matches.find(x=>x.id===b.dataset.unstage)));
        $$('.finish').forEach(b=>b.onclick=()=>{const m=s.matches.find(x=>x.id===b.dataset.match);m.done=!m.done;m.completedAt=m.done?new Date().toISOString():null;save();renderActive();});
        const emptyCount=s.matches.filter(m=>!m.teamA?.length&&!m.manualSlots).length;
        const readyPlayers=s.waiting.filter(isAvailable),unavailablePlayers=s.waiting.filter(p=>!isAvailable(p));
        const waitingRow=(p,unavailable=false)=>{const mins=Math.max(0,Math.floor((Date.now()-new Date(p.queuedAt||s.startedAt))/60000)),wins=p.wins||0,losses=p.losses||0,games=p.gamesPlayed??wins+losses,rating=playerRating(p),partner=sessionPlayer(p.partnerId),status=p.status||'ready',color=playerTeamColor(p,s);return `<button class="waiting-player" data-waiting-player="${p.id}" type="button"><span class="waiting-avatar"${color?` style="background:${color}"`:''}>${escapeHtml(playerInitials(p))}</span><span class="waiting-name">${escapeHtml(p.name)} <small class="waiting-rating" data-rating="${rating}" aria-label="${rating} star rating">${'★'.repeat(rating)}</small>${partner?`<small class="waiting-link">↔ ${escapeHtml(partner.name)}</small>`:''}</span>${unavailable?`<span class="availability-pill ${status}">${status}</span>`:`<span class="wait-time">${mins} min</span>`}<span class="waiting-record">${wins}-${losses} (${games})</span></button>`;};
        const participation=s.mode==='team'?`<section class="match-history" style="margin-top:1rem"><h3 class="match-history-title">Games per player · Required ${s.gamesPerPlayer}</h3><div class="summary-table"><div class="summary-table-head"><span>Player</span><span>Played</span><span>Required</span><span>Remaining</span><span>Status</span></div>${s.players.map(player=>{const played=player.gamesPlayed||0,remaining=Math.max(0,s.gamesPerPlayer-played),status=remaining===0?'Completed':playerStatusLabel(player);return `<div class="summary-player"><strong>${escapeHtml(player.name)}</strong><span>${played}</span><span>${s.gamesPerPlayer}</span><span>${remaining}</span><span class="availability-pill ${remaining===0?'completed':playerStatus(player)}">${status}</span></div>`;}).join('')}</div></section>`:'';
        const totalAppearances=s.players.length*s.gamesPerPlayer,totalMatches=totalAppearances/4,totalMatchesLabel=Number.isInteger(totalMatches)?totalMatches:totalMatches.toFixed(2);
        const matchupSummary=s.mode==='team'?`<section class="match-history" style="margin-top:1rem"><h3 class="match-history-title">Team matchup counts</h3><div class="summary-table"><div class="summary-table-head"><span style="grid-column:1/5">Matchup</span><span>Games</span></div>${Array.from({length:s.teamCount||0},(_,left)=>Array.from({length:(s.teamCount||0)-left-1},(_,offset)=>{const right=left+offset+1,key=matchupKey(left,right),leftName=s.teamNames?.[left]||teamPalette[left]?.[0]||`Team ${left+1}`,rightName=s.teamNames?.[right]||teamPalette[right]?.[0]||`Team ${right+1}`;return `<div class="summary-player"><strong style="grid-column:1/5">${escapeHtml(leftName)} vs ${escapeHtml(rightName)}</strong><span>${s.matchupCounts?.[key]||0}</span></div>`;})).flat().join('')}</div></section>`:'';
        const teamStandings=s.mode==='team'?Array.from({length:s.teamCount||0},(_,index)=>{const members=s.players.filter(player=>player.teamIndex===index),wins=Math.round(members.reduce((sum,player)=>sum+(player.wins||0),0)/2),losses=Math.round(members.reduce((sum,player)=>sum+(player.losses||0),0)/2);return {index,name:s.teamNames?.[index]||teamPalette[index]?.[0]||`Team ${index+1}`,games:wins+losses,wins,losses,points:wins};}).sort((a,b)=>b.points-a.points||b.wins-a.wins||a.losses-b.losses||a.name.localeCompare(b.name)):[];
        const standingsSummary=s.mode==='team'?`<section class="match-history" style="margin-top:1rem"><h3 class="match-history-title">Team standings</h3><div class="summary-table"><div class="summary-table-head"><span>Rank</span><span>Team</span><span>Games</span><span>Wins</span><span>Losses</span><span>Points</span></div>${teamStandings.map((team,index)=>`<div class="summary-player"><span>${index+1}</span><strong>${escapeHtml(team.name)}</strong><span>${team.games}</span><span>${team.wins}</span><span>${team.losses}</span><span>${team.points}</span></div>`).join('')}</div></section>`:'';
        const teamList=s.mode==='team'?`<div class="active-game-limit"><div><label class="section-label" for="active-games-per-player">Games per player</label><p class="hint">Updates every player’s required and remaining games.</p><div class="match-plan"><span><strong>Players:</strong> ${s.players.length}</span><span><strong>Games per player:</strong> ${s.gamesPerPlayer}</span><span><strong>Total matches required:</strong> ${totalMatchesLabel}</span></div></div><input class="field" id="active-games-per-player" type="number" min="1" max="20" step="1" value="${s.gamesPerPlayer}" aria-label="Games per player"></div><div class="team-rosters">${Array.from({length:s.teamCount||0},(_,index)=>{const members=s.players.filter(player=>player.teamIndex===index),name=s.teamNames?.[index]||teamPalette[index]?.[0]||`Team ${index+1}`;return `<section class="team-roster" style="--team-color:${teamColor(index)}"><div class="team-roster-head"><strong>${escapeHtml(name)}</strong><span class="team-roster-count">${members.length}/${s.teamSize||0} players</span></div>${members.length?`<div class="team-stats-head"><span>Player</span><span>Played</span><span>Required</span><span>Remaining</span><span>W-L</span><span>Status</span></div><div class="team-player-grid">${members.map(player=>{const played=player.gamesPlayed||0,remaining=Math.max(0,s.gamesPerPlayer-played),status=remaining===0?'Completed':playerStatusLabel(player);return `<button class="waiting-player" data-waiting-player="${player.id}" type="button"><span class="waiting-avatar">${escapeHtml(playerInitials(player))}</span><span class="waiting-name">${escapeHtml(player.name)}</span><span class="team-player-stats"><span>${played}</span><span>${s.gamesPerPlayer}</span><span>${remaining}</span><span>${player.wins||0}-${player.losses||0}</span><span class="availability-pill ${remaining===0?'completed':playerStatus(player)}">${status}</span></span></button>`;}).join('')}</div>`:'<p class="hint" style="padding:.7rem .85rem">No players assigned.</p>'}</section>`;}).join('')}</div>`:null;
        $('#waiting').innerHTML=teamList||`<div class="waiting"><div class="waiting-head"><span class="waiting-title">Waiting · ${readyPlayers.length}</span>${emptyCount&&readyCount>=4?`<button class="stage-all" id="stage-all">Stage all empty (${emptyCount})</button>`:''}</div>${readyPlayers.length?`<div class="waiting-columns"><span>Wait</span><span>W-L</span><span>(GP)</span></div><div class="waiting-list">${readyPlayers.map(p=>waitingRow(p)).join('')}</div>`:'<p class="waiting-empty">No ready players waiting.</p>'}${unavailablePlayers.length?`<section class="unavailable-section"><div class="waiting-head"><span class="waiting-title">Unavailable · ${unavailablePlayers.length}</span></div><div class="waiting-list">${unavailablePlayers.map(p=>waitingRow(p,true)).join('')}</div></section>`:''}</div>`;
        if(teamList){const remainingAppearances=s.players.reduce((sum,player)=>sum+Math.max(0,s.gamesPerPlayer-(player.gamesPlayed||0)),0),remainingMatches=remainingAppearances/4;$('.match-plan',$('#waiting'))?.insertAdjacentHTML('beforeend',`<span><strong>Remaining matches:</strong> ${Number.isInteger(remainingMatches)?remainingMatches:remainingMatches.toFixed(2)}</span>`);$('#waiting').insertAdjacentHTML('beforeend',standingsSummary+matchupSummary);$$('.team-player-grid .waiting-name').forEach(nameEl=>{const player=s.players.find(item=>item.name===nameEl.textContent.trim());if(!player)return;const lastQueued=playerCopies(player.id).map(copy=>new Date(copy.queuedAt||s.startedAt).getTime()).filter(Number.isFinite).reduce((latest,time)=>Math.max(latest,time),0),minutes=Math.max(0,Math.floor((Date.now()-lastQueued)/60000));nameEl.insertAdjacentHTML('beforeend',`<small>Waiting ${minutes} min</small>`);if((player.gamesPlayed||0)>=s.gamesPerPlayer)nameEl.closest('.waiting-player')?.classList.add('completed-player');});}
        const activeGameLimit=$('#active-games-per-player');if(activeGameLimit)activeGameLimit.onchange=()=>{const value=Number(activeGameLimit.value);if(!Number.isInteger(value)||value<1||value>20){activeGameLimit.value=s.gamesPerPlayer;return;}s.gamesPerPlayer=value;state.setup.gamesPerPlayer=value;save();renderActive();};
        $$('[data-waiting-player]').forEach(b=>b.onclick=()=>showPlayerActions(b.dataset.waitingPlayer));
        const stageAll=$('#stage-all');if(stageAll)stageAll.onclick=()=>{s.matches.filter(m=>!m.teamA?.length&&!m.manualSlots).forEach(m=>{if(s.waiting.filter(p=>(p.status||'ready')==='ready').length>=4){if(s.mode==='team')stageTeamCourtWithHistory(m);else stageCourt(m);}});};
        const history=[...(s.completedMatches||[])].reverse();
        $('#match-history').innerHTML=history.length?`<section class="match-history"><h3 class="match-history-title">Match history · ${history.length}</h3><div class="match-history-card">${history.map(item=>{const aWinner=item.winner==='a',bWinner=item.winner==='b',noWinner=!item.winner,noScore=Number(item.scoreA)===0&&Number(item.scoreB)===0;const scores=noWinner?'<span class="no-winner-label">No winner</span>':noScore?`<span class="${aWinner?'winner-mark':'loser-mark'}">${aWinner?'W':'—'}</span><span class="${bWinner?'winner-mark':'loser-mark'}">${bWinner?'W':'—'}</span>`:`<span class="${aWinner?'winning-score':''}">${item.scoreA}</span><span class="${bWinner?'winning-score':''}">${item.scoreB}</span>`;return `<div class="history-match ${noWinner?'no-winner-row':''}"><span class="history-round">R${item.round||1}</span><div class="history-teams"><span class="${aWinner?'winner':''}">${escapeHtml(item.teamA.join(' & '))}</span><span class="${bWinner?'winner':''}">${escapeHtml(item.teamB.join(' & '))}</span></div><div class="history-score">${scores}<span class="history-duration" title="Match duration">${matchDuration(item.startedAt,item.completedAt)}</span></div></div>`}).join('')}</div></section>`:'';
        if(s.mode==='team')$$('.history-match').forEach((row,index)=>{const item=history[index],teams=$$('.history-teams span',row),indices=[item.teamAIndex,item.teamBIndex],colors=indices.map(teamIndex=>teamPalette[teamIndex]?.[1]||'#1673d1');row.style.background=`linear-gradient(90deg,${colors[0]}24,${colors[1]}24)`;row.style.borderLeftColor=colors[0];indices.forEach((teamIndex,side)=>{if(teamIndex===undefined)return;const name=s.teamNames?.[teamIndex]||teamPalette[teamIndex]?.[0]||`Team ${teamIndex+1}`;teams[side].insertAdjacentHTML('afterbegin',`<small style="color:${colors[side]}">${escapeHtml(name)} · </small>`);});});
        const addCourtButton=$('#add-court');addCourtButton.disabled=s.matches.length>=8;addCourtButton.textContent=s.matches.length>=8?'Maximum 8 courts':'＋ Add court';
    }
    $('#add-court').onclick=addCourt;
    $('#add-more').onclick=()=>{updatingSession=true;state.players=[...state.current.players];renderPlayers();show('checkin');};
    function showTeamStandings(){const s=state.current;if(!s)return;const teams=Array.from({length:s.teamCount||0},(_,index)=>{const members=s.players.filter(player=>player.teamIndex===index),wins=Math.round(members.reduce((sum,player)=>sum+(player.wins||0),0)/2),losses=Math.round(members.reduce((sum,player)=>sum+(player.losses||0),0)/2);return {name:s.teamNames?.[index]||teamPalette[index]?.[0]||`Team ${index+1}`,games:wins+losses,wins,losses,points:wins};}).sort((a,b)=>b.points-a.points||b.wins-a.wins||a.losses-b.losses||a.name.localeCompare(b.name));standingsPanel.innerHTML=`<div class="standings-head"><div><h3 id="standings-title">Team Standings</h3><p>${escapeHtml(s.name)} · ${s.played||0} matches</p></div><button class="standings-close" id="standings-close" aria-label="Close">×</button></div><div class="summary-table"><div class="summary-table-head"><span>Rank</span><span>Team</span><span>Games</span><span>Wins</span><span>Losses</span><span>Points</span></div>${teams.map((team,index)=>`<div class="summary-player"><span>${index+1}</span><strong>${escapeHtml(team.name)}</strong><span>${team.games}</span><span>${team.wins}</span><span>${team.losses}</span><span>${team.points}</span></div>`).join('')}</div><div class="standings-actions"><button class="standings-back" id="standings-back">Back to Game</button></div>`;standingsDialog.hidden=false;$('#standings-close').onclick=hideStandings;$('#standings-back').onclick=hideStandings;}
    function decorateTeamStandings(){standingsPanel.classList.add('team-standings-large');const trophies=['🥇','🥈','🥉'];$$('.summary-player',standingsPanel).forEach((row,index)=>{const cells=[...row.children],games=Number(cells[2]?.textContent)||0,wins=Number(cells[3]?.textContent)||0,rate=games?Math.round(wins/games*100):0;if(cells[0])cells[0].textContent=`${trophies[index]||`#${index+1}`}`;if(cells[1])cells[1].insertAdjacentHTML('beforeend',`<small style="display:block;color:var(--muted);font-weight:650">${rate}% win rate</small>`);});}
    $('#show-standings').onclick=()=>{standingsPanel.classList.remove('team-standings-large');if(state.current?.mode==='team'){showTeamStandings();decorateTeamStandings();return;}showStandings();};
    function renderSessionSummary(){const s=state.summary;if(!s){show('home');return;}const players=[...(s.players||[])].sort((a,b)=>(b.wins||0)-(a.wins||0)||(b.gamesPlayed||0)-(a.gamesPlayed||0)||a.name.localeCompare(b.name)),matches=s.completedMatches||[],duration=Math.max(0,new Date(s.endedAt).getTime()-new Date(s.startedAt).getTime()),minutes=Math.floor(duration/60000),durationText=minutes>=60?`${Math.floor(minutes/60)}h ${minutes%60}m`:`${minutes}m`,withScore=matches.filter(m=>Number(m.scoreA)>0||Number(m.scoreB)>0).length,top=players.slice(0,3);const topRows=top.map((p,index)=>{const games=p.gamesPlayed||0,wins=p.wins||0,rate=games?Math.round(wins/games*100):0;return `<div class="standing-row top-${index+1}"><span class="standing-rank leader">${index+1}</span><div><div class="standing-name">${escapeHtml(p.name)} <span class="standing-stars">${'★'.repeat(playerRating(p))}</span></div><div class="standing-meta">${rate}% win rate · ${games} ${games===1?'game':'games'}</div></div><span class="standing-wins">${wins}<small>wins</small></span></div>`}).join('');const matchRows=[...matches].reverse().map(item=>{const aWinner=item.winner==='a',bWinner=item.winner==='b',noWinner=!item.winner,noScore=Number(item.scoreA)===0&&Number(item.scoreB)===0,scores=noWinner?'<span class="no-winner-label">No winner</span>':noScore?`<span class="${aWinner?'winner-mark':'loser-mark'}">${aWinner?'W':'—'}</span><span class="${bWinner?'winner-mark':'loser-mark'}">${bWinner?'W':'—'}</span>`:`<span class="${aWinner?'winning-score':''}">${item.scoreA}</span><span class="${bWinner?'winning-score':''}">${item.scoreB}</span>`;return `<div class="history-match ${noWinner?'no-winner-row':''}"><span class="history-round">R${item.round||1}</span><div class="history-teams"><span class="${aWinner?'winner':''}">${escapeHtml(item.teamA.join(' & '))}</span><span class="${bWinner?'winner':''}">${escapeHtml(item.teamB.join(' & '))}</span></div><div class="history-score">${scores}</div></div>`}).join('');$('#session-summary-content').innerHTML=`<h3 class="summary-name">${escapeHtml(s.name)}</h3><p class="summary-date">${new Date(s.startedAt).toLocaleString()}</p><div class="summary-metrics"><div class="summary-metric"><strong>${durationText}</strong><span>Duration</span></div><div class="summary-metric"><strong>${s.played||matches.length}</strong><span>Matches</span></div><div class="summary-metric"><strong>${withScore}</strong><span>With score</span></div><div class="summary-metric"><strong>${players.length}</strong><span>Players</span></div></div>${topRows?`<h3 class="summary-block-title">Top 3 players</h3><div class="standings-panel" style="width:100%;box-shadow:none;border:1px solid #dbe1e9">${topRows}</div>`:''}<h3 class="summary-block-title">Player stats</h3><div class="summary-table"><div class="summary-table-head"><span>#</span><span>Player</span><span>GP</span><span>W</span><span>L</span></div>${players.map((p,index)=>`<div class="summary-player"><span class="summary-player-number">${index+1}</span><strong>${escapeHtml(p.name)} <small class="summary-rating">${'★'.repeat(playerRating(p))}</small></strong><span>${p.gamesPlayed||0}</span><span>${p.wins||0}</span><span>${p.losses||0}</span></div>`).join('')}</div>${matchRows?`<h3 class="summary-block-title">Match history · ${matches.length}</h3><div class="match-history-card">${matchRows}</div>`:''}`;}
    function renderSummaryPlayerStats(){const table=$('.summary-table'),s=state.summary;if(!table||!s)return;const players=[...(s.players||[])].sort((a,b)=>(b.wins||0)-(a.wins||0)||(b.gamesPlayed||0)-(a.gamesPlayed||0)||a.name.localeCompare(b.name));table.innerHTML=`<div class="summary-table-head"><span>#</span><span>Player</span><span>Games</span><span>W</span><span>L</span><span>Win rate</span></div>${players.map((p,index)=>{const games=p.gamesPlayed||0,wins=p.wins||0,rate=games?Math.round(wins/games*100):0;return `<div class="summary-player"><span class="summary-player-number">${index+1}</span><strong>${escapeHtml(p.name)} <small class="summary-rating">${'★'.repeat(playerRating(p))}</small></strong><span>${games}</span><span>${wins}</span><span>${p.losses||0}</span><span>${rate}%</span></div>`}).join('')}`;}
    function renderTeamSummary(){const s=state.summary,content=$('#session-summary-content');if(!s||!content)return;const teams=Array.from({length:s.teamCount||0},(_,index)=>{const members=s.players.filter(player=>player.teamIndex===index),wins=Math.round(members.reduce((sum,player)=>sum+(player.wins||0),0)/2),losses=Math.round(members.reduce((sum,player)=>sum+(player.losses||0),0)/2);return {name:s.teamNames?.[index]||teamPalette[index]?.[0]||`Team ${index+1}`,wins,losses,points:wins};}).sort((a,b)=>b.points-a.points||b.wins-a.wins);const titles=$$('.summary-block-title',content);if(titles[0])titles[0].textContent='Top Teams';const topPanel=$('.standings-panel',content);if(topPanel)topPanel.innerHTML=teams.slice(0,3).map((team,index)=>`<div class="standing-row top-${index+1}"><span class="standing-rank leader">${['🥇','🥈','🥉'][index]}</span><div><div class="standing-name">${escapeHtml(team.name)}</div><div class="standing-meta">${team.wins} wins · ${team.losses} losses · ${team.points} points</div></div><span class="standing-wins">${team.points}<small>points</small></span></div>`).join('');if(titles[1])titles[1].textContent='Player Statistics';const table=$('.summary-table',content);if(table)table.innerHTML=`<div class="summary-table-head"><span>#</span><span>Player / Team</span><span>Games</span><span>W</span><span>L</span><span>Remaining</span></div>${s.players.map((player,index)=>{const team=s.teamNames?.[player.teamIndex]||teamPalette[player.teamIndex]?.[0]||'Unassigned',games=player.gamesPlayed||0;return `<div class="summary-player"><span>${index+1}</span><strong>${escapeHtml(player.name)} <small style="display:block;color:var(--muted)">${escapeHtml(team)}</small></strong><span>${games}</span><span>${player.wins||0}</span><span>${player.losses||0}</span><span>${Math.max(0,(s.gamesPerPlayer||0)-games)}</span></div>`;}).join('')}`;}
    function renderSummaryMatchDurations(){const matches=[...(state.summary?.completedMatches||[])].reverse(),scores=$$('#session-summary-content .history-score');scores.forEach((score,index)=>{const item=matches[index];if(!item||score.querySelector('.history-duration'))return;score.insertAdjacentHTML('beforeend',`<span class="history-duration" title="Match duration">${matchDuration(item.startedAt,item.completedAt)}</span>`);});}
    bindScoreButton('#score-team-a','a');bindScoreButton('#score-team-b','b');
    $('#match-cancel').onclick=()=>show('active');
    const endDialog=$('#end-dialog'),endDialogPanel=$('#end-dialog-panel');
    function hideEndDialog(){endDialog.hidden=true;endDialog.classList.remove('selector');}
    function confirmEndSession(){if(!state.current)return;const incomplete=state.current.players.filter(player=>(player.gamesPlayed||0)<state.current.gamesPerPlayer);if(incomplete.length){toast(`${incomplete.length} player${incomplete.length===1?' needs':'s need'} more games before the session can end`);return;}endDialog.classList.remove('selector');endDialogPanel.innerHTML=`<div class="end-dialog-copy"><h3 id="end-dialog-title">End session?</h3><p>Every player has completed ${state.current.gamesPerPlayer} games. You’ll see the complete session summary next.</p></div><div class="confirm-actions"><button id="cancel-end-session">Cancel</button><button id="confirm-end-session" style="color:#ff6b66">End session</button></div>`;endDialog.hidden=false;$('#cancel-end-session').onclick=hideEndDialog;$('#confirm-end-session').onclick=finishSession;}
    function finishSession(){const session=structuredClone(state.current);session.id=session.id||crypto.randomUUID();session.endedAt=new Date().toISOString();state.summary=session;if(state.keepHistory){state.history.unshift(session);archiveSession(session);}state.current=null;state.players=[];state.setup={...initial.setup};$('#session-name').value='';renderSetup();renderPlayers();hideEndDialog();save();show('session-summary');}
    $('#end-session').onclick=confirmEndSession;
    $('#summary-done').onclick=()=>show('home');
    function closeOpenMatch(record,winner=null){const s=state.current,m=currentOpenMatch();if(!s||!m)return;const completedAt=new Date().toISOString();if(record){s.played=(s.played||0)+1;const winningTeam=winner==='a'?m.teamA:winner==='b'?m.teamB:[],losingTeam=winner==='a'?m.teamB:winner==='b'?m.teamA:[],bump=(player,field)=>{const canonical=s.players.find(p=>p.id===player.id);[...new Set([player,canonical].filter(Boolean))].forEach(p=>p[field]=(p[field]||0)+1);};[...m.teamA,...m.teamB].forEach(p=>bump(p,'gamesPlayed'));winningTeam.forEach(p=>bump(p,'wins'));losingTeam.forEach(p=>bump(p,'losses'));s.completedMatches=s.completedMatches||[];s.completedMatches.push({court:m.court,round:s.round,teamA:m.teamA.map(p=>p.name),teamB:m.teamB.map(p=>p.name),teamAIndex:m.teamAIndex,teamBIndex:m.teamBIndex,scoreA:m.scoreA||0,scoreB:m.scoreB||0,winner,startedAt:m.startedAt,completedAt});}const queuedAt=completedAt;[...m.teamA,...m.teamB].forEach(p=>p.queuedAt=queuedAt);s.waiting.push(...m.teamA,...m.teamB);m.teamA=[];m.teamB=[];m.started=false;m.startedAt=null;m.completedAt=null;m.scoreA=0;m.scoreB=0;m.done=false;openMatchId=null;hideEndDialog();save();show('active');}
    function requestEndMatch(){const m=currentOpenMatch();if(!m)return;const scoreA=m.scoreA||0,scoreB=m.scoreB||0;if(scoreA!==scoreB){const winner=scoreA>scoreB?'a':'b',team=winner==='a'?m.teamA:m.teamB;endDialog.classList.remove('selector');endDialogPanel.innerHTML=`<div class="end-dialog-copy"><h3 id="end-dialog-title">End match?</h3><p>Final score: ${scoreA} – ${scoreB} · ${escapeHtml(team.map(p=>p.name).join(' & '))} wins</p></div><div class="confirm-actions"><button id="end-dialog-cancel">Cancel</button><button id="end-dialog-confirm">End match</button></div>`;endDialog.hidden=false;$('#end-dialog-cancel').onclick=hideEndDialog;$('#end-dialog-confirm').onclick=()=>closeOpenMatch(true,winner);return;}endDialog.classList.add('selector');endDialogPanel.innerHTML=`<div class="end-dialog-copy"><h3 id="end-dialog-title">How did this match end?</h3><p>${scoreA===0?'No score was entered.':'The scores are tied.'} Pick a winner or skip.</p></div><div class="winner-options"><button data-winner="a">${escapeHtml(m.teamA.map(p=>p.name).join(' & '))} won</button><button data-winner="b">${escapeHtml(m.teamB.map(p=>p.name).join(' & '))} won</button><button class="no-winner" id="no-winner">End without a winner</button></div><div class="winner-cancel-wrap"><button class="winner-cancel" id="winner-cancel">Cancel</button></div>`;endDialog.hidden=false;$$('[data-winner]',endDialogPanel).forEach(b=>b.onclick=()=>closeOpenMatch(true,b.dataset.winner));$('#no-winner').onclick=()=>closeOpenMatch(true,null);$('#winner-cancel').onclick=hideEndDialog;}
    $('#end-unrecorded').onclick=()=>closeOpenMatch(false);$('#end-recorded').onclick=requestEndMatch;
    endDialog.onclick=e=>{if(e.target===endDialog)hideEndDialog();};
    function renderHistory(){const list=[...(state.current?[state.current]:[]),...state.history];$('#history-list').innerHTML=list.length?list.map((s,i)=>{const active=i===0&&state.current,historyIndex=i-(state.current?1:0);return `<article class="session-card"><span class="section-label" style="margin:0">${active?'In progress':ago(s.startedAt)}</span><h3>${escapeHtml(s.name)}</h3><p class="meta">${s.players.length} players · ${s.courts} courts · ${s.played||s.completedMatches?.length||0} matches</p><button class="card-link" ${active?'data-open-active':`data-open-summary="${historyIndex}"`}>${active?'Open session':'View summary'} →</button></article>`}).join(''):'<p class="empty">No sessions yet.</p>';const openActive=$('[data-open-active]');if(openActive)openActive.onclick=()=>show('active');$$('[data-open-summary]').forEach(b=>b.onclick=()=>{const session=state.history[Number(b.dataset.openSummary)];if(!session)return;if(!session.endedAt)session.endedAt=session.startedAt;state.summary=session;save();show('session-summary');});}
    $('#history-toggle').classList.toggle('on',state.keepHistory);$('#history-toggle').onclick=()=>{state.keepHistory=!state.keepHistory;$('#history-toggle').classList.toggle('on',state.keepHistory);save();};
    $('#clear-data').onclick=()=>{if(confirm('Clear all Capikol sessions and players?'))fetch({{ Illuminate\Support\Js::from(route('open-play.data.clear')) }},{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrfToken}}).then(response=>{if(!response.ok)throw new Error('Unable to clear Open Play data');state=structuredClone(initial);location.reload();}).catch(()=>toast('Open Play data could not be cleared'));};
    setInterval(()=>{updateMatchTimers();updateMatchDetailClock();},1000);
    renderSetup();renderSkills();renderPlayers();renderHome();show(state.current&&state.screen==='active'?'active':(state.screen==='session-summary'&&state.summary?'session-summary':(state.screen==='history'||state.screen==='settings'?state.screen:'home')));
})();
</script>
</body>
</html>
