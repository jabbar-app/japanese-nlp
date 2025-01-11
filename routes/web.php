<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NLPController;


Route::get('/', function () {
    return view('index');
});
Route::post('/parse', [NLPController::class, 'parse']);
