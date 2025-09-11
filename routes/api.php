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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;

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
Route::get('/hardware/search', [HardwareController::class, 'searchHardware']);

// Software API Routes
Route::get('/software', [SoftwareController::class, 'getAllSoftware']);
Route::get('/software/profile', [SoftwareController::class, 'getSoftwareByProfile']);
Route::get('/software/additional', [SoftwareController::class, 'getAdditionalSoftware']);
Route::get('/software/manufacturers', [SoftwareController::class, 'getManufacturers']);
Route::get('/software/manufacturer', [SoftwareController::class, 'getSoftwareByManufacturer']);
Route::get('/software/search', [SoftwareController::class, 'searchSoftware']);

// Options API Routes
Route::get('/options', [OptionsController::class, 'getOptions']);
Route::get('/telefon-types', [OptionsController::class, 'getTelefonTypes']);

// SAP API Routes
Route::get('/sap/profiles', [SapController::class, 'getSapProfiles']);
Route::get('/sap/categories', [SapController::class, 'getSapCategories']);
Route::get('/sap/category', [SapController::class, 'getSapProfilesByCategory']);
Route::get('/sap/profile', [SapController::class, 'getSapProfileById']);
Route::get('/sap/statistics', [SapController::class, 'getSapStatistics']);
Route::get('/sap/search', [SapController::class, 'searchSapProfiles']);

// Job Update API Routes
Route::post('/job-update', [JobUpdateController::class, 'submitJobUpdate']);

// Orders API Routes
Route::get('/orders/user', [OrderController::class, 'getUserOrders']);

// Admin API Routes
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);
    
    // User Management
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    
    // Hardware Management
    Route::get('/hardware', [AdminController::class, 'getHardwareItems']);
    Route::post('/hardware', [AdminController::class, 'createHardwareItem']);
    Route::put('/hardware/{id}', [AdminController::class, 'updateHardwareItem']);
    Route::delete('/hardware/{id}', [AdminController::class, 'deleteHardwareItem']);
    
    // Hardware Categories Management
    Route::get('/hardware/categories', [AdminController::class, 'getHardwareCategories']);
    Route::post('/hardware/categories', [AdminController::class, 'createHardwareCategory']);
    Route::put('/hardware/categories/{id}', [AdminController::class, 'updateHardwareCategory']);
    Route::delete('/hardware/categories/{id}', [AdminController::class, 'deleteHardwareCategory']);
    
    // Software Management
    Route::get('/software', [AdminController::class, 'getSoftwareItems']);
    Route::post('/software', [AdminController::class, 'createSoftwareItem']);
    Route::put('/software/{id}', [AdminController::class, 'updateSoftwareItem']);
    Route::delete('/software/{id}', [AdminController::class, 'deleteSoftwareItem']);
    
    // Software Manufacturers Management
    Route::get('/software/manufacturers', [AdminController::class, 'getSoftwareManufacturers']);
    Route::post('/software/manufacturers', [AdminController::class, 'createSoftwareManufacturer']);
    Route::put('/software/manufacturers/{id}', [AdminController::class, 'updateSoftwareManufacturer']);
    Route::delete('/software/manufacturers/{id}', [AdminController::class, 'deleteSoftwareManufacturer']);
    
    // SAP Management
    Route::get('/sap/roles', [AdminController::class, 'getSapRoles']);
    Route::get('/sap/groups', [AdminController::class, 'getSapRoleGroups']);
    Route::post('/sap/roles', [AdminController::class, 'createSapRole']);
    Route::put('/sap/roles/{id}', [AdminController::class, 'updateSapRole']);
    Route::delete('/sap/roles/{id}', [AdminController::class, 'deleteSapRole']);
    Route::post('/sap/groups', [AdminController::class, 'createSapRoleGroup']);
    Route::put('/sap/groups/{id}', [AdminController::class, 'updateSapRoleGroup']);
    Route::delete('/sap/groups/{id}', [AdminController::class, 'deleteSapRoleGroup']);
    
    // Profile Management
    Route::get('/profiles', [ReferenzprofilController::class, 'adminIndex']);
    Route::post('/profiles', [ReferenzprofilController::class, 'store']);
    Route::put('/profiles/{id}', [ReferenzprofilController::class, 'update']);
    Route::delete('/profiles/{id}', [ReferenzprofilController::class, 'destroy']);
    
    // Order Management (Job-Updates)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/stats', [OrderController::class, 'getStats']);
    Route::get('/orders/export', [OrderController::class, 'exportOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::put('/orders/{id}/process', [OrderController::class, 'processOrder']);
    
    // Master Data Management
    Route::get('/bereiche', [AdminController::class, 'getBereiche']);
    Route::post('/bereiche', [AdminController::class, 'createBereich']);
    Route::put('/bereiche/{id}', [AdminController::class, 'updateBereich']);
    Route::delete('/bereiche/{id}', [AdminController::class, 'deleteBereich']);
    
    Route::get('/teams', [AdminController::class, 'getTeams']);
    Route::post('/teams', [AdminController::class, 'createTeam']);
    Route::put('/teams/{id}', [AdminController::class, 'updateTeam']);
    Route::delete('/teams/{id}', [AdminController::class, 'deleteTeam']);
    
    Route::get('/funktionen', [AdminController::class, 'getFunktionen']);
    Route::post('/funktionen', [AdminController::class, 'createFunktion']);
    Route::put('/funktionen/{id}', [AdminController::class, 'updateFunktion']);
    Route::delete('/funktionen/{id}', [AdminController::class, 'deleteFunktion']);
});
