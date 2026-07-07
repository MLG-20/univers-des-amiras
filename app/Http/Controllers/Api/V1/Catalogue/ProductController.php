<?php

namespace App\Http\Controllers\Api\V1\Catalogue;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Catalogue\ProductResource;
use App\Models\Catalogue\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(
            Product::query()
                ->active()
                ->with(['category', 'images', 'variants' => fn ($query) => $query->active()])
                ->latest()
                ->paginate(20)
        );
    }

    public function show(Product $product): ProductResource
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'images', 'variants' => fn ($query) => $query->active()]);

        return ProductResource::make($product);
    }
}
