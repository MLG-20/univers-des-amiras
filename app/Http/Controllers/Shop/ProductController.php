<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\FilterProductsRequest;
use App\Models\Catalogue\Category;
use App\Models\Catalogue\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(FilterProductsRequest $request): View
    {
        $products = Product::query()
            ->active()
            ->filter($request->filters())
            ->with(['category', 'images', 'variants' => fn ($query) => $query->active()])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Une requête envoyée par le composant Alpine de recherche/filtres (voir
        // resources/js/shop-filters.js) ne reçoit que la grille de produits, pas
        // toute la page — évite un rechargement complet à chaque filtre.
        if ($request->ajax()) {
            return view('shop.partials.product-grid', ['products' => $products]);
        }

        return view('shop.index', [
            'products' => $products,
            'categories' => Category::query()->active()->orderBy('position')->get(),
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
