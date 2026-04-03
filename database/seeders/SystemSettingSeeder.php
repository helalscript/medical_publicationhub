<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'title' => 'PublicationHub',
            'system_name' => 'PublicationHub',
            'email' => 'publicationhub@gmail.com',
            'contact_number' => '+880000000',
            'company_open_hour' => '24 HOUR',
            'copyright_text' => '© Copyright 2025, All right reserved',
            'logo' => 'uploads/logos/logo.png',
            'favicon' => 'uploads/favicons/favicon.png',
            'address' => 'TEST',
            'description' => null,
            'web_url' => 'www.publicationhub.com',
        ]);
    }
}
