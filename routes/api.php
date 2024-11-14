<?php

use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\API\v1\PostController;
use App\Http\Controllers\API\v1\RoleController;
use App\Http\Controllers\API\v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::controller(PostController::class)->group(function () {
        Route::get('/posts', 'index');
        Route::post('/post', 'store');
        Route::get('/post/{slug}', 'show');
    });

    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles', 'index');
        Route::post('/role', 'store');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::post('/users', 'store');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::post('/category', 'store');
    });
});
