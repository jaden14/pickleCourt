<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f6f7fb">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PB Queue · Open Play</title>
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
        .court-avatar { display:grid; width:30px; height:30px; margin:.08rem 0 .15rem; place-items:center; border:2px solid rgb(255 255 255 / .82); border-radius:50%; background:#ef5350; color:#fff; font-size:.65rem; font-weight:850; box-shadow:0 2px 5px rgb(31 70 40 / .18); }
        .court-slot:nth-child(2) .court-avatar { background:#21b86b; }
        .court-slot:nth-child(3) .court-avatar { background:#50bec1; }
        .court-slot:nth-child(4) .court-avatar { background:#f0821d; }
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
        .score-team { width:100%; min-height:clamp(160px,27dvh,230px); padding:1rem; border:1px solid #dce2ea; border-radius:16px; background:#fff; cursor:pointer; }
        .score-team.leading { border:2px solid #5d9df2; }
        .score-team strong { display:block; font-size:1.05rem; }
        .score-number { display:block; margin-top:.3rem; font-size:5rem; font-weight:500; line-height:1; font-variant-numeric:tabular-nums; }
        .score-divider { display:flex; align-items:center; gap:.5rem; margin:.55rem 0; color:#a0a9b7; font-size:.62rem; text-transform:uppercase; }
        .score-divider:before,.score-divider:after { content:""; height:1px; flex:1; background:#e2e6ec; }
        .score-hint { margin:.65rem 0 0; color:#8a95a6; text-align:center; font-size:.65rem; }
        .match-detail-actions { display:grid; grid-template-columns:1fr 1.25fr; gap:.65rem; margin-top:1rem; padding-top:.65rem; border-top:1px solid #e4e8ee; }
        .match-detail-actions button { min-height:50px; border:0; border-radius:12px; cursor:pointer; font-weight:750; }
        .end-unrecorded { background:transparent; color:#5e697a; }
        .end-recorded { background:var(--blue); color:#fff; }
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
        @media (min-width:760px) { .app { width:min(100%,840px); box-shadow:0 0 50px rgb(25 39 64 / .12); } .screen { padding-inline:3.75rem; } .court-grid { grid-template-columns:repeat(2,1fr); } .sticky-action { right:calc((100vw - min(100vw,840px))/2); left:calc((100vw - min(100vw,840px))/2); padding-inline:3.75rem; } }
        @media (max-width:370px) { .screen { padding-inline:1rem; } .skills { gap:.25rem; } .skill { font-size:.78rem; } }
    </style>
</head>
<body>
<main class="app">
    <section class="screen active" data-screen="home">
        <div class="content home-content">
            <div class="home-actions"><a class="dashboard-back" href="/admin" aria-label="Back to dashboard">← Back to dashboard</a><button class="theme-toggle" id="theme-toggle" type="button" aria-label="Switch to dark theme">☾</button></div>
            <div class="brand"><span class="ball" aria-hidden="true"></span> PICKLEBALL</div>
            <h1>PB Queue</h1>
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
            <span class="section-label">Courts</span>
            <div class="stepper"><button class="circle-button" id="court-minus">−</button><output class="stepper-output" id="court-count">2</output><button class="circle-button" id="court-plus">＋</button></div>
            <span class="section-label">Matchmaking</span>
            <button class="option selected" data-mode="balanced" type="button" role="radio" aria-checked="true"><strong>Balanced</strong><small>Even teams, courts grouped by player rating. Recording a winner is optional.</small><span class="radio" aria-hidden="true"></span></button>
            <button class="option" data-mode="winners" type="button" role="radio" aria-checked="false"><strong>Winners & Losers</strong><small>Winners play winners, losers play losers. A winner is required each game.</small><span class="radio" aria-hidden="true"></span></button>
            <p class="meta">You can switch this any time during the session.</p>
            <span class="section-label">Game format</span>
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
                <div class="skill-head"><span class="section-label">Star rating</span><button class="help" id="skill-help">ⓘ What do these mean?</button></div>
                <div class="skills" id="skills"></div>
                <button class="add-button" id="add-player" disabled>Add player</button>
                <button class="text-button" id="bulk-toggle">＋ Bulk paste</button>
                <div id="bulk-area" hidden><textarea id="bulk-input" placeholder="One player name per line:&#10;Alex&#10;Jordan&#10;Casey"></textarea><p class="hint">All pasted players will receive the selected <span id="default-skill">4-star</span> rating.</p><button class="add-button" id="add-list" disabled>Add list</button></div>
            </div>
            <div class="players" id="player-list"></div>
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
            <div id="waiting"></div>
            <div id="match-history"></div>
        </div>
    </section>

    <section class="screen" data-screen="match-detail">
        <div class="match-detail-content">
            <header class="match-detail-head"><div><h2 id="detail-court">Court</h2><p id="detail-round">Round 1</p></div><button class="match-cancel" id="match-cancel" type="button">Cancel</button></header>
            <div class="match-clock" id="match-clock">0:00</div>
            <p class="match-target" id="match-target">Game to 11</p>
            <button class="score-team" id="score-team-a" type="button"><strong id="team-a-names"></strong><span class="score-number" id="score-a">0</span></button>
            <div class="score-divider">vs</div>
            <button class="score-team" id="score-team-b" type="button"><strong id="team-b-names"></strong><span class="score-number" id="score-b">0</span></button>
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
    <section class="screen" data-screen="settings"><div class="content"><header class="topbar"><button class="back" data-go="home">‹</button><div><h2>Settings</h2><p>PB Queue preferences</p></div></header><div class="form-card"><div class="row-between"><div><strong>Keep session history</strong><p class="meta">Saved to your account and available across browsers.</p></div><button class="toggle on" id="history-toggle"></button></div><p class="hint" style="margin-top:1rem">Your active tournament, players, courts, results, standings, and preferences are securely stored in the system database.</p><button class="text-button" id="clear-data" style="color:#c2413c;margin-top:2rem">Clear all PB Queue data</button></div></div></section>
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
    const initial = { screen:'home', setup:{ name:'', courts:2, mode:'balanced', points:11, pointsOn:true, timer:false }, players:[], rating:4, current:null, history:[], keepHistory:true };
    const databaseState = {{ Illuminate\Support\Js::from($openPlayState ?? null) }};
    let state = {...initial,...(databaseState||{})};
    state.setup = { ...initial.setup, ...(state.setup || {}) };
    state.setup.name = typeof state.setup.name === 'string' ? state.setup.name : '';
    state.setup.courts = Math.min(8, Math.max(1, Number(state.setup.courts) || initial.setup.courts));
    state.players = state.players || []; state.history = state.history || [];
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
    const save = () => {clearTimeout(saveTimer);saveTimer=setTimeout(()=>{const persistedState={...state,history:[]};fetch({{ Illuminate\Support\Js::from(route('open-play.state.save')) }},{method:'PUT',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({state:persistedState})}).then(response=>{if(!response.ok)throw new Error('Unable to save Open Play');}).catch(()=>toast('Open Play changes could not be saved'));},250);};
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
        if(name==='home') renderHome(); if(name==='history') renderHistory(); if(name==='active') renderActive(); if(name==='match-detail') renderMatchDetail(); if(name==='session-summary'){renderSessionSummary();renderSummaryPlayerStats();renderSummaryMatchDurations();}
    }
    $$('[data-go]').forEach(b=>b.addEventListener('click',()=>show(b.dataset.go)));
    $$('[data-tab]').forEach(b=>b.addEventListener('click',()=>show(b.dataset.tab)));
    function renderHome() {
        const sessions=state.history||[],totalMatches=sessions.reduce((sum,s)=>sum+(s.played||s.completedMatches?.length||0),0),totalPlayers=sessions.reduce((sum,s)=>sum+(s.players?.length||0),0);
        $('#queue-stats').innerHTML=sessions.length?`<section class="queue-stats"><h3>Your PB Queue</h3><div class="queue-stat-grid"><div><strong>${sessions.length}</strong><span>${sessions.length===1?'session':'sessions'}</span></div><div><strong>${totalMatches}</strong><span>${totalMatches===1?'match':'matches'}</span></div><div><strong>${totalPlayers}</strong><span>players</span></div></div></section>`:'';
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
    function renderSetup(){
        if(!['balanced','winners'].includes(state.setup.mode)) state.setup.mode='balanced';
        $('#court-count').value=$('#court-count').textContent=state.setup.courts;
        $$('.option').forEach(o=>{const selected=o.dataset.mode===state.setup.mode;o.classList.toggle('selected',selected);o.setAttribute('aria-checked',String(selected));});
    }
    $('#court-minus').onclick=()=>{state.setup.courts=Math.max(1,state.setup.courts-1);renderSetup();save();};
    $('#court-plus').onclick=()=>{state.setup.courts=Math.min(8,state.setup.courts+1);renderSetup();save();};
    $$('.option').forEach(o=>o.onclick=()=>{state.setup.mode=o.dataset.mode;renderSetup();save();});
    function bindToggle(id,field){ const el=$(id); el.classList.toggle('on',state.setup[field]); el.onclick=()=>{state.setup[field]=!state.setup[field];el.classList.toggle('on',state.setup[field]);if(field==='pointsOn')$('#point-pills').hidden=!state.setup[field];save();}; }
    bindToggle('#point-toggle','pointsOn'); bindToggle('#timer-toggle','timer'); $('#point-pills').hidden=!state.setup.pointsOn;
    $$('#point-pills .pill').forEach(p=>{p.classList.toggle('selected',+p.textContent===state.setup.points);p.onclick=()=>{state.setup.points=+p.textContent;$$('#point-pills .pill').forEach(x=>x.classList.toggle('selected',x===p));save();};});
    $('#continue-checkin').onclick=()=>show('checkin');
    const ratings=[1,2,3,4,5];
    function renderSkills(){ $('#default-skill').textContent=`${state.rating}-star`; $('#skills').innerHTML=ratings.map(r=>`<button class="skill ${r===state.rating?'selected':''}" data-rating="${r}" aria-label="${r} star rating">${r}<small>${'★'.repeat(r)}</small></button>`).join(''); $$('#skills .skill').forEach(b=>b.onclick=()=>{state.rating=+b.dataset.rating;renderSkills();save();}); }
    const cleanPlayerName = name => name.trim().replace(/\s+/g,' ');
    const playerNameKey = name => cleanPlayerName(name).toLocaleLowerCase();
    const playerNameExists = name => state.players.some(player=>playerNameKey(player.name)===playerNameKey(name));
    const updateAdd=()=>$('#add-player').disabled=!$('#player-name').value.trim();
    $('#player-name').oninput=updateAdd; $('#player-name').onkeydown=e=>{if(e.key==='Enter'&&!$('#add-player').disabled)addPlayer();};
    function addPlayer(){ const input=$('#player-name'),name=cleanPlayerName(input.value);if(!name)return;if(playerNameExists(name)){toast(`${name} is already added`);input.focus();input.select();return;}state.players.push({id:crypto.randomUUID(),name,rating:state.rating});input.value='';updateAdd();renderPlayers();save(); }
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
    $('#add-list').onclick=()=>{const names=$('#bulk-input').value.split(/\n/).map(cleanPlayerName).filter(Boolean),seen=new Set(state.players.map(p=>playerNameKey(p.name))),added=[];names.forEach(name=>{const key=playerNameKey(name);if(seen.has(key))return;seen.add(key);added.push({id:crypto.randomUUID(),name,rating:state.rating});});state.players.push(...added);$('#bulk-input').value='';$('#add-list').disabled=true;renderPlayers();save();const skipped=names.length-added.length;toast(`${added.length} added${skipped?` · ${skipped} duplicate${skipped===1?'':'s'} skipped`:''}`); };
    const playerRating = player => Math.round(Math.min(5, Math.max(1, Number(player.rating ?? player.skill ?? 4))));
    const playerInitials = player => player.name.trim().split(/\s+/).slice(0,2).map(part=>part[0]).join('').toUpperCase();
    function renderPlayers(){ $('#player-list').innerHTML=state.players.map(p=>`<div class="player"><span class="avatar">${escapeHtml(p.name[0].toUpperCase())}</span><span class="player-info"><strong>${escapeHtml(p.name)}</strong><small><span class="rating-stars" aria-hidden="true">${'★'.repeat(playerRating(p))}<span class="empty-star">${'☆'.repeat(5-playerRating(p))}</span></span> · ${playerRating(p)}-star rating</small></span><button class="remove" data-id="${p.id}" aria-label="Remove ${escapeHtml(p.name)}">×</button></div>`).join(''); $$('.remove').forEach(b=>b.onclick=()=>{state.players=state.players.filter(p=>p.id!==b.dataset.id);renderPlayers();save();}); const need=Math.max(0,4-state.players.length),start=$('#start-session');start.disabled=need>0;start.textContent=updatingSession?'Update session':(need?`Need ${need} more ready`:'Start session'); }
    function shuffled(list){ return [...list].sort(()=>Math.random()-.5); }
    const emptyCourts = courts => Array.from({length:courts},(_,i)=>({id:crypto.randomUUID(),court:i+1,teamA:[],teamB:[],started:false,done:false}));
    $('#start-session').onclick=()=>{ if(updatingSession&&state.current){const existingIds=new Set(state.current.players.map(p=>p.id));const added=state.players.filter(p=>!existingIds.has(p.id)).map(p=>({...p,queuedAt:new Date().toISOString()}));state.current.players.push(...added);state.current.waiting.push(...added);updatingSession=false;save();show('active');if(added.length)toast(`${added.length} ${added.length===1?'player':'players'} added to waiting`);return;}if(state.players.length<4){toast(`Add ${4-state.players.length} more ready ${state.players.length===3?'player':'players'}`);return;}const mode=['balanced','winners'].includes(state.setup.mode)?state.setup.mode:'balanced',sessionName=String(state.setup.name??'').trim()||'Open Play',queuedAt=new Date().toISOString(),players=state.players.map(p=>({...p,queuedAt}));state.setup.mode=mode;state.setup.name=String(state.setup.name??'');state.current={...state.setup,id:crypto.randomUUID(),mode,name:sessionName,players,startedAt:queuedAt,round:1,played:0,matches:emptyCourts(state.setup.courts),waiting:[...players]};save();show('active'); };
    function stageCourt(match){ const s=state.current,available=s.waiting.filter(p=>(p.status||'ready')==='ready');if(match.teamA.length||match.manualSlots||available.length<4)return;const ordered=s.mode==='balanced'?[...available].sort((a,b)=>playerRating(b)-playerRating(a)):shuffled(available),four=[];for(const player of ordered){if(four.length>=4||four.some(p=>p.id===player.id))continue;const partner=ordered.find(p=>p.id===player.partnerId);if(partner&&four.length<=2&&!four.some(p=>p.id===partner.id))four.push(player,partner);else four.push(player);}four.splice(4);const pair=four.filter(p=>four.some(x=>x.id===p.partnerId));if(pair.length>=2){const first=pair[0],partner=four.find(p=>p.id===first.partnerId),rest=four.filter(p=>p.id!==first.id&&p.id!==partner.id);match.teamA=[first,partner];match.teamB=rest;}else{match.teamA=[four[0],four[3]];match.teamB=[four[1],four[2]];}const ids=new Set(four.map(p=>p.id));s.waiting=s.waiting.filter(p=>!ids.has(p.id));match.started=false;match.startedAt=null;match.completedAt=null;match.done=false;save();renderActive(); }
    function unstageCourt(match){ const s=state.current;const queuedAt=new Date().toISOString();[...match.teamA,...match.teamB].forEach(p=>p.queuedAt=queuedAt);s.waiting.push(...match.teamA,...match.teamB);match.teamA=[];match.teamB=[];match.started=false;match.startedAt=null;match.completedAt=null;match.done=false;save();renderActive(); }
    function shuffleCourt(match){ const four=shuffled([...match.teamA,...match.teamB]);match.teamA=[four[0],four[1]];match.teamB=[four[2],four[3]];save();renderActive(); }
    function preferredAnnouncementVoice(){const voices=speechSynthesis.getVoices(),english=voices.filter(voice=>voice.lang?.toLowerCase().startsWith('en'));return english.find(voice=>/google.*(us|uk).*english|microsoft.*(aria|jenny|zira|susan)|samantha|serena/i.test(voice.name))||english.find(voice=>voice.default)||english[0]||voices.find(voice=>voice.default)||null;}
    function callPlayers(match){const teamA=match.teamA.map(p=>p.name).join(', and '),teamB=match.teamB.map(p=>p.name).join(', and '),message=`${teamA}. Versus. ${teamB}. Please proceed to Court ${match.court}.`;toast(`Calling Court ${match.court}`);if('speechSynthesis'in window&&'SpeechSynthesisUtterance'in window){speechSynthesis.cancel();const announcement=new SpeechSynthesisUtterance(message),voice=preferredAnnouncementVoice();if(voice){announcement.voice=voice;announcement.lang=voice.lang;}else announcement.lang='en-US';announcement.rate=.9;announcement.pitch=1;speechSynthesis.speak(announcement);}else toast(message);}
    function courtPlayer(player){ return `<div class="court-slot"><span class="court-slot-rating">${playerRating(player)}.0</span><span class="court-avatar">${escapeHtml(playerInitials(player))}</span><span class="court-player-name">${escapeHtml(player.name)}</span></div>`; }
    function editableCourtPlayer(player,index,matchId){return `<button class="manual-slot court-slot" data-edit-staged-slot="${index}" data-edit-staged-match="${matchId}" type="button" aria-label="Change ${escapeHtml(player.name)}"><span class="court-slot-rating">${playerRating(player)} ★</span><span class="court-avatar">${escapeHtml(playerInitials(player))}</span><span class="court-player-name">${escapeHtml(player.name)}</span></button>`;}
    function manualSlot(player,index,matchId){return `<button class="manual-slot court-slot" data-pick-slot="${index}" data-pick-match="${matchId}" type="button" aria-label="${player?`Change ${escapeHtml(player.name)}`:'Pick a player'}">${player?`<span class="court-slot-rating">${playerRating(player)} ★</span><span class="court-avatar">${escapeHtml(playerInitials(player))}</span><span class="court-player-name">${escapeHtml(player.name)}</span>`:'<span class="court-slot-rating"></span><span class="empty-slot-circle">＋</span><span class="empty-slot-label">Empty</span>'}</button>`;}
    function beginManual(match){match.manualSlots=[null,null,null,null];save();renderActive();}
    function finalizeManual(match){if(!match.manualSlots?.every(Boolean))return;match.teamA=[match.manualSlots[0],match.manualSlots[1]];match.teamB=[match.manualSlots[2],match.manualSlots[3]];delete match.manualSlots;match.started=false;match.done=false;save();renderActive();}
    function cancelManual(match){const queuedAt=new Date().toISOString();(match.manualSlots||[]).filter(Boolean).forEach(p=>{p.queuedAt=queuedAt;state.current.waiting.push(p);});delete match.manualSlots;save();renderActive();}
    function autoFillManual(match){const empty=match.manualSlots.map((p,i)=>p?null:i).filter(i=>i!==null),available=state.current.waiting.filter(p=>(p.status||'ready')==='ready');if(available.length<empty.length)return;const ordered=state.current.mode==='balanced'?[...available].sort((a,b)=>playerRating(b)-playerRating(a)):shuffled(available);const picked=ordered.slice(0,empty.length),ids=new Set(picked.map(p=>p.id));state.current.waiting=state.current.waiting.filter(p=>!ids.has(p.id));empty.forEach((slot,i)=>match.manualSlots[slot]=picked[i]);finalizeManual(match);}
    function removeCourt(match){const s=state.current;if(s.matches.length<=1||match.teamA.length)return;if(match.manualSlots)cancelManual(match);s.matches=s.matches.filter(m=>m.id!==match.id);s.matches.forEach((m,i)=>m.court=i+1);s.courts=s.matches.length;state.setup.courts=s.courts;save();renderActive();}
    function addCourt(){const s=state.current;if(!s||s.matches.length>=8)return;s.matches.push({id:crypto.randomUUID(),court:s.matches.length+1,teamA:[],teamB:[],started:false,done:false});s.courts=s.matches.length;state.setup.courts=s.courts;save();renderActive();toast(`Court ${s.courts} added`);}
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
    function playerCopies(id){const s=state.current;if(!s)return[];const copies=[s.players.find(p=>p.id===id),s.waiting.find(p=>p.id===id),...s.matches.flatMap(m=>[...(m.teamA||[]),...(m.teamB||[]),...(m.manualSlots||[])]).filter(p=>p?.id===id)];return [...new Set(copies.filter(Boolean))];}
    function updatePlayer(id,changes){playerCopies(id).forEach(p=>Object.assign(p,changes));}
    function hidePlayerActions(){playerActionSheet.hidden=true;actionPlayerId=null;}
    function showPlayerActions(playerId){const p=sessionPlayer(playerId)||state.current?.waiting.find(x=>x.id===playerId);if(!p)return;actionPlayerId=playerId;const partner=sessionPlayer(p.partnerId),status=p.status||'ready';playerActionPanel.innerHTML=`<div class="player-action-head"><h3 id="player-action-title">${escapeHtml(p.name)}</h3><p>${'★'.repeat(playerRating(p))} · ${status}${partner?` · ↔ ${escapeHtml(partner.name)}`:''}</p></div><button class="player-action" id="link-partner">${partner?`Unlink from ${escapeHtml(partner.name)}`:'Link with partner…'}</button><button class="player-action" data-player-status="${status==='resting'?'ready':'resting'}">${status==='resting'?'Mark as ready':'Mark as resting'}</button><button class="player-action" data-player-status="${status==='away'?'ready':'away'}">${status==='away'?'Mark as ready':'Mark as away'}</button><button class="player-action danger" id="remove-session-player">Remove from session</button><div class="picker-cancel-wrap"><button class="winner-cancel" id="player-action-cancel">Cancel</button></div>`;playerActionSheet.hidden=false;$('#link-partner').onclick=()=>partner?unlinkPlayers(p,partner):showPartnerPicker(p);$$('[data-player-status]',playerActionPanel).forEach(b=>b.onclick=()=>{updatePlayer(p.id,{status:b.dataset.playerStatus});hidePlayerActions();save();renderActive();});$('#remove-session-player').onclick=()=>removeSessionPlayer(p);$('#player-action-cancel').onclick=hidePlayerActions;}
    function unlinkPlayers(player,partner){updatePlayer(player.id,{partnerId:null});updatePlayer(partner.id,{partnerId:null});hidePlayerActions();save();renderActive();}
    function showPartnerPicker(player){const candidates=state.current.players.filter(p=>p.id!==player.id);playerActionPanel.innerHTML=`<div class="player-action-head"><h3 id="player-action-title">Link ${escapeHtml(player.name)}</h3><p>Linked players are kept on the same team when both are staged.</p></div><span class="picker-label">Pick a partner · ${candidates.length}</span>${candidates.map(p=>`<button class="picker-player" data-link-player="${p.id}"><span class="waiting-avatar">${escapeHtml(playerInitials(p))}</span><span><strong>${escapeHtml(p.name)}</strong><small>${'★'.repeat(playerRating(p))}${p.partnerId?` · linked to ${escapeHtml(sessionPlayer(p.partnerId)?.name||'player')}`:''}</small></span></button>`).join('')}<div class="picker-cancel-wrap"><button class="winner-cancel" id="partner-cancel">Cancel</button></div>`;$$('[data-link-player]',playerActionPanel).forEach(b=>b.onclick=()=>linkPlayers(player,sessionPlayer(b.dataset.linkPlayer)));$('#partner-cancel').onclick=()=>showPlayerActions(player.id);}
    function linkPlayers(player,partner){if(!partner)return;const oldA=sessionPlayer(player.partnerId),oldB=sessionPlayer(partner.partnerId);if(oldA)updatePlayer(oldA.id,{partnerId:null});if(oldB)updatePlayer(oldB.id,{partnerId:null});updatePlayer(player.id,{partnerId:partner.id});updatePlayer(partner.id,{partnerId:player.id});hidePlayerActions();save();renderActive();toast(`${player.name} linked with ${partner.name}`);}
    function removeSessionPlayer(player){const s=state.current,partner=sessionPlayer(player.partnerId);if(partner)updatePlayer(partner.id,{partnerId:null});s.waiting=s.waiting.filter(p=>p.id!==player.id);s.players=s.players.filter(p=>p.id!==player.id);state.players=state.players.filter(p=>p.id!==player.id);hidePlayerActions();save();renderActive();toast(`${player.name} removed`);}
    playerActionSheet.onclick=e=>{if(e.target===playerActionSheet)hidePlayerActions();};
    const standingsDialog=$('#standings-dialog'),standingsPanel=$('#standings-panel');
    function hideStandings(){standingsDialog.hidden=true;}
    function showStandings(){const s=state.current;if(!s)return;const playingIds=new Set(s.matches.flatMap(m=>[...(m.teamA||[]),...(m.teamB||[]),...(m.manualSlots||[])]).filter(Boolean).map(p=>p.id));const players=[...s.players].sort((a,b)=>{const gamesA=a.gamesPlayed||0,gamesB=b.gamesPlayed||0,rateA=gamesA?(a.wins||0)/gamesA:0,rateB=gamesB?(b.wins||0)/gamesB:0;return (b.wins||0)-(a.wins||0)||rateB-rateA||gamesB-gamesA||a.name.localeCompare(b.name);});standingsPanel.innerHTML=`<div class="standings-head"><div><h3 id="standings-title">Live Standings</h3><p>${escapeHtml(s.name)} · ${s.played||0} ${(s.played||0)===1?'game':'games'}</p></div><button class="standings-close" id="standings-close" aria-label="Close">×</button></div><div class="standings-note">Provisional · fewer than 5 games</div><div>${players.map((p,index)=>{const games=p.gamesPlayed||0,wins=p.wins||0,rate=games?Math.round(wins/games*100):0,playing=playingIds.has(p.id),status=playing?'playing':p.status||'ready',qualified=games>=5,isLeader=index<3;const badge=status==='ready'?'':`<span class="standing-status ${status}">${status[0].toUpperCase()+status.slice(1)}</span>`;return `<div class="standing-row ${isLeader?`top-${index+1}`:''}"><span class="standing-rank ${isLeader?'leader':qualified?'qualified':''}">${isLeader?index+1:qualified?index+1:'–'}</span><div><div class="standing-name">${escapeHtml(p.name)} <span class="standing-stars">${'★'.repeat(playerRating(p))}</span>${badge}</div><div class="standing-meta">${rate}% win rate · ${games} ${games===1?'game':'games'}${qualified?'':` · ${Math.max(0,5-games)} more to qualify`}</div></div><span class="standing-wins">${wins}<small>wins</small></span></div>`}).join('')}</div><div class="standings-actions"><button class="standings-back" id="standings-back">Back to Game</button></div>`;standingsDialog.hidden=false;$('#standings-close').onclick=hideStandings;$('#standings-back').onclick=hideStandings;}
    standingsDialog.onclick=e=>{if(e.target===standingsDialog)hideStandings();};
    const currentOpenMatch = () => state.current?.matches.find(m=>m.id===openMatchId);
    function updateMatchDetailClock(){ const m=currentOpenMatch();if(!m?.startedAt)return;const seconds=Math.max(0,Math.floor(((m.completedAt?new Date(m.completedAt).getTime():Date.now())-new Date(m.startedAt).getTime())/1000));const clock=$('#match-clock');if(clock)clock.textContent=`${Math.floor(seconds/60)}:${String(seconds%60).padStart(2,'0')}`; }
    function renderMatchDetail(){ const m=currentOpenMatch(),s=state.current;if(!m||!s){show('active');return;}$('#detail-court').textContent=`Court ${m.court}`;$('#detail-round').textContent=`Round ${s.round}`;$('#match-target').textContent=s.pointsOn?`Game to ${s.points}`:'Open score';$('#team-a-names').textContent=m.teamA.map(p=>p.name).join(' · ');$('#team-b-names').textContent=m.teamB.map(p=>p.name).join(' · ');$('#score-a').textContent=m.scoreA||0;$('#score-b').textContent=m.scoreB||0;$('#score-team-a').classList.toggle('leading',(m.scoreA||0)>(m.scoreB||0));$('#score-team-b').classList.toggle('leading',(m.scoreB||0)>(m.scoreA||0));updateMatchDetailClock(); }
    function changeScore(team,amount){ const m=currentOpenMatch();if(!m||m.done)return;const key=team==='a'?'scoreA':'scoreB';m[key]=Math.max(0,(m[key]||0)+amount);save();renderMatchDetail(); }
    function bindScoreButton(id,team){ const button=$(id);let holdTimer,held=false;button.onpointerdown=()=>{held=false;holdTimer=setTimeout(()=>{held=true;changeScore(team,-1);},600);};button.onpointerup=button.onpointercancel=button.onpointerleave=()=>clearTimeout(holdTimer);button.onclick=()=>{if(held){held=false;return;}changeScore(team,1);}; }
    function renderActive(){
        const s=state.current;if(!s){show('home');return;}
        s.matches=s.matches||emptyCourts(s.courts);s.waiting=s.waiting||[];
        const readyCount=s.waiting.filter(p=>(p.status||'ready')==='ready').length;
        $('#active-title').textContent=s.name;
        const played=s.played||s.matches.filter(m=>m.done).length;
        $('#active-summary').textContent=`Round ${s.round} · ${played} ${played===1?'match':'matches'} played`;
        $('#matches').innerHTML=s.matches.map(m=>{
            const staged=m.teamA?.length===2&&m.teamB?.length===2;
            const building=Array.isArray(m.manualSlots);
            const players=staged?[...m.teamA,...m.teamB]:[];
            const picked=building?m.manualSlots.filter(Boolean).length:0;
            const status=building?`Building · ${picked}/4`:!staged?'Open':'● Staged';
            const live=m.startedAt?`<span class="court-live"><span class="court-alert" aria-hidden="true">⚑</span><span class="match-timer ${m.done?'complete':''}" data-match-timer data-started-at="${m.startedAt}" data-ended-at="${m.completedAt||''}">${elapsedMinutes(m.startedAt,m.completedAt)}m</span></span>`:`<span>${status}</span>`;
            const courtContent=staged?(m.started?players.map(courtPlayer).join(''):players.map((player,index)=>editableCourtPlayer(player,index,m.id)).join('')):building?m.manualSlots.map((player,index)=>manualSlot(player,index,m.id)).join(''):'<div class="court-slot"></div>'.repeat(4);
            const liveTeams=`<div class="live-team-actions"><button class="live-team-winner team-a" data-live-winner="a" data-live-match="${m.id}" type="button"><span class="live-team-win">Team 1 wins</span></button><button class="live-team-winner team-b" data-live-winner="b" data-live-match="${m.id}" type="button"><span class="live-team-win">Team 2 wins</span></button></div>`;
            const actions=staged?(m.started?liveTeams:`<div class="court-actions"><button class="start-match" data-start="${m.id}">Start match</button><button class="shuffle" data-shuffle="${m.id}" aria-label="Shuffle players">Shuffle</button><button class="call-players" data-call-players="${m.id}" aria-label="Call players">📣</button><button class="unstage" data-unstage="${m.id}" aria-label="Clear court">×</button></div>`):building?`<div class="building-actions"><button class="autofill" data-autofill="${m.id}" ${readyCount<4-picked?'disabled':''}>Auto-fill rest</button><span class="slots-needed">Need ${4-picked} more</span><button class="unstage" data-cancel-manual="${m.id}" aria-label="Cancel manual selection">×</button></div>`:`<div class="empty-court-actions"><button class="stage" data-stage="${m.id}" ${readyCount<4?'disabled':''}>${readyCount<4?'Need 4 ready players':'Stage next →'}</button><button class="manual-pick" data-manual="${m.id}">or pick players manually</button><button class="remove-court" data-remove-court="${m.id}" ${s.matches.length<=1?'disabled':''}>× Remove court</button></div>`;
            return `<article class="match-card ${m.started&&!m.done?'playing open-match':''} ${building?'building':''}" ${m.started&&!m.done?`data-open-match="${m.id}" role="button" tabindex="0" aria-label="Open Court ${m.court} match"`:''}><div class="match-head"><strong>Court ${m.court}</strong>${live}</div><div class="court-preview ${staged||building?'':'empty'}" aria-label="${staged?'Staged players':building?'Choose players':'Empty court'}">${courtContent}</div>${actions}</article>`;
        }).join('');
        $$('[data-stage]').forEach(b=>b.onclick=()=>stageCourt(s.matches.find(x=>x.id===b.dataset.stage)));
        $$('[data-start]').forEach(b=>b.onclick=()=>{const m=s.matches.find(x=>x.id===b.dataset.start);m.started=true;m.startedAt=new Date().toISOString();m.completedAt=null;m.scoreA=0;m.scoreB=0;save();renderActive();});
        $$('[data-live-winner]').forEach(b=>b.onclick=e=>{e.stopPropagation();const match=s.matches.find(m=>m.id===b.dataset.liveMatch);if(!match)return;const winner=b.dataset.liveWinner,team=winner==='a'?match.teamA:match.teamB,label=team.map(p=>p.name).join(' & ');openMatchId=match.id;closeOpenMatch(true,winner);toast(`${label} wins`);});
        $$('[data-manual]').forEach(b=>b.onclick=()=>beginManual(s.matches.find(x=>x.id===b.dataset.manual)));
        $$('[data-pick-slot]').forEach(b=>b.onclick=()=>{const match=s.matches.find(m=>m.id===b.dataset.pickMatch);if(match)showPlayerPicker(match,Number(b.dataset.pickSlot));});
        $$('[data-edit-staged-slot]').forEach(b=>b.onclick=()=>{const match=s.matches.find(m=>m.id===b.dataset.editStagedMatch);if(match)editStagedPlayer(match,Number(b.dataset.editStagedSlot));});
        $$('[data-autofill]').forEach(b=>b.onclick=()=>autoFillManual(s.matches.find(x=>x.id===b.dataset.autofill)));
        $$('[data-cancel-manual]').forEach(b=>b.onclick=()=>cancelManual(s.matches.find(x=>x.id===b.dataset.cancelManual)));
        $$('[data-remove-court]').forEach(b=>b.onclick=()=>removeCourt(s.matches.find(x=>x.id===b.dataset.removeCourt)));
        $$('[data-open-match]').forEach(card=>{const open=()=>{openMatchId=card.dataset.openMatch;show('match-detail');};card.onclick=open;card.onkeydown=e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();open();}};});
        $$('[data-shuffle]').forEach(b=>b.onclick=()=>shuffleCourt(s.matches.find(x=>x.id===b.dataset.shuffle)));
        $$('[data-call-players]').forEach(b=>b.onclick=()=>callPlayers(s.matches.find(x=>x.id===b.dataset.callPlayers)));
        $$('[data-unstage]').forEach(b=>b.onclick=()=>unstageCourt(s.matches.find(x=>x.id===b.dataset.unstage)));
        $$('.finish').forEach(b=>b.onclick=()=>{const m=s.matches.find(x=>x.id===b.dataset.match);m.done=!m.done;m.completedAt=m.done?new Date().toISOString():null;save();renderActive();});
        const emptyCount=s.matches.filter(m=>!m.teamA?.length&&!m.manualSlots).length;
        const readyPlayers=s.waiting.filter(p=>(p.status||'ready')==='ready'),unavailablePlayers=s.waiting.filter(p=>(p.status||'ready')!=='ready');
        const waitingRow=(p,unavailable=false)=>{const mins=Math.max(0,Math.floor((Date.now()-new Date(p.queuedAt||s.startedAt))/60000)),wins=p.wins||0,losses=p.losses||0,games=p.gamesPlayed??wins+losses,rating=playerRating(p),partner=sessionPlayer(p.partnerId),status=p.status||'ready';return `<button class="waiting-player" data-waiting-player="${p.id}" type="button"><span class="waiting-avatar">${escapeHtml(playerInitials(p))}</span><span class="waiting-name">${escapeHtml(p.name)} <small class="waiting-rating" data-rating="${rating}" aria-label="${rating} star rating">${'★'.repeat(rating)}</small>${partner?`<small class="waiting-link">↔ ${escapeHtml(partner.name)}</small>`:''}</span>${unavailable?`<span class="availability-pill ${status}">${status}</span>`:`<span class="wait-time">${mins} min</span>`}<span class="waiting-record">${wins}-${losses} (${games})</span></button>`;};
        $('#waiting').innerHTML=`<div class="waiting"><div class="waiting-head"><span class="waiting-title">Waiting · ${readyPlayers.length}</span>${emptyCount&&readyCount>=4?`<button class="stage-all" id="stage-all">Stage all empty (${emptyCount})</button>`:''}</div>${readyPlayers.length?`<div class="waiting-columns"><span>Wait</span><span>W-L</span><span>(GP)</span></div><div class="waiting-list">${readyPlayers.map(p=>waitingRow(p)).join('')}</div>`:'<p class="waiting-empty">No ready players waiting.</p>'}${unavailablePlayers.length?`<section class="unavailable-section"><div class="waiting-head"><span class="waiting-title">Unavailable · ${unavailablePlayers.length}</span></div><div class="waiting-list">${unavailablePlayers.map(p=>waitingRow(p,true)).join('')}</div></section>`:''}</div>`;
        $$('[data-waiting-player]').forEach(b=>b.onclick=()=>showPlayerActions(b.dataset.waitingPlayer));
        const stageAll=$('#stage-all');if(stageAll)stageAll.onclick=()=>{s.matches.filter(m=>!m.teamA?.length&&!m.manualSlots).forEach(m=>{if(s.waiting.filter(p=>(p.status||'ready')==='ready').length>=4)stageCourt(m);});};
        const history=[...(s.completedMatches||[])].reverse();
        $('#match-history').innerHTML=history.length?`<section class="match-history"><h3 class="match-history-title">Match history · ${history.length}</h3><div class="match-history-card">${history.map(item=>{const aWinner=item.winner==='a',bWinner=item.winner==='b',noWinner=!item.winner,noScore=Number(item.scoreA)===0&&Number(item.scoreB)===0;const scores=noWinner?'<span class="no-winner-label">No winner</span>':noScore?`<span class="${aWinner?'winner-mark':'loser-mark'}">${aWinner?'W':'—'}</span><span class="${bWinner?'winner-mark':'loser-mark'}">${bWinner?'W':'—'}</span>`:`<span class="${aWinner?'winning-score':''}">${item.scoreA}</span><span class="${bWinner?'winning-score':''}">${item.scoreB}</span>`;return `<div class="history-match ${noWinner?'no-winner-row':''}"><span class="history-round">R${item.round||1}</span><div class="history-teams"><span class="${aWinner?'winner':''}">${escapeHtml(item.teamA.join(' & '))}</span><span class="${bWinner?'winner':''}">${escapeHtml(item.teamB.join(' & '))}</span></div><div class="history-score">${scores}<span class="history-duration" title="Match duration">${matchDuration(item.startedAt,item.completedAt)}</span></div></div>`}).join('')}</div></section>`:'';
        const addCourtButton=$('#add-court');addCourtButton.disabled=s.matches.length>=8;addCourtButton.textContent=s.matches.length>=8?'Maximum 8 courts':'＋ Add court';
    }
    $('#add-court').onclick=addCourt;
    $('#add-more').onclick=()=>{updatingSession=true;state.players=[...state.current.players];renderPlayers();show('checkin');};
    $('#show-standings').onclick=showStandings;
    function renderSessionSummary(){const s=state.summary;if(!s){show('home');return;}const players=[...(s.players||[])].sort((a,b)=>(b.wins||0)-(a.wins||0)||(b.gamesPlayed||0)-(a.gamesPlayed||0)||a.name.localeCompare(b.name)),matches=s.completedMatches||[],duration=Math.max(0,new Date(s.endedAt).getTime()-new Date(s.startedAt).getTime()),minutes=Math.floor(duration/60000),durationText=minutes>=60?`${Math.floor(minutes/60)}h ${minutes%60}m`:`${minutes}m`,withScore=matches.filter(m=>Number(m.scoreA)>0||Number(m.scoreB)>0).length,top=players.slice(0,3);const topRows=top.map((p,index)=>{const games=p.gamesPlayed||0,wins=p.wins||0,rate=games?Math.round(wins/games*100):0;return `<div class="standing-row top-${index+1}"><span class="standing-rank leader">${index+1}</span><div><div class="standing-name">${escapeHtml(p.name)} <span class="standing-stars">${'★'.repeat(playerRating(p))}</span></div><div class="standing-meta">${rate}% win rate · ${games} ${games===1?'game':'games'}</div></div><span class="standing-wins">${wins}<small>wins</small></span></div>`}).join('');const matchRows=[...matches].reverse().map(item=>{const aWinner=item.winner==='a',bWinner=item.winner==='b',noWinner=!item.winner,noScore=Number(item.scoreA)===0&&Number(item.scoreB)===0,scores=noWinner?'<span class="no-winner-label">No winner</span>':noScore?`<span class="${aWinner?'winner-mark':'loser-mark'}">${aWinner?'W':'—'}</span><span class="${bWinner?'winner-mark':'loser-mark'}">${bWinner?'W':'—'}</span>`:`<span class="${aWinner?'winning-score':''}">${item.scoreA}</span><span class="${bWinner?'winning-score':''}">${item.scoreB}</span>`;return `<div class="history-match ${noWinner?'no-winner-row':''}"><span class="history-round">R${item.round||1}</span><div class="history-teams"><span class="${aWinner?'winner':''}">${escapeHtml(item.teamA.join(' & '))}</span><span class="${bWinner?'winner':''}">${escapeHtml(item.teamB.join(' & '))}</span></div><div class="history-score">${scores}</div></div>`}).join('');$('#session-summary-content').innerHTML=`<h3 class="summary-name">${escapeHtml(s.name)}</h3><p class="summary-date">${new Date(s.startedAt).toLocaleString()}</p><div class="summary-metrics"><div class="summary-metric"><strong>${durationText}</strong><span>Duration</span></div><div class="summary-metric"><strong>${s.played||matches.length}</strong><span>Matches</span></div><div class="summary-metric"><strong>${withScore}</strong><span>With score</span></div><div class="summary-metric"><strong>${players.length}</strong><span>Players</span></div></div>${topRows?`<h3 class="summary-block-title">Top 3 players</h3><div class="standings-panel" style="width:100%;box-shadow:none;border:1px solid #dbe1e9">${topRows}</div>`:''}<h3 class="summary-block-title">Player stats</h3><div class="summary-table"><div class="summary-table-head"><span>#</span><span>Player</span><span>GP</span><span>W</span><span>L</span></div>${players.map((p,index)=>`<div class="summary-player"><span class="summary-player-number">${index+1}</span><strong>${escapeHtml(p.name)} <small class="summary-rating">${'★'.repeat(playerRating(p))}</small></strong><span>${p.gamesPlayed||0}</span><span>${p.wins||0}</span><span>${p.losses||0}</span></div>`).join('')}</div>${matchRows?`<h3 class="summary-block-title">Match history · ${matches.length}</h3><div class="match-history-card">${matchRows}</div>`:''}`;}
    function renderSummaryPlayerStats(){const table=$('.summary-table'),s=state.summary;if(!table||!s)return;const players=[...(s.players||[])].sort((a,b)=>(b.wins||0)-(a.wins||0)||(b.gamesPlayed||0)-(a.gamesPlayed||0)||a.name.localeCompare(b.name));table.innerHTML=`<div class="summary-table-head"><span>#</span><span>Player</span><span>Games</span><span>W</span><span>L</span><span>Win rate</span></div>${players.map((p,index)=>{const games=p.gamesPlayed||0,wins=p.wins||0,rate=games?Math.round(wins/games*100):0;return `<div class="summary-player"><span class="summary-player-number">${index+1}</span><strong>${escapeHtml(p.name)} <small class="summary-rating">${'★'.repeat(playerRating(p))}</small></strong><span>${games}</span><span>${wins}</span><span>${p.losses||0}</span><span>${rate}%</span></div>`}).join('')}`;}
    function renderSummaryMatchDurations(){const matches=[...(state.summary?.completedMatches||[])].reverse(),scores=$$('#session-summary-content .history-score');scores.forEach((score,index)=>{const item=matches[index];if(!item||score.querySelector('.history-duration'))return;score.insertAdjacentHTML('beforeend',`<span class="history-duration" title="Match duration">${matchDuration(item.startedAt,item.completedAt)}</span>`);});}
    bindScoreButton('#score-team-a','a');bindScoreButton('#score-team-b','b');
    $('#match-cancel').onclick=()=>show('active');
    const endDialog=$('#end-dialog'),endDialogPanel=$('#end-dialog-panel');
    function hideEndDialog(){endDialog.hidden=true;endDialog.classList.remove('selector');}
    function confirmEndSession(){if(!state.current)return;endDialog.classList.remove('selector');endDialogPanel.innerHTML=`<div class="end-dialog-copy"><h3 id="end-dialog-title">End session?</h3><p>No more matches can be generated after ending. You’ll see the complete session summary next.</p></div><div class="confirm-actions"><button id="cancel-end-session">Cancel</button><button id="confirm-end-session" style="color:#ff6b66">End session</button></div>`;endDialog.hidden=false;$('#cancel-end-session').onclick=hideEndDialog;$('#confirm-end-session').onclick=finishSession;}
    function finishSession(){const session=structuredClone(state.current);session.id=session.id||crypto.randomUUID();session.endedAt=new Date().toISOString();state.summary=session;if(state.keepHistory){state.history.unshift(session);archiveSession(session);}state.current=null;state.players=[];state.setup={...initial.setup};$('#session-name').value='';renderSetup();renderPlayers();hideEndDialog();save();show('session-summary');}
    $('#end-session').onclick=confirmEndSession;
    $('#summary-done').onclick=()=>show('home');
    function closeOpenMatch(record,winner=null){const s=state.current,m=currentOpenMatch();if(!s||!m)return;const completedAt=new Date().toISOString();if(record){s.played=(s.played||0)+1;const winningTeam=winner==='a'?m.teamA:winner==='b'?m.teamB:[],losingTeam=winner==='a'?m.teamB:winner==='b'?m.teamA:[],bump=(player,field)=>{const canonical=s.players.find(p=>p.id===player.id);[...new Set([player,canonical].filter(Boolean))].forEach(p=>p[field]=(p[field]||0)+1);};[...m.teamA,...m.teamB].forEach(p=>bump(p,'gamesPlayed'));winningTeam.forEach(p=>bump(p,'wins'));losingTeam.forEach(p=>bump(p,'losses'));s.completedMatches=s.completedMatches||[];s.completedMatches.push({court:m.court,round:s.round,teamA:m.teamA.map(p=>p.name),teamB:m.teamB.map(p=>p.name),scoreA:m.scoreA||0,scoreB:m.scoreB||0,winner,startedAt:m.startedAt,completedAt});}const queuedAt=completedAt;[...m.teamA,...m.teamB].forEach(p=>p.queuedAt=queuedAt);s.waiting.push(...m.teamA,...m.teamB);m.teamA=[];m.teamB=[];m.started=false;m.startedAt=null;m.completedAt=null;m.scoreA=0;m.scoreB=0;m.done=false;openMatchId=null;hideEndDialog();save();show('active');}
    function requestEndMatch(){const m=currentOpenMatch();if(!m)return;const scoreA=m.scoreA||0,scoreB=m.scoreB||0;if(scoreA!==scoreB){const winner=scoreA>scoreB?'a':'b',team=winner==='a'?m.teamA:m.teamB;endDialog.classList.remove('selector');endDialogPanel.innerHTML=`<div class="end-dialog-copy"><h3 id="end-dialog-title">End match?</h3><p>Final score: ${scoreA} – ${scoreB} · ${escapeHtml(team.map(p=>p.name).join(' & '))} wins</p></div><div class="confirm-actions"><button id="end-dialog-cancel">Cancel</button><button id="end-dialog-confirm">End match</button></div>`;endDialog.hidden=false;$('#end-dialog-cancel').onclick=hideEndDialog;$('#end-dialog-confirm').onclick=()=>closeOpenMatch(true,winner);return;}endDialog.classList.add('selector');endDialogPanel.innerHTML=`<div class="end-dialog-copy"><h3 id="end-dialog-title">How did this match end?</h3><p>${scoreA===0?'No score was entered.':'The scores are tied.'} Pick a winner or skip.</p></div><div class="winner-options"><button data-winner="a">${escapeHtml(m.teamA.map(p=>p.name).join(' & '))} won</button><button data-winner="b">${escapeHtml(m.teamB.map(p=>p.name).join(' & '))} won</button><button class="no-winner" id="no-winner">End without a winner</button></div><div class="winner-cancel-wrap"><button class="winner-cancel" id="winner-cancel">Cancel</button></div>`;endDialog.hidden=false;$$('[data-winner]',endDialogPanel).forEach(b=>b.onclick=()=>closeOpenMatch(true,b.dataset.winner));$('#no-winner').onclick=()=>closeOpenMatch(true,null);$('#winner-cancel').onclick=hideEndDialog;}
    $('#end-unrecorded').onclick=()=>closeOpenMatch(false);$('#end-recorded').onclick=requestEndMatch;
    endDialog.onclick=e=>{if(e.target===endDialog)hideEndDialog();};
    function renderHistory(){const list=[...(state.current?[state.current]:[]),...state.history];$('#history-list').innerHTML=list.length?list.map((s,i)=>{const active=i===0&&state.current,historyIndex=i-(state.current?1:0);return `<article class="session-card"><span class="section-label" style="margin:0">${active?'In progress':ago(s.startedAt)}</span><h3>${escapeHtml(s.name)}</h3><p class="meta">${s.players.length} players · ${s.courts} courts · ${s.played||s.completedMatches?.length||0} matches</p><button class="card-link" ${active?'data-open-active':`data-open-summary="${historyIndex}"`}>${active?'Open session':'View summary'} →</button></article>`}).join(''):'<p class="empty">No sessions yet.</p>';const openActive=$('[data-open-active]');if(openActive)openActive.onclick=()=>show('active');$$('[data-open-summary]').forEach(b=>b.onclick=()=>{const session=state.history[Number(b.dataset.openSummary)];if(!session)return;if(!session.endedAt)session.endedAt=session.startedAt;state.summary=session;save();show('session-summary');});}
    $('#history-toggle').classList.toggle('on',state.keepHistory);$('#history-toggle').onclick=()=>{state.keepHistory=!state.keepHistory;$('#history-toggle').classList.toggle('on',state.keepHistory);save();};
    $('#clear-data').onclick=()=>{if(confirm('Clear all PB Queue sessions and players?'))fetch({{ Illuminate\Support\Js::from(route('open-play.data.clear')) }},{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrfToken}}).then(response=>{if(!response.ok)throw new Error('Unable to clear Open Play data');state=structuredClone(initial);location.reload();}).catch(()=>toast('Open Play data could not be cleared'));};
    setInterval(()=>{updateMatchTimers();updateMatchDetailClock();},1000);
    renderSetup();renderSkills();renderPlayers();renderHome();show(state.current&&state.screen==='active'?'active':(state.screen==='session-summary'&&state.summary?'session-summary':(state.screen==='history'||state.screen==='settings'?state.screen:'home')));
})();
</script>
</body>
</html>
