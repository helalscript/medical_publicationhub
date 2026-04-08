<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    protected $model = Publication::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(rand(4, 8));

        $categoryIds = Category::query()
            ->where('type', 'publication')
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit($this->faker->numberBetween(0, 3))
            ->pluck('id')
            ->values()
            ->all();

        return [
            'category_ids' => $categoryIds === [] ? null : $categoryIds,
            'title' => rtrim($title, '.'),
            'overview' => $this->faker->paragraph(2),
            'description' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
            'attachment' => null,
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
