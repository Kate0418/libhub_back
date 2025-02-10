<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\TransitionController;
use App\Models\SearchTag;
use AWS\CRT\HTTP\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/sign-up', [AuthController::class, 'signUp']);
});

Route::middleware("auth:sanctum")->group(function () {
    Route::get('/token', fn() => response()->json([
        "success" => true,
    ]));

    Route::post('/image', [ImageController::class, 'store']);

    Route::get('/library', [LibraryController::class, 'index']);
    Route::post('/library', [LibraryController::class, 'store']);
    Route::get('/library/tags', [LibraryController::class, 'tags']);
});

Route::get('/image', [ImageController::class, 'index']);
Route::post('/image/view', [ImageController::class, 'view']);
Route::get("/tag/select", function () {
    return [
        "success" => true,
        "tags" => SearchTag::pluck("name")
            ->map(function ($name) {
                return [
                    "value" => $name,
                    "label" => $name,
                ];
            }),
    ];
});
Route::get('/transition', [TransitionController::class, 'index']);
Route::post('/transition', [TransitionController::class, 'store']);
