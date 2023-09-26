<?php

use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('report');
});

Route::get('/get-transactions', [TransactionsController::class, 'index']);

