<?php

use App\Http\Controllers\MovieApiController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ['ok' => true, 'ts' => now()->toISOString()]);

Route::get('/movies/search', [MovieApiController::class, 'search']);
Route::get('/movie/{id}', [MovieApiController::class, 'getById']);
