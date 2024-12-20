<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/transactions/{transaction}/print', [\App\Http\Controllers\TransactionController::class, 'print'])->name('transaction.print');
Route::get('/tanks/{tank}/print', [\App\Http\Controllers\TankController::class, 'print'])->name('tank.print');
