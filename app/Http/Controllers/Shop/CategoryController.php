<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Catalogue\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        // Same anti-enumeration rule as products: a disabled category 404s.
        abort_unless($category->is_active, 404);

        $category->load(['children' => fn ($query) => $query->active()->orderBy('position')]);

        $products = $category->products()
            ->active()
            ->with(['category', 'images', 'variants' => fn ($query) => $query->active()])
            ->latest()
            ->paginate(12);

        return view('shop.category', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
