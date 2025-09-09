<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArrayReceiverController;
use App\Http\Controllers\BereichController;
use App\Http\Controllers\FunktionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\HardwareController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\SachbereichController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VorgesetzteController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\JobUpdateController;
use App\Http\Controllers\SapController;
use App\Http\Controllers\ReferenzprofilController;

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
Route::get('/positionen', [PositionController::class, 'index']);
Route::get('/vorgesetzte', [VorgesetzteController::class, 'index']);

// Referenzprofil API Routes
Route::get('/referenzprofile', [ReferenzprofilController::class, 'index']);
Route::get('/referenzprofile/bereich', [ReferenzprofilController::class, 'getByBereich']);

// Hardware API Routes
Route::get('/hardware', [HardwareController::class, 'getAllHardware']);
Route::get('/hardware/profile', [HardwareController::class, 'getHardwareByProfile']);
Route::get('/hardware/additional', [HardwareController::class, 'getAdditionalHardware']);
Route::get('/hardware/categories', [HardwareController::class, 'getCategories']);
Route::get('/hardware/category', [HardwareController::class, 'getHardwareByCategory']);

// Software API Routes
Route::get('/software', [SoftwareController::class, 'getAllSoftware']);
Route::get('/software/profile', [SoftwareController::class, 'getSoftwareByProfile']);
Route::get('/software/additional', [SoftwareController::class, 'getAdditionalSoftware']);
Route::get('/software/manufacturers', [SoftwareController::class, 'getManufacturers']);
Route::get('/software/manufacturer', [SoftwareController::class, 'getSoftwareByManufacturer']);

// Options API Routes
Route::get('/options', [OptionsController::class, 'getOptions']);
Route::get('/telefon-types', [OptionsController::class, 'getTelefonTypes']);

// SAP API Routes
Route::get('/sap/profiles', [SapController::class, 'getSapProfiles']);
Route::get('/sap/categories', [SapController::class, 'getSapCategories']);
Route::get('/sap/category', [SapController::class, 'getSapProfilesByCategory']);
Route::get('/sap/profile', [SapController::class, 'getSapProfileById']);
Route::get('/sap/statistics', [SapController::class, 'getSapStatistics']);

// Job Update API Routes
Route::post('/job-update', [JobUpdateController::class, 'submitJobUpdate']);
