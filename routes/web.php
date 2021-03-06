<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminCheck;


Route::prefix('app')->middleware([AdminCheck::class])->group(function(){
    //tag routes
    Route::post('/create_tag', [AdminController::class, 'addTag']);
    Route::get('/get_tags', [AdminController::class, 'getTag']);
    Route::post('/edit_tag', [AdminController::class, 'editTag']);
    Route::post('/delete_tag', [AdminController::class, 'deleteTag']);
    //category routes
    Route::post('/upload', [AdminController::class, 'upload']);
    Route::post('/delete_image', [AdminController::class, 'deleteImage']);
    Route::post('/create_category', [AdminController::class, 'addCategory']);
    Route::get('/get_category', [AdminController::class, 'getCategory']);
    Route::post('/edit_category', [AdminController::class, 'editCategory']);
    Route::post('/delete_category', [AdminController::class, 'deleteCategory']);
    //admin user
    Route::post('/create_user', [AdminController::class, 'createUser']);
    Route::get('/get_users', [AdminController::class, 'getUsers']);
    Route::post('/edit_user', [AdminController::class, 'editUser']);
    Route::post('/delete_user', [AdminController::class, 'deleteUser']);
    //admin login
    Route::post('/admin_login', [AdminController::class, 'adminLogin']);
    //role route
    Route::post('create_role', [AdminController::class, 'addRole']);
    Route::get('get_roles', [AdminController::class, 'getRoles']);
    Route::post('edit_role', [AdminController::class, 'editRole']);
    Route::post('delete_role', [AdminController::class, 'deleteRole']);
    Route::post('assign_roles', [AdminController::class, 'assignRole']);

    //blog
    Route::post('create-blog', [AdminController::class, 'createBlog']);
    Route::get('blogsdata', [AdminController::class, 'blogdata']); //get the blogs item
    Route::post('delete_blog', [AdminController::class, 'deleteBlog']); //get the blogs item
    Route::get('blog_single/{id}', [AdminController::class, 'singleBlogItem']); //get the blogs item
    Route::post('update_blog/{id}', [AdminController::class, 'updateBlog']); //get the blogs item
});

Route::post('createBlog', [AdminController::class, 'uploadEditorImage']);
Route::get('blogdata', [AdminController::class, 'blogdata']);
Route::get('slug', [AdminController::class, 'slug']);


Route::get('/logout', [AdminController::class, 'logout']);
Route::get('/', [AdminController::class, 'index']);
Route::any('{slug?}', [AdminController::class, 'index']);

