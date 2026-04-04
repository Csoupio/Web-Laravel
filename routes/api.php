<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('tickets', [TicketController::class, 'storeApi'])
        ->name('api.tickets.store');
});
