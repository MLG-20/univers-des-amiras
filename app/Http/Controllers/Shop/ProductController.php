<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Catalogue\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->active()
            ->with(['category', 'images', 'variants' => fn ($query) => $query->active()])
            ->latest()
            ->paginate(12);

        return view('shop.index', [
            'products' => $products,
        ]);
    }

    public function show(Product $product): View
    {
        // Les produits soft-supprimés sont déjà exclus par le scope par défaut ;
        // les produits inactifs doivent aussi renvoyer 404, pour empêcher
        // l'énumération d'articles désactivés via leur slug.
        abort_unless($product->is_active, 404);

        $product->load(['category', 'images', 'variants' => fn ($query) => $query->active()]);

        return view('shop.product', [
            'product' => $product,
        ]);
    }
}
