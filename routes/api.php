<?php

use App\Http\Controllers\Gatway\AssasController;
use App\Http\Controllers\Notification\WebhookController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('webhookInvoice', [AssasController::class, 'webhookInvoice'])->name('webhookInvoice');
Route::post('webhookClicksing', [WebhookController::class, 'webhookClicksing'])->name('webhookClicksing');