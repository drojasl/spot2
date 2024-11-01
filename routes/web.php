<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortenerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/go/{short}', [ShortenerController::class, 'go'])->name('go');

Route::get('/create', function () {
    return Inertia::render('Create');
})->middleware(['auth', 'verified'])->name('create');

Route::middleware(['auth'])->group(function () {
    Route::get('/list', [ShortenerController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->prefix('short')->group(function () {
    Route::post('/store', [ShortenerController::class, 'store'])->name('shortener.store');
    Route::delete('/delete/{id}', [ShortenerController::class, 'destroy'])->name('shortener.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
