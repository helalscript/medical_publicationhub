<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Clergy;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
        
        User::factory()->create([
            'name' => 'Coordinator',
            'email' => 'coordinator@coordinator.com',
            'password' => Hash::make('12345678'),
            'role' => 'coordinator',
        ]);
        User::factory()->create([
            'name' => 'Mentor',
            'email' => 'mentor@mentor.com',
            'password' => Hash::make('12345678'),
            'role' => 'mentor',
        ]);
        User::factory()->create([
            'name' => 'Researcher',
            'email' => 'researcher@researcher.com',
            'password' => Hash::make('12345678'),
            'role' => 'researcher',
        ]);

       
        $this->call(SystemSettingSeeder::class);
        $this->call(CMSSeeder::class);
        $this->call(DynamicPageSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(FaqSeeder::class);
    }
}
