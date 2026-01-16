<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('theme::index');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
