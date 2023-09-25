<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OneBeautyController;
use App\Http\Controllers\OneMotosController;
use App\Http\Controllers\OnePositiveController;
use App\Http\Controllers\OneServicosController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VendasController;
use Illuminate\Support\Facades\Route;

//Login
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'login_action'])->name('login_action');

//Cadastro
Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/register', [RegisterController::class, 'register_action'])->name('register_action');
Route::get('/registerAssociado', [RegisterController::class, 'registerAssociado'])->name('registerAssociado');

//Vendas
Route::get('/associadonemotos/{id}/{entrada}', [OneMotosController::class, 'associado'])->name('associado');
Route::get('/onemotos/{id}', [OneMotosController::class, 'index'])->name('onemotos');
Route::get('/associadonepositive/{id}/{entrada}', [OnePositiveController::class, 'associado'])->name('associadopositive');
Route::get('/onepositive/{id}', [OnePositiveController::class, 'index'])->name('onepositive');
Route::get('/onebeauty/{id}', [OneBeautyController::class, 'index'])->name('onebeauty');
Route::get('/oneservicos/{id}', [OneServicosController::class, 'index'])->name('oneservicos');

Route::post('/venda/{id}', [VendasController::class, 'vender'])->name('vender');
Route::view('/contrato', 'relatorio.contrato')->name('relatorio.contrato');

//Extras
Route::view('/obrigado', 'obrigado');

//Autenticados
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');

    Route::get('/vendas/{id}', [VendasController::class, 'getVendas'])->name('vendas');
    Route::post('vendas', [VendasController::class, 'vendas'])->name('vendas');

    Route::post('lance', [VendasController::class, 'lance'])->name('lance');

    Route::get('/relatorioVendas', [RelatorioController::class, 'index'])->name('relatorioVendas');
    Route::post('relatorioVendas', [RelatorioController::class, 'filtro'])->name('relatorioVendas');

    Route::get('/relatorioUsuarios', [RelatorioController::class, 'usuarios'])->name('relatorioUsuarios');
    Route::post('relatorioUsuarios', [RelatorioController::class, 'upusuarios'])->name('relatorioUsuarios');

    Route::get('/relatorioParcelas/{id?}', [RelatorioController::class, 'relatorioParcelas'])->name('relatorioParcelas');
    Route::post('geraAssasParcela', [VendasController::class, 'geraAssasParcela'])->name('geraAssasParcela');

    Route::get('/relatorioPremiados', [RelatorioController::class, 'premiados'])->name('relatorioPremiados');
    Route::post('relatorioPremiados', [RelatorioController::class, 'cria_premiados'])->name('relatorioPremiados');
    Route::post('relatorioPremiadosUp', [RelatorioController::class, 'atualiza_premiados'])->name('relatorioPremiadosUp');

    Route::get('/relatorioContratos', [RelatorioController::class, 'relatorioContratos'])->name('relatorioContratos');

    Route::get('/perfil',[PerfilController::class, 'perfil'])->name('perfil');
    Route::post('/user/update', [PerfilController::class, 'update'])->name('update');

    Route::get('/relatorioParcelasAdmin', [RelatorioController::class, 'indexx'])->name('relatorioParcelasAdmin');
    Route::post('/relatorioParcelasAdmin', [RelatorioController::class, 'relatorioAction'])->name('relatorioParcelasAdmin');

});
