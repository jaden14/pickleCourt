import { readFile, writeFile } from 'node:fs/promises';

const source = await readFile('resources/views/open-play.blade.php', 'utf8');
const standalone = source
    .replace('<html lang="{{ str_replace(\'_\', \'-\', app()->getLocale()) }}">', '<html lang="en">')
    .replace('<meta name="csrf-token" content="{{ csrf_token() }}">', '<meta name="csrf-token" content="offline">')
    .replace("const databaseState = {{ Illuminate\\Support\\Js::from($openPlayState ?? null) }};", "const databaseState = (()=>{try{return JSON.parse(localStorage.getItem('pb-queue-offline-state')||'null');}catch{return null;}})();")
    .replace("    const offlineState = (()=>{try{return JSON.parse(localStorage.getItem('pb-queue-offline-state')||'null');}catch{return null;}})();\n", '')
    .replace('    let state = {...initial,...((navigator.onLine?databaseState:offlineState)||databaseState||offlineState||{})};', '    let state = {...initial,...(databaseState||{})};')
    .replace("    const csrfToken = document.querySelector('meta[name=\"csrf-token\"]').content;\n", '')
    .replace(/    const persistOffline[\s\S]*?    if\('serviceWorker' in navigator\) window\.addEventListener\('load',\(\)=>navigator\.serviceWorker\.register\('\/pwa-sw\.js'\)\);\n/, "    const save = () => localStorage.setItem('pb-queue-offline-state', JSON.stringify({...state,history:[]}));\n    const archiveSession = () => Promise.resolve();\n")
    .replace(/    const archiveSession = session => fetch\([\s\S]*?\n/, '')
    .replace(/\$\('#clear-data'\)\.onclick=[\s\S]*?;\n/, "$('#clear-data').onclick=()=>{if(confirm('Clear all PB Queue sessions and players?')){localStorage.removeItem('pb-queue-offline-state');state=structuredClone(initial);location.reload();}};\n")
    .replace("if('speechSynthesis'in window&&'SpeechSynthesisUtterance'in window)", "if(window.Android&&typeof window.Android.speak==='function'){window.Android.speak(message);}else if('speechSynthesis'in window&&'SpeechSynthesisUtterance'in window)")
    .replace('<a class="dashboard-back" href="{{ route(\'filament.admin.pages.dashboard\') }}">← Back to dashboard</a>', '<span class="dashboard-back">Offline Open Play</span>');

if (standalone.includes('{{') || standalone.includes('fetch({{')) {
    throw new Error('A Laravel directive remains in the standalone Open Play build.');
}

await writeFile('mobile/www/index.html', standalone);
