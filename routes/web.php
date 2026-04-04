<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('index'))
    ->name('home');

require __DIR__.'/auth.php';

Route::get('dashboard', [ClientController::class, 'index'])
    ->name('dashboard');

Route::get('tickets/{id}', [TicketController::class, 'show'])
    ->name('tickets.show');
Route::post('tickets', [TicketController::class, 'store'])
    ->name('tickets.store');
Route::post('tickets/{id}/comment', [TicketController::class, 'addComment'])
    ->name('tickets.comment');

Route::get('projets', [ProjetController::class, 'index'])
    ->name('projets.index');
Route::get('projets/{id}', [ProjetController::class, 'show'])
    ->name('projets.show');
Route::get('projets/{id}/tickets/create', [TicketController::class, 'create'])
    ->name('tickets.create');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])
        ->name('index');

    Route::post('users', [AdminController::class, 'storeUser'])
        ->name('users.store');

    Route::post('clients', [AdminController::class, 'storeClient'])
        ->name('clients.store');
    Route::put('clients/{id}', [AdminController::class, 'updateClient'])
        ->name('clients.update');

    Route::post('projets', [AdminController::class, 'storeProjet'])
        ->name('projets.store');

    Route::patch('tickets/{id}/status', [AdminController::class, 'forceStatus'])
        ->name('tickets.status');
});