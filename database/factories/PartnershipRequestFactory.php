<?php

namespace Database\Factories;

use App\Models\PartnershipRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartnershipRequest>
 */
class PartnershipRequestFactory extends Factory
{
    protected $model = PartnershipRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'organization' => $this->faker->boolean(70) ? $this->faker->company() : null,
            'email' => $this->faker->boolean(85) ? $this->faker->safeEmail() : null,
            'phone' => $this->faker->boolean(60) ? $this->faker->phoneNumber() : null,
            'message' => $this->faker->boolean(80) ? $this->faker->paragraphs(2, true) : null,
            'ip' => $this->faker->ipv4(),
            'status' => $this->faker->randomElement(['pending', 'pending', 'pending', 'reviewed']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function reviewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reviewed',
        ]);
    }
}
