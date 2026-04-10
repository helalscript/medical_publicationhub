<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        $course = Course::query()->inRandomOrder()->first();
        if (! $course) {
            throw new \RuntimeException('ModuleFactory requires at least one course. Run CourseSeeder first.');
        }

        return [
            'course_id' => $course->id,
            'mentor_id' => $this->mentorIdForCourse($course),
            'type' => $this->faker->randomElement(['video', 'document', 'mixed']),
            'title' => 'Module: '.$this->faker->words(4, true),
            'video' => null,
            'attached_file' => null,
            'attached_text' => $this->faker->optional(0.4)->paragraph(),
            'document' => null,
            'order_by' => $this->faker->numberBetween(0, 20),
            'durations' => $this->faker->optional(0.8)->randomElement(['15 min', '30 min', '45 min', '1 hour', '2 hours']),
            'is_locked' => $this->faker->boolean(15),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive']),
        ];
    }

    public function forCourse(Course $course, ?int $orderBy = null): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => $course->id,
            'mentor_id' => $this->mentorIdForCourse($course),
            'order_by' => $orderBy ?? ($attributes['order_by'] ?? 0),
        ]);
    }

    private function mentorIdForCourse(Course $course): ?int
    {
        $ids = array_values(array_filter(array_map('intval', (array) ($course->mentor_ids ?? []))));
        if ($ids !== []) {
            return (int) $this->faker->randomElement($ids);
        }

        return Mentor::query()->where('status', 'active')->inRandomOrder()->value('id');
    }
}
