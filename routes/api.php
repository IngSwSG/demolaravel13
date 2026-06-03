<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('tasks/{task}/complete', [\App\Http\Controllers\TaskController::class, 'complete']);
Route::apiResource('tasks', \App\Http\Controllers\TaskController::class);
