<?php

namespace App\Providers;

use App\Models\Catalogue\Category;
use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Toutes les pages boutique partagent la même nav de catégories racines ;
        // ce composer évite de passer $navCategories manuellement depuis chaque
        // contrôleur Shop\*.
        View::composer('layouts.shop', function ($view): void {
            $view->with(
                'navCategories',
                Category::query()->active()->whereNull('parent_id')->orderBy('position')->get()
            );
            $view->with('cartItemCount', app(CartService::class)->currentItemCount(request()));
        });
    }
}
