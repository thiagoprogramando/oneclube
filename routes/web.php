<?php

use App\Http\Controllers\Gatway\AssasController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Users\ClientController;
use App\Http\Controllers\Users\UserController as UsersUserController;
use App\Http\Controllers\Users\WalletController;
use Illuminate\Support\Facades\Route;

//Login
Route::get('/', [UsersUserController::class, 'index'])->name('login');
Route::post('/', [UsersUserController::class, 'login'])->name('login');

//Vendas
Route::get('/limpanome/{id}/{valor}', [ProdutoController::class, 'limpanome'])->name('onepositive');
Route::post('/sell/{id}', [SaleController::class, 'sell'])->name('sell');

//Extras
Route::view('/obrigado', 'obrigado')->name('obrigado');

//Cliente
Route::view('/cliente', 'cliente.index')->name('cliente');
Route::post('/logarClient', [ClientController::class, 'logarClient'])->name('logarClient');
Route::get('/vendasCliente', [ClientController::class, 'vendasCliente'])->name('vendasCliente');
Route::get('/faturasCliente/{id}', [ClientController::class, 'faturasCliente'])->name('faturasCliente');
Route::get('/logoutClient', [ClientController::class, 'logoutClient'])->name('logoutClient');

//Autenticados
Route::middleware(['auth'])->group(function () {
    
    //Sales
    Route::get('/sales/{produto}', [SaleController::class, 'getSales'])->name('sales');
    Route::post('filterSales', [SaleController::class, 'filterSales'])->name('filterSales');
    Route::post('updateSale', [SaleController::class, 'updateSale'])->name('updateSale');

    //Manager
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/validation', [ManagerController::class, 'validation'])->name('validation');

    Route::get('/saleManager', [SaleController::class, 'saleManager'])->name('saleManager');
    Route::post('filterSaleManager', [SaleController::class, 'filterSaleManager'])->name('filterSaleManager');
    Route::post('updateSaleManager', [SaleController::class, 'updateSaleManager'])->name('updateSaleManager');

    Route::get('/listUsers', [ManagerController::class, 'listUsers'])->name('listUsers');

    //Users
    Route::get('/profile',[UsersUserController::class, 'profile'])->name('profile');
    Route::post('profileUpdate', [UsersUserController::class, 'profileUpdate'])->name('profileUpdate');

    //Wallet
    Route::get('/wallet', [WalletController::class, 'wallet'])->name('wallet');
    Route::post('withdraw', [WalletController::class, 'withdraw'])->name('withdraw');

    Route::post('/createUser', [ManagerController::class, 'createUser'])->name('createUser');

    //Invoices
    Route::get('/invoices/{id?}', [ManagerController::class, 'invoices'])->name('invoices');
    Route::post('invoiceCreate', [AssasController::class, 'invoiceCreate'])->name('invoiceCreate');

    //Actions
    Route::get('/logout', [UsersUserController::class, 'logout'])->name('logout');
});
