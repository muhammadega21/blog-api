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
        Route::get('/post/search', 'index');
        Route::post('/post', 'store');
        Route::get('/post/{slug}', 'show');
        Route::put('/post/{id}/update', 'update');
        Route::delete('/post/{id}/delete', 'destroy');
    });

    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles', 'index');
        Route::post('/role', 'store');
        Route::put('/role/{id}/update', 'update');
        Route::delete('/role/{id}/delete', 'destroy');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::get('/user/{username}/profile', 'show');
        Route::put('/user/{id}/update', 'update');
        Route::delete('/user/{id}/delete', 'destroy');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::post('/category', 'store');
        Route::get('/category/{slug}', 'show');
        Route::put('/category/{id}/update', 'update');
        Route::delete('/category/{id}/delete', 'destroy');
    });
});
