<?php

namespace App\Observers;

use App\Models\Catalogue\Category;
use App\Services\ImageVariantGenerator;

class CategoryObserver
{
    public function __construct(private readonly ImageVariantGenerator $variants) {}

    public function saved(Category $category): void
    {
        if ($category->image_path && ($category->wasRecentlyCreated || $category->wasChanged('image_path'))) {
            $this->variants->generate($category->image_path);
        }
    }

    public function deleted(Category $category): void
    {
        if ($category->image_path) {
            $this->variants->forget($category->image_path);
        }
    }
}
