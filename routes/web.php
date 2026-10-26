<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/services', 'services')->name('services');
    Route::get('/portfolio', 'portfolio')->name('portfolio');
    Route::get('/results', 'results')->name('results');
    Route::get('/international-experience', 'international')->name('international');
    Route::get('/sitemap.xml', 'sitemap')->name('sitemap');
});

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

/*
| Content admin for before-and-after case studies (password in ADMIN_PASSWORD).
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'show'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');

    Route::middleware('admin')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::resource('case-studies', \App\Http\Controllers\Admin\CaseStudyController::class)->except('show');
    });
});
