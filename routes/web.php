<?php

use App\Http\Controllers\OpenPlayController;
use App\Models\Court;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'courts' => app()->environment('testing')
            ? collect()
            : Court::query()
                ->where('status', 'active')
                ->where('is_reservable', true)
                ->orderBy('name')
                ->get(),
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/open-play', [OpenPlayController::class, 'show'])->name('open-play');
    Route::post('/open-play/access-request', [OpenPlayController::class, 'requestAccess'])->name('open-play.access.request');
    Route::put('/open-play/state', [OpenPlayController::class, 'saveState'])->name('open-play.state.save');
    Route::post('/open-play/team-logos', [OpenPlayController::class, 'uploadTeamLogo'])->name('open-play.team-logos.upload');
    Route::post('/open-play/sessions', [OpenPlayController::class, 'archiveSession'])->name('open-play.sessions.archive');
    Route::delete('/open-play/data', [OpenPlayController::class, 'clearData'])->name('open-play.data.clear');
});

Route::post('/admin/impersonation/stop', function () {
    $impersonatorId = session()->pull('impersonator_id');
    $impersonator = $impersonatorId ? User::query()->find($impersonatorId) : null;

    abort_unless($impersonator?->role === 'admin', 403);

    Auth::login($impersonator);
    session()->regenerate();
    session()->put(
        'password_hash_'.config('auth.defaults.guard'),
        $impersonator->getAuthPassword(),
    );

    return redirect('/admin/users');
})->middleware('auth')->name('impersonation.stop');
