<?php

use Illuminate\Support\Facades\Route;
use LaraVeil\Posts\Controllers\PostController;

Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::get('posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('posts', [PostController::class, 'store'])->name('admin.posts.store');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('admin.posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('admin.posts.destroy');

    // Settings
    Route::get('posts/settings', [PostController::class, 'settings'])->name('admin.posts.settings');
    Route::post('posts/settings/remove-table', [PostController::class, 'removeTable'])->name('admin.posts.remove_table');
});
