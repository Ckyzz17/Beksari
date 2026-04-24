<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\KategoriController;

// Kategori
Route::apiResource('kategoris', KategoriController::class)
     ->only(['index', 'store', 'destroy']);

// Menu — full CRUD
Route::apiResource('menus', MenuController::class);
