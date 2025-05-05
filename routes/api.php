<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

// AUTH
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::group(['middleware' => 'auth:api'], function (){
    // Current User & Logout
    Route::get('/user', [AuthController::class, 'currentUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //* ADMIN
    Route::group(['middleware' => 'role:Admin'], function(){
        Route::get('/users',[UserController::class,'allUsers']);
        Route::get('/users/{id}',[UserController::class,'detailUser']);
        Route::post('/verifier',[UserController::class,'verifierRegister']);
        Route::patch('/change/{id}',[UserController::class,'changeRoles']);
        Route::patch('/reset/{id}',[UserController::class,'passwordReset']);
        Route::get('/permissions',[PermissionController::class,'permissions']);
        Route::get('/permissions/{id}',[PermissionController::class,'detailPermission']);
    });

    //* VERIFIKATOR
    Route::group(['middleware' => 'role:Verifikator'], function(){
        Route::post('/user/status',[UserController::class,'userStatus']);
        Route::patch('/user/verification/{id}',[UserController::class,'userVerification']);
        Route::post('/permission/status',[PermissionController::class,'permissionStatus']);
        Route::patch('/permission/decision/{id}',[PermissionController::class,'decision']);
    });

    //* ORDINARY (USER)
    Route::group(['middleware' => 'role:Ordinary'], function(){
        Route::patch('/update/password',[UserController::class,'passwordUpdate']);
        Route::post('/apply/permission',[PermissionController::class,'applyPermission']);
        Route::get('/list/permission',[PermissionController::class,'permissionList']);
        Route::patch('/change/permission/{id}',[PermissionController::class,'changePermission']);
        Route::get('/detail/permission/{id}',[PermissionController::class,'permissionStatusDetail']);
        Route::patch('/cancel/permission/{id}',[PermissionController::class,'cancelPermission']);
        Route::delete('/remove/permission/{id}',[PermissionController::class,'removePermission']);
    });
});