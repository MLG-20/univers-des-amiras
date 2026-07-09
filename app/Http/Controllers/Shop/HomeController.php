<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use App\Models\Content\HeroSlide;
use App\Models\Content\SiteSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('shop.home', [
            'slides' => HeroSlide::query()->active()->orderBy('position')->get(),
            'settings' => SiteSetting::current(),
            'collections' => Category::query()
                ->active()
                ->whereNull('parent_id')
                ->orderBy('position')
                ->get(),
            'newProducts' => Product::query()
                ->active()
                ->with(['category', 'images', 'variants' => fn ($query) => $query->active()])
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}
