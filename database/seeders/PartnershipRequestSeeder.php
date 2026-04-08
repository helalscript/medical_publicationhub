<?php

namespace Database\Seeders;

use App\Models\PartnershipRequest;
use Illuminate\Database\Seeder;

class PartnershipRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnershipRequest::factory()
            ->count(25)
            ->create();
    }
}
