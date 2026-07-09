<?php

namespace App\Console\Commands\Images;

use App\Models\Catalogue\ProductImage;
use App\Services\ImageVariantGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('images:generate-variants')]
#[Description('Génère les variantes redimensionnées (thumb/medium/large) des images produit déjà en base, pour les images uploadées avant la mise en place du redimensionnement automatique.')]
class GenerateVariants extends Command
{
    public function handle(ImageVariantGenerator $generator): int
    {
        $images = ProductImage::query()->get();

        $this->withProgressBar($images, fn (ProductImage $image) => $generator->generate($image->path));

        $this->newLine(2);
        $this->info("Variantes générées pour {$images->count()} image(s).");

        return self::SUCCESS;
    }
}
