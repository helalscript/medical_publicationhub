<?php

namespace Database\Factories;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mentor>
 */
class MentorFactory extends Factory
{
    protected $model = Mentor::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'user_id' => User::factory()->mentorAccount()->state([
                'name' => $name,
            ]),
            'bio' => $this->faker->optional(0.6)->paragraph(),
            'university' => $this->faker->optional(0.7)->company(),
            'department' => $this->faker->optional(0.5)->word(),
            'social_links' => null,
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
