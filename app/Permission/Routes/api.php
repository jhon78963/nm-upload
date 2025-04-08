<?php

use App\Permission\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::controller(PermissionController::class)->group(function() {
    Route::post('/permissions', 'create');
    Route::patch('/permissions/{permission}', 'update');
    Route::delete('/permissions/{permission}', 'delete');
    Route::get('/permissions', 'getAll');
    Route::get('/permissions/{permission}', 'get');
});
