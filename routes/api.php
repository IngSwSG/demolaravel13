<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tasks', \App\Http\Controllers\TaskController::class);

Route::patch('tasks/{task}/complete', [TaskController::class, 'complete']);