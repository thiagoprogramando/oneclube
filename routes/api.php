<?php

use App\Http\Controllers\AsaasController;
use App\Http\Controllers\BancoDoBrasilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Webhook Clicksin
Route::post('clicksing', [AsaasController::class, 'enviaLinkPagamento'])->name('clicksing');
//Webhook Assas
Route::post('assas', [AsaasController::class, 'receberPagamento'])->name('assas');
//Planos One Clube
Route::post('oneclube', [AsaasController::class, 'geraAssasOneClube'])->name('oneclube');
//Gera Token BB
Route::get('/tokenBB', [BancoDoBrasilController::class, 'geraToken'])->name('tokenBB');


// Route::post('/geraPagamento', [AsaasController::class, 'geraPagamento'])->name('geraLink');
