<?php

use App\Image\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::controller(ImageController::class)->group(function() {
    Route::post('/images/upload', 'upload');
    Route::post('/images/multiple-upload', 'multipleUpload');
    Route::delete('/images/{path}', 'delete');
    Route::get('/images', 'getAll');
    Route::get('/images/{image}', 'get');
});
