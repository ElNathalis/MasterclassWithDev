<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\MasterClassController;
use App\Http\Controllers\AdminController;

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.updateRole');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.destroy');
});

Route::get('/masterclass/check-slots', [MasterClassController::class, 'checkOccupiedSlots'])
    ->middleware('auth')
    ->name('masterclass.checkOccupiedSlots');

Route::get('/masterclass/create', [MasterClassController::class, 'create'])
    ->middleware('auth')
    ->name('masterclass.create');

Route::get('/masterclass/{masterClass}/edit', [MasterClassController::class, 'edit'])
    ->middleware('auth')
    ->name('masterclass.edit');

Route::patch('/masterclass/{masterClass}', [MasterClassController::class, 'update'])
    ->middleware('auth')
    ->name('masterclass.update');

Route::post('/masterclass', [MasterClassController::class, 'store'])
    ->middleware('auth')
    ->name('masterclass.store');

Route::get('/cabinet', [CabinetController::class, 'index'])
    ->middleware('auth')
    ->name('cabinet');

Route::get('/registration/{masterClass}', [RegistrationController::class, 'show'])
    ->middleware('auth')
    ->name('registration.show');

Route::post('/registration/{masterClass}', [RegistrationController::class, 'store'])
    ->middleware('auth')
    ->name('registration.store');

Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
