<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;



Route::get('/{any?}', function () {
    return view('welcome');
});
//tag routes
Route::post('/app/create_tag', [AdminController::class, 'addTag']);
Route::get('/app/get_tags', [AdminController::class, 'getTag']);
Route::post('/app/edit_tag', [AdminController::class, 'editTag']);
Route::post('/app/delete_tag', [AdminController::class, 'deleteTag']);
//category routes
Route::post('/app/upload', [AdminController::class, 'upload']);
Route::post('/app/delete_image', [AdminController::class, 'deleteImage']);
Route::post('/app/create_category', [AdminController::class, 'addCategory']);
Route::get('/app/get_category', [AdminController::class, 'getCategory']);
Route::post('/app/edit_category', [AdminController::class, 'editCategory']);
Route::post('/app/delete_category', [AdminController::class, 'deleteCategory']);
