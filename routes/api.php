<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;

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
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::post('login', [SessionController::class, 'login'])->middleware('guest:sanctum');
Route::post('user',  [UserController::class, 'store'])->middleware('guest:sanctum');


Route::middleware('auth:api')->group(function(){
    Route::any('logout',        [SessionController::class, 'logout']);

    Route::get('user',          [UserController::class, 'index']);
    Route::put('user/{id}',     [UserController::class, 'update']);
    Route::get('user/{id}',     [UserController::class, 'show']);
    Route::delete('user/{id}',  [UserController::class, 'destroy']);


    Route::get('task',          [TaskController::class, 'index']);
    Route::get('task/{id}',     [TaskController::class, 'show']);
    Route::put('task/{id}',     [TaskController::class, 'update']);
    Route::post('task',         [TaskController::class, 'store']);
    Route::delete('task/{id}',  [TaskController::class, 'destroy']);

});

