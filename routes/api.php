<?php

use App\Http\Controllers\AsaasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Webhook Clicksin
Route::post('clicksing', [AsaasController::class, 'enviaLinkPagamento'])->name('clicksing');
//Webhook Assas
Route::post('assas', [AsaasController::class, 'receberPagamentoAssas'])->name('assas');
//Webhook BancoDoBrasil
