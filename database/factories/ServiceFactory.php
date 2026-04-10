<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);

        return [
            'title' => $title,
            'icon' => null, // admin can upload later
            'overview' => $this->faker->paragraph(2),
            'description' => '<p>'.$this->faker->paragraph(4).'</p>',
            'starting_price' => $this->faker->randomFloat(2, 10, 500),
            'whats_app_num' => $this->faker->boolean(40) ? $this->faker->e164PhoneNumber() : null,
            'is_features' => $this->faker->boolean(25),
            'created_by' => User::query()->inRandomOrder()->value('id'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
