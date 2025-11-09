<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PreferenceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Article Search & Viewing
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

    // Customizable News Preferences
    Route::get('/news/preferences', [PreferenceController::class, 'edit'])->name('news.preferences.edit');
    Route::put('/news/preferences', [PreferenceController::class, 'update'])->name('news.preferences.update');
});

require __DIR__.'/settings.php';
