<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Illuminate\Notifications\DatabaseNotification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\WelcomeNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => User::factory(),
            'data' => [
                'title' => $this->faker->sentence(),
                'message' => $this->faker->paragraph(),
                'type' => 'welcome',
                'image' => 'http://ibraheemaltamim.test/notification-icon/png-transparent-tea-cup-color-gr.png',
                'deep_link' => [
                    'app' => 'myapp://welcome',
                    'web' => 'http://ibraheemaltamim.test/welcome'
                ],
                'data' => [
                    'user_id' => 1, // Placeholder, usually overridden in seeder
                    'email' => $this->faker->safeEmail(),
                ],
                'footer' => 'Thanks for joining us!',
                'timestamp' => now()->toISOString(),
            ],
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
