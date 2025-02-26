<?php

use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//User public
Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    //User
    Route::post('/logout',[UserController::class,'logout']);
    
    //Author
    Route::apiResource('authors',AuthorController::class)->except(['index','show']);

    //Books
    Route::apiResource('books',BookController::class)->except(['index','show']);

    //Categories
    Route::apiResource('categories',CategoryController::class)->except(['index','show']);
});

Route::middleware(['auth:sanctum','role:admin'])->group(function(){
    //User
    Route::get('/users',[UserController::class,'index']);
    
    //Author
    /* Route::get('authors',[AuthorController::class, 'index']);
    Route::get('authors/{id}',[AuthorController::class, 'show']); */
    Route::apiResource('authors',AuthorController::class)->only(['show','index']);

    //Books
    Route::apiResource('books',BookController::class)->only(['show','index']);
    //Categories
    Route::apiResource('categories',CategoryController::class)->only(['show','index']);
});
