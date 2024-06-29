<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Iamnotstatic\LaravelAPIAuth\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LivroController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::prefix('v1')->group(function () {
    Route::post('auth/token', [LoginController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('livros', [LivroController::class, 'listarTodos']);
        Route::post('livros', [LivroController::class, 'create']);
        Route::post('livros/{id}/importar-indices-xml', [LivroController::class, 'importarIndices']);
    });
});
