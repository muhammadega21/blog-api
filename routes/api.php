<?php

use App\Http\Controllers\API\Auth\AuthCotroller;
use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\API\v1\PostController;
use App\Http\Controllers\API\v1\RoleController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthCotroller::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::prefix('v1')->group(function () {
    Route::controller(PostController::class)->group(function () {
        Route::get('/post/search', 'index');
        Route::get('/post/{slug}', 'show');
        Route::get('/posts', 'index');
        Route::middleware('auth:sanctum', 'role:Administrator.Editor.Author')->group(function () {
            Route::post('/post', 'store');
            Route::put('/post/{id}/update', 'update');
            Route::delete('/post/{id}/delete', 'destroy');
        });
    });

    Route::controller(UserController::class)->group(function () {
        Route::middleware('auth:sanctum', 'role:Administrator.Editor')->group(function () {
            Route::get('/users', 'index');
            Route::put('/user/{id}/update', 'update');
            Route::delete('/user/{id}/delete', 'destroy');
        });
        Route::get('/user/{username}/profile', 'show');
    });

    Route::middleware('auth:sanctum', 'role:Administrator')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/roles', 'index');
            Route::post('/role', 'store');
            Route::put('/role/{id}/update', 'update');
            Route::delete('/role/{id}/delete', 'destroy');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index');
            Route::post('/category', 'store');
            Route::get('/category/{slug}', 'show');
            Route::put('/category/{id}/update', 'update');
            Route::delete('/category/{id}/delete', 'destroy');
        });
    });
});
