<?php

namespace App\Observers;

use App\Models\Catalogue\ProductImage;
use App\Services\ImageVariantGenerator;

class ProductImageObserver
{
    public function __construct(private readonly ImageVariantGenerator $variants) {}

    public function saved(ProductImage $productImage): void
    {
        if ($productImage->wasRecentlyCreated || $productImage->wasChanged('path')) {
            $this->variants->generate($productImage->path);
        }
    }

    public function deleted(ProductImage $productImage): void
    {
        $this->variants->forget($productImage->path);
    }
}
