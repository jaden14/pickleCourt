@if (session()->has('impersonator_id'))
    <div style="position:sticky;z-index:50;top:0;display:flex;align-items:center;justify-content:center;gap:1rem;padding:.65rem 1rem;background:#f59e0b;color:#111827;font-size:.85rem;font-weight:700;box-shadow:0 2px 8px rgb(0 0 0 / .18);">
        <span>You are impersonating {{ auth()->user()->name }}.</span>
        <form method="POST" action="{{ route('impersonation.stop') }}">
            @csrf
            <button type="submit" style="cursor:pointer;border:1px solid #111827;border-radius:.45rem;background:#111827;color:white;padding:.35rem .7rem;font-size:.75rem;font-weight:800;">
                Return to administrator
            </button>
        </form>
    </div>
@endif
