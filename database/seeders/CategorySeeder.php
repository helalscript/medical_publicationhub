<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['type' => 'faq', 'name' => 'General', 'slug' => 'general', 'description' => 'General', 'status' => 'active'],
            ['type' => 'faq', 'name' => 'Application', 'slug' => 'application', 'description' => 'Application', 'status' => 'active'],
            ['type' => 'faq', 'name' => 'Meta Academy', 'slug' => 'meta_academy', 'description' => 'Meta Academy', 'status' => 'active'],
            ['type' => 'faq', 'name' => 'Account_support', 'slug' => 'account_support', 'description' => 'Account Support', 'status' => 'active']

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
