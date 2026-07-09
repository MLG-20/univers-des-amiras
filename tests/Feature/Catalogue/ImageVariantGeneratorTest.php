<?php

namespace Tests\Feature\Catalogue;

use App\Models\Catalogue\ProductImage;
use App\Services\ImageVariantGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageVariantGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_creates_a_webp_variant_for_each_size(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/original.jpg', $this->fakeJpeg());

        app(ImageVariantGenerator::class)->generate('products/original.jpg');

        foreach (array_keys(ImageVariantGenerator::SIZES) as $size) {
            Storage::disk('public')->assertExists("products/original-{$size}.webp");
        }
    }

    public function test_forget_removes_generated_variants(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/original.jpg', $this->fakeJpeg());

        $generator = app(ImageVariantGenerator::class);
        $generator->generate('products/original.jpg');
        $generator->forget('products/original.jpg');

        foreach (array_keys(ImageVariantGenerator::SIZES) as $size) {
            Storage::disk('public')->assertMissing("products/original-{$size}.webp");
        }
    }

    public function test_product_image_sized_url_falls_back_to_original_when_variant_missing(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/original.jpg', $this->fakeJpeg());

        $image = ProductImage::factory()->make(['path' => 'products/original.jpg']);

        $this->assertSame($image->url(), $image->sizedUrl('thumb'));
    }

    public function test_product_image_sized_url_uses_generated_variant_once_available(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/original.jpg', $this->fakeJpeg());

        app(ImageVariantGenerator::class)->generate('products/original.jpg');

        $image = ProductImage::factory()->make(['path' => 'products/original.jpg']);

        $this->assertStringContainsString('original-thumb.webp', $image->sizedUrl('thumb'));
    }

    private function fakeJpeg(): string
    {
        $gdImage = imagecreatetruecolor(20, 20);
        imagefill($gdImage, 0, 0, imagecolorallocate($gdImage, 200, 150, 100));

        ob_start();
        imagejpeg($gdImage);
        $contents = ob_get_clean();
        imagedestroy($gdImage);

        return $contents;
    }
}
