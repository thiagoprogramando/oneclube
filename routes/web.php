<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendasController;
use Illuminate\Support\Facades\Route;

//Login
Route::get('/', [UserController::class, 'index'])->name('login');
Route::post('/', [UserController::class, 'login_action'])->name('login_action');

//Cadastro
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/register', [UserController::class, 'register_action'])->name('register_action');

//Vendas
Route::get('/limpanome/{id}', [ProdutoController::class, 'limpanome'])->name('onepositive');

Route::post('/venda/{id}', [VendasController::class, 'vender'])->name('vender');

//Extras
Route::view('/obrigado', 'obrigado');

//Autenticados
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');

    Route::get('/vendas/{id}', [VendasController::class, 'getVendas'])->name('vendas');
    Route::post('vendas', [VendasController::class, 'vendas'])->name('vendas');

    Route::get('/relatorioVendas', [RelatorioController::class, 'index'])->name('relatorioVendas');
    Route::post('relatorioVendas', [RelatorioController::class, 'filtro'])->name('relatorioVendas');

    Route::get('/relatorioUsuarios', [RelatorioController::class, 'usuarios'])->name('relatorioUsuarios');
    Route::post('relatorioUsuarios', [RelatorioController::class, 'upusuarios'])->name('relatorioUsuarios');

    Route::get('/perfil',[UserController::class, 'perfil'])->name('perfil');
    Route::post('/user/update', [UserController::class, 'update'])->name('update');

    Route::get('/relatorio', [ParcelaController::class, 'index'])->name('relatorio');
    Route::post('/relatorio', [ParcelaController::class, 'relatorioAction'])->name('relatorioAction');

});
