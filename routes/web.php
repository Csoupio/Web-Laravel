<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProjetController;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('index'))->name('home');

Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/logout',    [AuthController::class, 'logout'])->name('logout');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Routes protégées — tous les utilisateurs connectés
|--------------------------------------------------------------------------
*/

Route::middleware('auth.session')->group(function () {

    Route::get('/dashboard', [ClientController::class, 'index'])->name('dashboard');

    Route::get('/tickets/{id}',            [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets',                [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{id}/comment',   [TicketController::class, 'addComment'])->name('tickets.comment');

    Route::get('/projets',                             [ProjetController::class, 'index'])->name('projets.index');
    Route::get('/projets/{id}',                        [ProjetController::class, 'show'])->name('projets.show');
    Route::get('/projets/{id}/tickets/create',         [TicketController::class, 'create'])->name('tickets.create');

});

/*
|--------------------------------------------------------------------------
| Routes protégées — Administrateur uniquement
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.session', 'role:Administrateur'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/',                                                [AdminController::class, 'index'])->name('index');

    Route::post('/users',                                          [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/clients',                                        [AdminController::class, 'storeClient'])->name('clients.store');
    Route::put('/clients/{id}',                                    [AdminController::class, 'updateClient'])->name('clients.update');
    Route::post('/projets',                                        [AdminController::class, 'storeProjet'])->name('projets.store');
    Route::patch('/tickets/{id}/status',                           [AdminController::class, 'forceStatus'])->name('tickets.status');

    // Gestion des collaborateurs sur un projet
    Route::post('/projets/{projetId}/collaborateurs',              [AdminController::class, 'assignCollaborateur'])->name('projets.collaborateurs.assign');
    Route::delete('/projets/{projetId}/collaborateurs/{userId}',   [AdminController::class, 'removeCollaborateur'])->name('projets.collaborateurs.remove');

});
