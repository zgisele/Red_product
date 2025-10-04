<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentification
Route::post('/register', [AuthController::class, 'register']);
// Route pour se connecter et obtenir un token
Route::post('/login', [AuthController::class, 'login']);


// Routes protégées par JWT
Route::group(['middleware' => 'auth:api'], function () {
    // Route pour se déconnecter (JWT required)
    Route::post('logout', [AuthController::class, 'logout']);
    // Route pour récupérer les informations de l'utilisateur connecté (JWT required)
    Route::get('me', [AuthController::class, 'me']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // CRUD Hotels
    Route::apiResource('hotels', HotelController::class);

    Route::get('/hotels', [HotelController::class, 'index']);
    Route::post('/hotels', [HotelController::class, 'store']);
    Route::get('/hotels/{id}', [HotelController::class, 'show']);
    Route::put('/hotels/{hotel}', [HotelController::class, 'update']);
    Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);
});
