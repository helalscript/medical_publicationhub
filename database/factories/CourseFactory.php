<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 29, 599);
        $hasDiscount = $this->faker->boolean(35);

        $pickMetaAcademyIds = function (int $min = 1, int $max = 3): array {
            $ids = Category::query()
                ->where('type', 'meta_academy')
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit($this->faker->numberBetween($min, $max))
                ->pluck('id')
                ->all();

            return array_values(array_map('intval', $ids));
        };

        $activeMentors = Mentor::query()->where('status', 'active');
        $mentorCount = (int) $activeMentors->count();
        $mentorIds = [];
        if ($mentorCount > 0) {
            $take = $this->faker->numberBetween(1, min(3, $mentorCount));
            $mentorIds = array_values(array_map(
                'intval',
                (clone $activeMentors)->inRandomOrder()->limit($take)->pluck('id')->all()
            ));
        }

        $descBlocks = [
            [
                'title' => 'Overview',
                'description' => '<p>'.$this->faker->paragraph(2).'</p>',
            ],
            [
                'title' => 'What you will learn',
                'description' => '<ul><li>'.$this->faker->sentence().'</li><li>'.$this->faker->sentence().'</li><li>'.$this->faker->sentence().'</li></ul>',
            ],
            [
                'title' => 'Requirements',
                'description' => '<p>'.$this->faker->paragraph().'</p>',
            ],
        ];

        return [
            'category_ids' => $pickMetaAcademyIds(1, 4),
            'title' => $this->faker->sentence(rand(4, 8)),
            'price' => $price,
            'old_price' => $hasDiscount ? round($price + $this->faker->randomFloat(2, 10, 120), 2) : null,
            'short_description' => $this->faker->optional(0.9)->sentence(12),
            'thumbnail' => null,
            'thumbnail_description' => $this->faker->optional(0.5)->sentence(8),
            'thumbnail_button_text' => $this->faker->optional(0.5)->randomElement(['Enroll now', 'View syllabus', 'Start learning']),
            'intro_video' => null,
            'overview' => $this->faker->paragraph(3),
            'descriptions' => $descBlocks,
            'mentor_ids' => $mentorIds,
            'is_future' => $this->faker->boolean(20),
            'total_durations' => $this->faker->randomElement(['4 Weeks', '6 Weeks', '8 Weeks', '12 Weeks', 'Self-paced']),
            'start_module' => $this->faker->numberBetween(1, 3),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => 'inactive',
        ]);
    }
}
