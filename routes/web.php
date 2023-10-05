<?php

use App\Http\Controllers\CupomController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\limpanomeController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VendasController;
use Illuminate\Support\Facades\Route;
use app\http\Kernel;

//Login
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'login_action'])->name('login_action');

//Cadastro
Route::get('/register/{codigo?}', [RegisterController::class, 'register'])->name('register');
Route::post('/register', [RegisterController::class, 'register_action'])->name('register_action');

//Gera Token BB
Route::get('/tokenBB', [BancoDoBrasilController::class, 'geraToken'])->name('tokenBB');

//Vendas
Route::get('/limpanome/{id}/{cupom?}', [limpanomeController::class, 'index'])->name('limpanome');

Route::post('/venda/{id}', [VendasController::class, 'vender'])->name('vender');
Route::view('/contrato', 'relatorio.contrato')->name('relatorio.contrato');

//Extras
Route::view('/obrigado', 'obrigado');

//Autenticados
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');

    Route::get('/vendas/{id}', [VendasController::class, 'getVendas'])->name('vendas');
    Route::post('vendas', [VendasController::class, 'getVendas'])->name('vendas');

    Route::get('/relatorioVendas', [RelatorioController::class, 'index'])->name('relatorioVendas');
    Route::post('relatorioVendas', [RelatorioController::class, 'filtro'])->name('relatorioVendas');

    Route::get('/relatorioUsuarios', [RelatorioController::class, 'usuarios'])->name('relatorioUsuarios');
    Route::post('relatorioUsuarios', [RelatorioController::class, 'upusuarios'])->name('relatorioUsuarios');

    Route::delete('/notificacao/{id}', [NotificacaoController::class, 'destroy'])->name('notificacao.destroy');
    Route::post('/cadastroNotficacao', [NotificacaoController::class, 'cadastroNotficacao'])->name('cadastroNotficacao');

    Route::get('/perfil',[PerfilController::class, 'perfil'])->name('perfil');
    Route::post('/user/update', [PerfilController::class, 'update'])->name('update');

    Route::get('/cupom',[CupomController::class, 'cupom'])->name('cupom');
    Route::post('cadastraCupom', [CupomController::class, 'cadastraCupom'])->name('cadastraCupom');
    Route::post('excluiCupom', [CupomController::class, 'excluiCupom'])->name('excluiCupom');




});
