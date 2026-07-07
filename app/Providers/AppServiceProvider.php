<?php

namespace App\Providers;

use App\Models\Catalogue\Category;
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
        // Every public shop page shares the same root-category nav; a composer
        // avoids passing $navCategories from every Shop\* controller manually.
        View::composer('layouts.shop', function ($view): void {
            $view->with(
                'navCategories',
                Category::query()->active()->whereNull('parent_id')->orderBy('position')->get()
            );
        });
    }
}
