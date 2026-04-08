<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates up to 10 demo accounts: 5 mentors (with mentor profiles) + 5 researchers.
 * Password for all: 12345678
 */
class MentorResearcherDemoSeeder extends Seeder
{
    public function run(): void
    {
        Mentor::factory()->count(5)->create();

        User::factory()
            ->researcherAccount()
            ->count(5)
            ->create();
    }
}
