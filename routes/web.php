<?php

use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentDuelController;
use App\Http\Controllers\TournamentEntryController;
use App\Http\Controllers\TournamentLogoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
return Redirect::route('tournament.index');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::prefix('tournament')->group(function () {
    Route::get('/', [TournamentController::class, 'index'])->name('tournament.index');
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/add', [TournamentController::class, 'create'])->name('tournament.create');
        Route::post('/', [TournamentController::class, 'store'])->name('tournament.store');
        Route::post('/enroll', [TournamentEntryController::class, 'store'])->name('tournament.enroll');
        Route::get('/{id}/edit', [TournamentController::class, 'edit'])->name('tournament.edit');
        Route::patch('/{id}', [TournamentController::class, 'update'])->name('tournament.update');
        Route::delete('/{id}', [TournamentController::class, 'destroy'])->name('tournament.destroy');

        Route::post('/{id}/logo', [TournamentLogoController::class, 'store'])->name('tournament.logo.store');

        Route::get('/duels', [TournamentDuelController::class, 'index'])->name('tournament.duel.index');
        Route::post('/duels', [TournamentDuelController::class, 'update'])->name('tournament.duel.update');
    });
    Route::get('/{id}', [TournamentController::class, 'show'])->name('tournament.show');
});
