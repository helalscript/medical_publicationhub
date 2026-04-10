<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Mentor;
use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Opportunity>
 */
class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(10);
        $slug = Str::slug($title).'-'.Str::lower(Str::random(5));

        $deadline = $this->faker->dateTimeBetween('+5 days', '+60 days');
        $start = (clone $deadline)->modify('+1 day');
        $end = (clone $start)->modify('+3 months');

        $mentorId = Mentor::query()->inRandomOrder()->value('id');

        $pickIds = function (string $type, int $max = 4): array {
            $ids = Category::query()
                ->where('type', $type)
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit($this->faker->numberBetween(1, $max))
                ->pluck('id')
                ->all();

            return array_values(array_map('intval', $ids));
        };

        $descBlocks = [
            [
                'title' => 'Responsibilities',
                'description' => '<ul><li>'.$this->faker->sentence().'</li><li>'.$this->faker->sentence().'</li></ul>',
            ],
            [
                'title' => 'Requirements',
                'description' => '<ul><li>'.$this->faker->sentence().'</li><li>'.$this->faker->sentence().'</li></ul>',
            ],
            [
                'title' => 'Benefits',
                'description' => '<ul><li>'.$this->faker->sentence().'</li><li>'.$this->faker->sentence().'</li></ul>',
            ],
        ];

        return [
            'mentor_id' => $mentorId, // can be overridden in seeder
            'category_ids' => $pickIds('course', 5),
            'title' => $title,
            'slug' => $slug,
            'overview' => $this->faker->paragraph(2),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'thumbnail' => null,
            'desciptions' => $descBlocks,
            'dead_line' => $deadline->format('Y-m-d'),
            'durations' => $this->faker->randomElement(['2 Months', '3 Months', '12 Weeks', '6 Weeks']),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'speciality_ids' => $pickIds('speciality', 4),
            'studydesign_type_ids' => $pickIds('student_design_type', 3),
            'contribution_type_ids' => $pickIds('contribution_type', 3),
            'university_hospital_ids' => $pickIds('university_hospital', 3),
            'place_ids' => $pickIds('place', 3),
            'experience_level_ids' => $pickIds('experience_level', 3),
            'is_future' => $this->faker->boolean(25),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}

