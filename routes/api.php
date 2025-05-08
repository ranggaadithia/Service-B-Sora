<?php

use App\Http\Controllers\ProxyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/users', [ProxyController::class, 'getUsers']);
Route::post('/stock/{ticker}', [ProxyController::class, 'index']);
