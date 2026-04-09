<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one mentor exists for required mentor_id
        $mentor = Mentor::query()->inRandomOrder()->first();
        if (! $mentor) {
            $mentorUser = User::factory()->create([
                'name' => 'Seed Mentor',
                'email' => 'seed_mentor@mentor.com',
                'password' => Hash::make('12345678'),
                'role' => 'mentor',
            ]);
            $mentor = Mentor::create([
                'user_id' => $mentorUser->id,
                'status' => 'active',
            ]);
        }

        Opportunity::factory()
            ->count(20)
            ->state(fn () => ['mentor_id' => $mentor->id, 'status' => 'active'])
            ->create();
    }
}

