<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\ContactController;
use App\Http\Controllers\Shop\ProductController;
use App\Models\Content\SiteSetting;
use Illuminate\Support\Facades\Route;

Route::get('catalogue', [ProductController::class, 'index'])->name('shop.index');
Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('shop.category');
Route::get('produits/{product:slug}', [ProductController::class, 'show'])->name('shop.product');

Route::get('a-propos', fn () => view('shop.about', ['settings' => SiteSetting::current()]))->name('shop.about');
Route::get('contact', [ContactController::class, 'show'])->name('shop.contact');
Route::post('contact', [ContactController::class, 'store'])->name('shop.contact.store');

Route::get('panier', [CartController::class, 'index'])->name('shop.cart');
Route::post('panier', [CartController::class, 'store'])->name('shop.cart.store');
Route::patch('panier/{item}', [CartController::class, 'update'])->name('shop.cart.update');
Route::delete('panier/{item}', [CartController::class, 'destroy'])->name('shop.cart.destroy');
