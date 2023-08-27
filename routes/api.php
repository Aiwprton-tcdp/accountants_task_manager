<?php

use App\Http\Controllers\CRM\InstallController as CRMInstallController;
use App\Http\Controllers\CRM\IndexAPIController as CRMIndexController;
use App\Http\Controllers\CRM\UserController as CRMUserController;
use Illuminate\Support\Facades\Route;

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

Route::post('/install', [CRMInstallController::class, 'install']);
Route::post('/index', [CRMIndexController::class, '__invoke']);

Route::prefix('bx')->group(function () {
    Route::get('/users', [CRMUserController::class, 'search']);
    Route::get('/parent_folder_id', [CRMUserController::class, 'getParentFolderId']);
    // Route::get('/departments', [CRMDepartmentController::class, 'search']);
});

Route::apiResources([
    'managers' => \App\Http\Controllers\ManagerController::class,
    'llc' => \App\Http\Controllers\LLCController::class,
    'departments' => \App\Http\Controllers\DepartmentController::class,
    'contracts' => \App\Http\Controllers\ContractController::class,
    'contract_types' => \App\Http\Controllers\ContractTypeController::class,
]);

Route::get('/actions', [\App\Http\Controllers\ActionController::class, 'index']);