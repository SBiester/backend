<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArrayReceiverController;
use App\Http\Controllers\BereichController;
use App\Http\Controllers\FunktionController;
use App\Http\Controllers\SachbereichController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VorgesetzteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/bereiche', [BereichController::class, 'index']);
Route::get('/sachbereiche', [SachbereichController::class, 'index']);
Route::get('/teams', [TeamController::class, 'index']);
Route::get('/funktionen', [FunktionController::class, 'index']);
Route::get('/vorgesetzte', [VorgesetzteController::class, 'index']);

Route::get('/square/{number}', function ($number) {
    if (!is_numeric($number)) {
        return response()->json(['error' => 'Bitte geben Sie eine gültige Zahl an.'], 400);
    }

    $number = (float) $number;
    $square = $number * $number;

    return response()->json([
        'number' => $number,
        'square' => $square,
    ]);
});

Route::get('/staedte/{stadt}', function ($stadt) {
    return response()->json([
        'stadt' => $stadt,
    ]);
});

//
