<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Font\CmsPageController;
use App\Http\Controllers\Font\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site public
|--------------------------------------------------------------------------
*/

// Pages principales servies par le CMS (noms de routes stables)
Route::get('/', [CmsPageController::class, 'show'])->defaults('slug', 'home')->name('home');
Route::get('/a-propos', [CmsPageController::class, 'show'])->defaults('slug', 'a-propos')->name('about');
Route::get('/programmes', [CmsPageController::class, 'show'])->defaults('slug', 'programmes')->name('programmes');
Route::get('/realisations', [CmsPageController::class, 'show'])->defaults('slug', 'realisations')->name('realisations');
Route::get('/contact', [CmsPageController::class, 'show'])->defaults('slug', 'contact')->name('contact');

// Formulaire de contact
Route::post('/contact', [ContactController::class, 'submitContact'])->name('contact.submit');

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.attempt');
});

Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::get('/settings', [SettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    Route::resource('pages', PageController::class)
        ->names('admin.pages')
        ->except(['show']);

    Route::post('/pages/{page}/sections', [SectionController::class, 'store'])->name('admin.sections.store');
    Route::get('/pages/{page}/sections/{section}/edit', [SectionController::class, 'edit'])->name('admin.sections.edit');
    Route::put('/pages/{page}/sections/{section}', [SectionController::class, 'update'])->name('admin.sections.update');
    Route::delete('/pages/{page}/sections/{section}', [SectionController::class, 'destroy'])->name('admin.sections.destroy');
    Route::post('/pages/{page}/sections/{section}/move', [SectionController::class, 'move'])->name('admin.sections.move');

    Route::get('/messages', [MessageController::class, 'index'])->name('admin.messages.index');
    Route::post('/messages/{message}/read', [MessageController::class, 'markRead'])->name('admin.messages.read');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('admin.messages.destroy');
});

// Pages CMS personnalisées (toujours en dernier : catch-all)
Route::get('/{slug}', [CmsPageController::class, 'show'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('cms.show');
