<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OpportunityApplication>
 */
class OpportunityApplicationFactory extends Factory
{
    protected $model = OpportunityApplication::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::query()->where('role', 'researcher')->inRandomOrder()->value('id');
        $opportunityId = Opportunity::query()->inRandomOrder()->value('id');

        return [
            'user_id' => $userId,
            'opportunity_id' => $opportunityId,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->optional(0.6)->phoneNumber(),
            'highest_edu_level' => $this->faker->optional(0.7)->randomElement(['BSc', 'MSc', 'MD', 'PhD']),
            'institution' => $this->faker->optional(0.8)->company(),
            'field_of_study' => $this->faker->optional(0.7)->word(),
            'research_experience' => $this->faker->optional(0.7)->paragraph(),
            'current_position' => $this->faker->optional(0.6)->jobTitle(),
            'linkedin_profile' => $this->faker->optional(0.4)->url(),
            'personal_website' => $this->faker->optional(0.3)->url(),
            'documents' => null,
            'cover_letter' => $this->faker->optional(0.8)->paragraphs(2, true),
            'accept_term' => true,
            'status' => $this->faker->randomElement(['pending', 'pending', 'accept', 'reject']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accept',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reject',
        ]);
    }
}
