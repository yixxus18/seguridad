<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ActivationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login')->middleware('guest');

// Rutas de invitados (no autenticados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:15,1');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1');

    Route::get('/activate/{user}', [ActivationController::class, 'activate'])
        ->name('activate')
        ->middleware('signed');
});

// Rutas de verificación de dos factores
Route::middleware(['web','verify.captcha'])->group(function () {
    Route::get('/two-factor-auth', [AuthenticatedSessionController::class, 'showTwoFactorForm'])
        ->name('two-factor.form');
    Route::post('/two-factor-auth', [AuthenticatedSessionController::class, 'verifyTwoFactor'])
        ->name('two-factor.verify');
});

// Rutas protegidas
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Ruta de fallback
Route::fallback(function () {
    return redirect()->route('login');
})->middleware('web');
