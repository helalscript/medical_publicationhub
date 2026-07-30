<?php

namespace Database\Seeders;

use App\Models\Rubric;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultRubrics = [
            [
                'title' => 'Research Quality Rubric',
                'description' => 'Standard rubric for evaluating overall research quality and structure.',
                'is_global' => true,
                'mentor_id' => null,
                'criteria' => [
                    ['name' => 'Clarity', 'points' => 25],
                    ['name' => 'Methodology', 'points' => 25],
                    ['name' => 'Analysis', 'points' => 25],
                    ['name' => 'Conclusion', 'points' => 25],
                ],
            ],
            [
                'title' => 'Literature Review Rubric',
                'description' => 'Rubric for assessing background literature scope and critical analysis.',
                'is_global' => true,
                'mentor_id' => null,
                'criteria' => [
                    ['name' => 'Sources Coverage', 'points' => 30],
                    ['name' => 'Critical Analysis', 'points' => 40],
                    ['name' => 'Structure & Coherence', 'points' => 30],
                ],
            ],
            [
                'title' => 'Writing & Formatting Rubric',
                'description' => 'Rubric to evaluate language, grammar, references, and presentation.',
                'is_global' => true,
                'mentor_id' => null,
                'criteria' => [
                    ['name' => 'Spelling & Grammar', 'points' => 30],
                    ['name' => 'Formatting & Citations', 'points' => 30],
                    ['name' => 'Flow & Readability', 'points' => 40],
                ],
            ],
        ];

        foreach ($defaultRubrics as $rubricData) {
            Rubric::updateOrCreate(
                ['title' => $rubricData['title'], 'is_global' => true],
                [
                    'description' => $rubricData['description'],
                    'mentor_id' => $rubricData['mentor_id'],
                    'criteria' => $rubricData['criteria'],
                ]
            );
        }
    }
}
