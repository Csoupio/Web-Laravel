<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\FacturationController;
 
Route::get('/', function() {
    if (!Auth::check()) return view('index');

    $user = Auth::user();
    if ($user->role === 'Administrateur') {
        return redirect()->route('admin.index');
    }
    return redirect()->route('dashboard');
})->name('home');
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/logout',   [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);

Route::middleware('auth.session')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'index'])->name('dashboard');

    Route::get('/tickets/{id}',           [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets',               [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{id}/comment',  [TicketController::class, 'addComment'])->name('tickets.comment');

    Route::post('/tickets/{id}/time',     [TimeEntryController::class, 'store'])->name('time.store');
    Route::delete('/time/{id}',           [TimeEntryController::class, 'destroy'])->name('time.destroy');

    Route::post('/tickets/{id}/facturation/mode',      [FacturationController::class, 'setMode'])->name('facturation.mode');
    Route::post('/tickets/{id}/facturation/soumettre', [FacturationController::class, 'soumettre'])->name('facturation.soumettre');
    Route::post('/tickets/{id}/facturation/accepter',  [FacturationController::class, 'accepter'])->name('facturation.accepter');
    Route::post('/tickets/{id}/facturation/refuser',   [FacturationController::class, 'refuser'])->name('facturation.refuser');
    Route::get('/facturation/validation',              [FacturationController::class, 'validationIndex'])->name('facturation.validation.index');
    Route::get('/facturation/validation/{id}',         [FacturationController::class, 'validationShow'])->name('facturation.validation.show');

    Route::get('/projets',                             [ProjetController::class, 'index'])->name('projets.index');
    Route::get('/projets/{id}',                        [ProjetController::class, 'show'])->name('projets.show');
    Route::get('/projets/{id}/tickets/create',         [TicketController::class, 'create'])->name('tickets.create');
    Route::get('/projets/{id}/time-report',            [TimeEntryController::class, 'projetReport'])->name('projets.time-report');
});

Route::prefix('admin')->name('admin.')->middleware('role:Administrateur')->group(function () {
    Route::get('/',                                           [AdminController::class, 'index'])->name('index');
    Route::post('/users',                                     [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/clients',                                   [AdminController::class, 'storeClient'])->name('clients.store');
    Route::put('/clients/{id}',                               [AdminController::class, 'updateClient'])->name('clients.update');
    Route::post('/projets',                                   [AdminController::class, 'storeProjet'])->name('projets.store');
    Route::patch('/tickets/{id}/status',                      [AdminController::class, 'forceStatus'])->name('tickets.status');
    Route::post('/projets/{projetId}/collaborateurs',         [AdminController::class, 'assignCollaborateur'])->name('projets.collaborateurs.assign');
    Route::delete('/projets/{projetId}/collaborateurs/{userId}', [AdminController::class, 'removeCollaborateur'])->name('projets.collaborateurs.remove');
    Route::patch('/projets/{id}/contrat',                     [AdminController::class, 'updateContrat'])->name('projets.contrat');
    Route::delete('/users/{id}',                              [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::delete('/clients/{id}',                            [AdminController::class, 'destroyClient'])->name('clients.destroy');
    Route::delete('/projets/{id}',                            [AdminController::class, 'destroyProjet'])->name('projets.destroy');
    Route::delete('/tickets/{id}',                            [AdminController::class, 'destroyTicket'])->name('tickets.destroy');
});

// API Routes (moved to web for session/CSRF support)
Route::prefix('api/v1')->group(function () {
    Route::post('tickets', [TicketController::class, 'storeApi'])->name('api.tickets.store');
});
