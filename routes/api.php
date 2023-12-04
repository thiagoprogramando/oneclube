<?php

use App\Http\Controllers\Gatway\AssasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('webhookInvoice', [AssasController::class, 'webhookInvoice'])->name('webhookInvoice');