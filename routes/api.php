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
Route::post('assas', [AsaasController::class, 'receberPagamento'])->name('assas');
//Planos One Clube
Route::post('oneclube', [AsaasController::class, 'geraAssasOneClube'])->name('oneclube');
//Planos Clube Job
Route::post('clubejob', [AsaasController::class, 'geraAssasClubeJob'])->name('clubejob');
//Webhook Assas Clube Job
Route::post('assasClubeJob', [AsaasController::class, 'receberPagamentoClubeJob'])->name('assasClubeJob');
//Consulta Faturas
Route::get('fatura/{id}', [AsaasController::class, 'consultaFatura'])->name('fatura');
