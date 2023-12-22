<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiTaskController;

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

Route::post('/login', [ApiAuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')
    ->get('/tasks', [ApiTaskController::class, 'index'])
    ->name('index');
Route::middleware('auth:sanctum')
    ->get('/tasks/tree', [ApiTaskController::class, 'tree'])
    ->name('tree');
Route::middleware('auth:sanctum')
    ->post('/tasks', [ApiTaskController::class, 'store'])
    ->name('store');
Route::middleware('auth:sanctum')
    ->get('/tasks/{id}', [ApiTaskController::class, 'show'])
    ->name('show');
Route::middleware('auth:sanctum')
    ->put('/tasks/{id}', [ApiTaskController::class, 'update'])
    ->name('update');
Route::middleware('auth:sanctum')
    ->delete('/tasks/{id}', [ApiTaskController::class, 'delete'])
    ->name('delete');
Route::middleware('auth:sanctum')
    ->post('/logout', [ApiAuthController::class, 'logout'])
    ->name('logout');
