<?php

use App\Http\Controllers\Issues\CategoriesController;
use App\Http\Controllers\Issues\IssuesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('issues', IssuesController::class);
    Route::resource('categories', CategoriesController::class)->except(['show', 'create']);
});
