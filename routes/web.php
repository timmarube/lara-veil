<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('theme::index');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');

require __DIR__.'/settings.php';



Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Extensibility (Volt)
    Volt::route('/plugins', 'admin.extensibility.plugins')->name('admin.plugins');
    Volt::route('/themes', 'admin.extensibility.themes')->name('admin.themes');

    // Media Manager (Volt)
    Volt::route('/media', 'admin.media.index')->name('admin.media.index');
    Volt::route('/media/create', 'admin.media.create')->name('admin.media.create');
    Volt::route('/media/{medium}/edit', 'admin.media.edit')->name('admin.media.edit');
});
