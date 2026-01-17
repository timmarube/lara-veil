<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('theme::index');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');

require __DIR__.'/settings.php';

use App\Http\Controllers\Admin\ExtensibilityController;

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/plugins', [ExtensibilityController::class, 'plugins'])->name('admin.plugins');
    Route::post('/plugins/{plugin}/toggle', [ExtensibilityController::class, 'togglePlugin'])->name('admin.plugins.toggle');
    
    Route::get('/themes', [ExtensibilityController::class, 'themes'])->name('admin.themes');
    Route::post('/themes/{theme}/activate', [ExtensibilityController::class, 'activateTheme'])->name('admin.themes.activate');
});
