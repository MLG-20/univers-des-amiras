<?php

namespace App\Observers;

use App\Models\Content\HeroSlide;
use App\Services\ImageVariantGenerator;

class HeroSlideObserver
{
    public function __construct(private readonly ImageVariantGenerator $variants) {}

    public function saved(HeroSlide $slide): void
    {
        if ($slide->image_path && ($slide->wasRecentlyCreated || $slide->wasChanged('image_path'))) {
            $this->variants->generate($slide->image_path);
        }
    }

    public function deleted(HeroSlide $slide): void
    {
        if ($slide->image_path) {
            $this->variants->forget($slide->image_path);
        }
    }
}
