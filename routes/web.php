<?php

use App\Http\Controllers\BancoDoBrasilController;
use App\Http\Controllers\CupomController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\limpanomeController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VendasController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Login
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'login_action'])->name('login_action');

//Cliente
Route::get('/cliente', [UserController::class, 'portalCliente'])->name('cliente');
Route::post('/cliente', [UserController::class, 'consultaCliente'])->name('cliente');
Route::get('/parcelaCliente/{id}',[VendasController::class, 'parcelaCliente'])->name('parcelaCliente');
Route::post('/geraParcelaBancoDoBrasil', [BancoDoBrasilController::class, 'geraParcela'])->name('geraParcelaBancoDoBrasil');

//Cadastro
Route::get('/register/{codigo?}', [RegisterController::class, 'register'])->name('register');
Route::post('/register', [RegisterController::class, 'register_action'])->name('register_action');

//Vendas
Route::get('/limpanome/{id}/{cupom?}', [limpanomeController::class, 'index'])->name('limpanome');
Route::get('/score/{id}/{cupom?}', [ScoreController::class, 'index'])->name('score');

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
    Route::get('/parcelas/{id}',[VendasController::class, 'parcelas'])->name('parcelas');
    Route::get('/vendaDelete/{id}', [VendasController::class, 'vendaDelete'])->name('vendaDelete');

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
