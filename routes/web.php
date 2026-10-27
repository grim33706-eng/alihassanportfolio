<?php

use App\Http\Controllers\Admin;
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
| Admin panel (password in ADMIN_PASSWORD, or set from Settings).
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'show'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');

    Route::middleware('admin')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

        Route::get('/content/{section}', [Admin\ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{section}', [Admin\ContentController::class, 'update'])->name('content.update');
        Route::post('/content/{section}/reset', [Admin\ContentController::class, 'reset'])->name('content.reset');

        Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::put('/settings/password', [Admin\SettingsController::class, 'password'])->name('settings.password');

        Route::get('/messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::post('/messages/read', [Admin\MessageController::class, 'markAllRead'])->name('messages.read');
        Route::get('/messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');

        Route::resource('case-studies', Admin\CaseStudyController::class)->except('show');
    });
});
