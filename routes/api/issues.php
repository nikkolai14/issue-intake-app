<?php

use App\Http\Controllers\Api\Issues\CategoriesController;
use App\Http\Controllers\Api\Issues\IssuesController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function (): void {
    // Issues endpoints
    Route::get('/issues', [IssuesController::class, 'index']);
    Route::post('/issues', [IssuesController::class, 'store']);
    Route::get('/issues/{issue}', [IssuesController::class, 'show']);
    Route::put('/issues/{issue}', [IssuesController::class, 'update']);
    Route::delete('/issues/{issue}', [IssuesController::class, 'destroy']);

    // Categories endpoints
    Route::get('/categories', [CategoriesController::class, 'index']);
    Route::post('/categories', [CategoriesController::class, 'store']);
    Route::get('/categories/{category}', [CategoriesController::class, 'show']);
    Route::put('/categories/{category}', [CategoriesController::class, 'update']);
    Route::delete('/categories/{category}', [CategoriesController::class, 'destroy']);
});
