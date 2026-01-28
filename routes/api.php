<?php

use App\Http\Controllers\AnneeacademiqueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RepondantController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SessionremiseController;
use App\Http\Controllers\TyperepondantController;
use App\Http\Controllers\SessiondemandeController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// === Guest routes ===
Route::post('/login', [UserController::class, 'login']);
Route::get('anneeacademiques_getcurrent/{resource}', [AnneeacademiqueController::class, 'getCurrent'])->name('anneeacademiques.getcurrent');
Route::get('demandes_getcurrent', [DemandeController::class, 'getCurrent'])->name('demandes.getcurrent');
Route::post('demandes/guestsubmit', [DemandeController::class, 'storeGuestSubmit'])->name('demandes.storeguestsubmit');
Route::get('regions_getactive', [RegionController::class, 'getActive'])->name('regions.getactive');
Route::get('sessiondemandes_getactive', [SessiondemandeController::class, 'getActive'])->name('sessiondemandes.getactive');
Route::get('sims_regions_dissocier/{region}/{nbrsim}/{anneeacademique}', [SimController::class, 'unlinkRegion'])->name('sims.unlinkregion');
Route::post('sims/provinces/associer', [SimController::class, 'linkProvince'])->name('sims.linkprovince');
Route::get('sims_provinces_dissocier/{province}/{nbrsim}/{anneeacademique}', [SimController::class, 'unlinkProvince'])->name('sims.unlinkprovince');
Route::get('sites_getactive', [SiteController::class, 'getActive'])->name('sites.getactive');
// ===

// === Protected API routes ===
Route::middleware('auth:sanctum')->group(function () {
    // Route::post('anneeacademiques_getcurrent/{resource}', [AnneeacademiqueController::class, 'getCurrent'])->name('anneeacademiques.getcurrent');

    Route::post('/logout', [UserController::class, 'logout']);
    // === Resources 
    Route::apiResource('anneeacademiques', AnneeacademiqueController::class);
    Route::apiResource('demandes', DemandeController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('provinces', ProvinceController::class);
    Route::apiResource('regions', RegionController::class);
    Route::apiResource('repondants', RepondantController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('sessiondemandes', SessiondemandeController::class);
    Route::apiResource('sessionremises', SessionremiseController::class);
    Route::apiResource('sims', SimController::class);
    Route::apiResource('sites', SiteController::class);
    Route::apiResource('typerepondants', TyperepondantController::class);
    Route::apiResource('users', UserController::class);
    // === Single routes
    Route::post('demandes/simdeclarerperte', [DemandeController::class, 'simDeclarerPerte'])->name('demandes.simdeclarerperte');
    Route::post('demandes/showby', [DemandeController::class, 'showBy'])->name('demandes.showby');
    Route::post('demandes/assignable', [DemandeController::class, 'demandeAssignable'])->name('demandes.assignable');
    Route::post('demande/attribuer/sim', [DemandeController::class, 'demandeAttribuerSim'])->name('demande.attribuer.sim');
    Route::post('demande/dissocier/sim', [DemandeController::class, 'demandeDissocierSim'])->name('demande.dissocier.sim');
    Route::post('repondants/import', [RepondantController::class, 'import'])->name('repondants.import');
    Route::post('sessiondemandes/showby', [SessiondemandeController::class, 'showBy'])->name('sessiondemandes.showby');
    Route::post('sims/import', [SimController::class, 'import'])->name('sims.import');
    Route::post('sims/attribuer/regions', [SimController::class, 'simAttribuerRegion'])->name('sims.attribuer.regions');
    Route::get('sims_remises', [SimController::class, 'simRemise'])->name('sims.remises');
    Route::post('sims/remettre', [SimController::class, 'simRemettre'])->name('sims.remettre');
    Route::post('users/regeneratepassword', [UserController::class, 'regeneratePassword'])->name('users.regeneratepassword');
    Route::post('users/resetpassword', [UserController::class, 'resetPassword'])->name('users.resetpassword');
    // 
});
