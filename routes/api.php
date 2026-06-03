<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tasks', \App\Http\Controllers\TaskController::class);

Route::put('/tasks/{task}/mark-as-completed', [\App\Http\Controllers\TaskController::class, 'markAsCompleted']);
