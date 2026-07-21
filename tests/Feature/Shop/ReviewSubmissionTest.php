<?php

namespace Tests\Feature\Shop;

use App\Models\Content\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_submitted_review_is_saved_but_not_published(): void
    {
        $response = $this->post(route('shop.reviews.store'), [
            'author_name' => 'Sokhna F.',
            'location' => 'Dakar',
            'rating' => 5,
            'comment' => 'Service impeccable, je recommande !',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'review-submitted');

        // Modération : créé mais NON publié tant que l'admin ne l'a pas validé.
        $this->assertDatabaseHas('reviews', [
            'author_name' => 'Sokhna F.',
            'is_published' => false,
        ]);
    }

    public function test_only_published_reviews_appear_on_the_home_page(): void
    {
        Review::create(['author_name' => 'Cliente publiée', 'comment' => 'Avis visible sur le site', 'is_published' => true]);
        Review::create(['author_name' => 'Cliente en attente', 'comment' => 'Avis en attente de validation', 'is_published' => false]);

        $response = $this->get(route('home'));

        $response->assertSee('Avis visible sur le site');
        $response->assertDontSee('Avis en attente de validation');
    }

    public function test_submission_requires_a_name_and_a_comment(): void
    {
        $response = $this->post(route('shop.reviews.store'), [
            'author_name' => '',
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['author_name', 'comment']);
        $this->assertDatabaseCount('reviews', 0);
    }
}
