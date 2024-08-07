<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::delete('/posts/{id}', [PostController::class, 'destroy']);