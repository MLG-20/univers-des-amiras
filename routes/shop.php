<?php

use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('catalogue', [ProductController::class, 'index'])->name('shop.index');
Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('shop.category');
Route::get('produits/{product:slug}', [ProductController::class, 'show'])->name('shop.product');
