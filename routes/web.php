<?php

use App\Models\Court;
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
