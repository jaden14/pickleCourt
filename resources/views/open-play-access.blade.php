<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Open Play Access</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:1.25rem;background:#f5f7fb;color:#18202b;font-family:Inter,ui-sans-serif,system-ui,sans-serif}.card{width:min(100%,520px);padding:1.5rem;border:1px solid #dce2eb;border-radius:18px;background:#fff;box-shadow:0 12px 35px rgb(25 39 64/.1)}.icon{display:grid;width:48px;height:48px;place-items:center;border-radius:14px;background:#e9f4ff;font-size:1.4rem}h1{margin:1rem 0 .4rem;font-size:1.6rem}p{color:#667186;line-height:1.5}.status{padding:.8rem;border-radius:10px;background:#fff7df;color:#8a5a00}.success{background:#e8f8ee;color:#147a46}textarea{width:100%;min-height:100px;margin:.5rem 0;padding:.75rem;border:1px solid #ccd4df;border-radius:10px;resize:vertical;font:inherit}button,a{display:block;width:100%;padding:.8rem;border:0;border-radius:10px;text-align:center;text-decoration:none;font:inherit;font-weight:750}button{background:#1673d1;color:#fff;cursor:pointer}.back{margin-top:.65rem;background:#f0f2f6;color:#4d5869}
    </style>
</head>
<body>
<main class="card">
    <div class="icon">🏓</div>
    <h1>Open Play module</h1>
    <p>This module is separate from court bookings. An administrator must approve access for your account.</p>
    @if (session('status'))<p class="status success">{{ session('status') }}</p>@endif
    @if ($accessRequest?->status === 'pending')
        <p class="status">Your request is awaiting administrator approval.</p>
    @else
        @if ($accessRequest?->status === 'rejected')<p class="status">Your previous request was not approved. You may send a new request.</p>@endif
        <form method="post" action="{{ route('open-play.access.request') }}">
            @csrf
            <label for="message">Why do you need Open Play? <small>(optional)</small></label>
            <textarea id="message" name="message" maxlength="1000" placeholder="Tell the administrator how you plan to use the module.">{{ old('message') }}</textarea>
            <button type="submit">Request access</button>
        </form>
    @endif
    <a class="back" href="/admin">Back to dashboard</a>
</main>
</body>
</html>
