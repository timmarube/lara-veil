<?php

use Illuminate\Support\Facades\Route;
use LaraVeil\UserManager\Controllers\UserController;

Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::get('user-list', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('user-list/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('user-list', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('user-list/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('user-list/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('user-list/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});
