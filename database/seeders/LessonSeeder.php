<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        if (Module::query()->doesntExist()) {
            $this->command?->warn('LessonSeeder skipped: no modules in database.');

            return;
        }

        Module::query()->orderBy('course_id')->orderBy('order_by')->orderBy('id')->each(function (Module $module) {
            $lessonCount = random_int(1, 4);

            for ($i = 1; $i <= $lessonCount; $i++) {
                Lesson::factory()
                    ->forModule($module, $i)
                    ->create();
            }
        });
    }
}
