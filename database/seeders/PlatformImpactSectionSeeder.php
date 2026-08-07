<?php

namespace Database\Seeders;

use App\Enums\Page;
use App\Enums\Section;
use App\Models\CMS;
use Illuminate\Database\Seeder;

class PlatformImpactSectionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Container (heading) ──────────────────────────────────────
        CMS::updateOrCreate(
            [
                'page'    => Page::HomePage->value,
                'section' => Section::PlatformImpactSection->value,
            ],
            [
                'title'       => 'PublicationHub by the Numbers',
                'sub_title'   => 'Platform Impact',
                'description' => 'A growing platform connecting researchers, services, and academy learning worldwide.',
                'status'      => 'active',
            ]
        );

        // ── Stat Items ────────────────────────────────────────────────
        $stats = [
            // Opportunities column
            ['title' => '500+',  'sub_title' => 'Research Opportunities', 'button_text' => 'opportunities'],
            ['title' => '150+',  'sub_title' => 'Expert Mentors',         'button_text' => 'opportunities'],
            ['title' => '10k+',  'sub_title' => 'Active Researchers',     'button_text' => 'opportunities'],
            // Services column
            ['title' => '12+',   'sub_title' => 'Research Services',      'button_text' => 'services'],
            ['title' => '800+',  'sub_title' => 'Projects Completed',     'button_text' => 'services'],
            ['title' => '98%',   'sub_title' => 'Client Satisfaction',    'button_text' => 'services'],
            // Academy column
            ['title' => '20+',   'sub_title' => 'Academy Courses',        'button_text' => 'academy'],
            ['title' => '5k+',   'sub_title' => 'Students Enrolled',      'button_text' => 'academy'],
            ['title' => '50+',   'sub_title' => 'Expert Instructors',     'button_text' => 'academy'],
        ];

        foreach ($stats as $stat) {
            CMS::updateOrCreate(
                [
                    'page'      => Page::HomePage->value,
                    'section'   => Section::PlatformImpactSectionContent->value,
                    'title'     => $stat['title'],
                    'sub_title' => $stat['sub_title'],
                ],
                [
                    'button_text' => $stat['button_text'],
                    'status'      => 'active',
                ]
            );
        }
    }
}
