<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::query()->doesntExist()) {
            $this->command?->warn('ModuleSeeder skipped: no courses in database.');

            return;
        }

        Course::query()->orderBy('id')->each(function (Course $course) {
            $moduleCount = random_int(2, 5);

            for ($i = 1; $i <= $moduleCount; $i++) {
                Module::factory()
                    ->forCourse($course, $i)
                    ->create();
            }
        });
    }
}
