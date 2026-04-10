<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        $module = Module::query()->inRandomOrder()->first();
        if (! $module) {
            throw new \RuntimeException('LessonFactory requires at least one module. Run ModuleSeeder first.');
        }

        return [
            'module_id' => $module->id,
            'title' => $this->faker->sentence(4),
            'file' => null,
            'video' => null,
            'attached_text' => $this->faker->optional(0.35)->paragraph(),
            'attached_file' => null,
            'document' => null,
            'order_by' => $this->faker->numberBetween(0, 20),
            'durations' => $this->faker->optional(0.7)->randomElement(['10 min', '20 min', '25 min', '35 min']),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive']),
        ];
    }

    public function forModule(Module $module, ?int $orderBy = null): static
    {
        return $this->state(fn (array $attributes) => [
            'module_id' => $module->id,
            'order_by' => $orderBy ?? ($attributes['order_by'] ?? 0),
        ]);
    }
}
