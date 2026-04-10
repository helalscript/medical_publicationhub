<?php

namespace Database\Factories;

use App\Models\ContactUs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactUs>
 */
class ContactUsFactory extends Factory
{
    protected $model = ContactUs::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->optional(0.5)->randomElement(['support', 'general', 'feedback']),
            'full_name' => $this->faker->name(),
            'file' => null,
            'ip_address' => $this->faker->ipv4(),
            'email' => $this->faker->safeEmail(),
            'message' => $this->faker->paragraphs(rand(1, 3), true),
            'is_read' => $this->faker->boolean(35),
            'is_agree' => $this->faker->boolean(90),
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }
}
