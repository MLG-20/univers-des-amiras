<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('catalogue', [ProductController::class, 'index'])->name('shop.index');
Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('shop.category');
Route::get('produits/{product:slug}', [ProductController::class, 'show'])->name('shop.product');

Route::get('panier', [CartController::class, 'index'])->name('shop.cart');
Route::post('panier', [CartController::class, 'store'])->name('shop.cart.store');
Route::patch('panier/{item}', [CartController::class, 'update'])->name('shop.cart.update');
Route::delete('panier/{item}', [CartController::class, 'destroy'])->name('shop.cart.destroy');
