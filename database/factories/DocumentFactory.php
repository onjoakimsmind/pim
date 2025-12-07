<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        
        return [
            'site_id' => \App\Models\Site::factory(),
            'author_id' => \App\Models\User::factory(),
            'type' => fake()->randomElement(['page', 'folder', 'link']),
            'controller' => fake()->randomElement([null, 'DefaultController', 'BlogController', 'ProductController', 'ProductController']),
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'meta' => [
                'description' => fake()->sentence(),
                'keywords' => fake()->words(5),
            ],
            'sort_order' => fake()->numberBetween(0, 100),
            'published' => fake()->boolean(70),
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 year', 'now') : null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
            'published_at' => null,
        ]);
    }
}
