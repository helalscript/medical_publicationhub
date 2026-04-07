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
            ['type' => 'faq', 'name' => 'Account_support', 'slug' => 'account_support', 'description' => 'Account Support', 'status' => 'active'],
            //opportunity categories
            ['type' => 'opportunity', 'name' => 'Data Science', 'slug' => 'data-science', 'description' => 'Data Science', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Neurology', 'slug' => 'neurology', 'description' => 'Neurology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Cardiology', 'slug' => 'cardiology', 'description' => 'Cardiology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Endocrinology', 'slug' => 'endocrinology', 'description' => 'Endocrinology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Gastroenterology', 'slug' => 'gastroenterology', 'description' => 'Gastroenterology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Hematology', 'slug' => 'hematology', 'description' => 'Hematology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Hepatology', 'slug' => 'hepatology', 'description' => 'Hepatology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Immunology', 'slug' => 'immunology', 'description' => 'Immunology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Oncology', 'slug' => 'oncology', 'description' => 'Oncology', 'status' => 'active'],
            ['type' => 'opportunity', 'name' => 'Machine Learning', 'slug' => 'machine-learning', 'description' => 'Machine Learning', 'status' => 'active'],
           


            //course categories
            ['type' => 'course', 'name' => 'Data Science', 'slug' => 'data-science', 'description' => 'Data Science', 'status' => 'active'],
            ['type' => 'course', 'name' => 'Data Analytics ', 'slug' => 'data-analytics', 'description' => 'Data Analytics', 'status' => 'active'],
            ['type' => 'course', 'name' => 'Data Engineering', 'slug' => 'data-engineering', 'description' => 'Data Engineering', 'status' => 'active'],
           ['type' => 'course', 'name' => 'Data Visualization', 'slug' => 'data-visualization', 'description' => 'Data Visualization', 'status' => 'active'],

           //place categories
           ['type' => 'place', 'name' => 'Online', 'slug' => 'online', 'description' => 'Online', 'status' => 'active'],
           ['type' => 'place', 'name' => 'On Campus', 'slug' => 'on-campus', 'description' => 'On Campus', 'status' => 'active'],
           ['type' => 'place', 'name' => 'Hybrid', 'slug' => 'hybrid', 'description' => 'Hybrid', 'status' => 'active'],
           ['type' => 'place', 'name' => 'Other', 'slug' => 'other', 'description' => 'Other', 'status' => 'active'],

           //contribution type categories
           ['type' => 'contribution_type', 'name' => 'Research', 'slug' => 'research', 'description' => 'Research', 'status' => 'active'],
           ['type' => 'contribution_type', 'name' => 'Publication', 'slug' => 'publication', 'description' => 'Publication', 'status' => 'active'],
           ['type' => 'contribution_type', 'name' => 'Other', 'slug' => 'other', 'description' => 'Other', 'status' => 'active'],

           //student design type categories
           ['type' => 'student_design_type', 'name' => 'Research', 'slug' => 'research', 'description' => 'Research', 'status' => 'active'],
           ['type' => 'student_design_type', 'name' => 'Publication', 'slug' => 'publication', 'description' => 'Publication', 'status' => 'active'],
           ['type' => 'student_design_type', 'name' => 'Other', 'slug' => 'other', 'description' => 'Other', 'status' => 'active'],

           //experience level categories
           ['type' => 'experience_level', 'name' => 'Beginner', 'slug' => 'beginner', 'description' => 'Beginner', 'status' => 'active'],
           ['type' => 'experience_level', 'name' => 'Intermediate', 'slug' => 'intermediate', 'description' => 'Intermediate', 'status' => 'active'],
           ['type' => 'experience_level', 'name' => 'Advanced', 'slug' => 'advanced', 'description' => 'Advanced', 'status' => 'active'],
           ['type' => 'experience_level', 'name' => 'Any', 'slug' => 'any', 'description' => 'Any', 'status' => 'active'],

           //speciality categories
           ['type' => 'speciality', 'name' => 'Data Science', 'slug' => 'data-science', 'description' => 'Data Science', 'status' => 'active'],
           ['type' => 'speciality', 'name' => 'Data Analytics', 'slug' => 'data-analytics', 'description' => 'Data Analytics', 'status' => 'active'],
           ['type' => 'speciality', 'name' => 'Data Engineering', 'slug' => 'data-engineering', 'description' => 'Data Engineering', 'status' => 'active'],
           ['type' => 'speciality', 'name' => 'Data Visualization', 'slug' => 'data-visualization', 'description' => 'Data Visualization', 'status' => 'active'],

           //meta_academy categories
           ['type' => 'meta_academy', 'name' => 'Research Methods', 'slug' => 'research-methods', 'description' => 'Research Methods', 'status' => 'active'],
           ['type' => 'meta_academy', 'name' => 'Scientific Writing', 'slug' => 'scientific-writing', 'description' => 'Scientific Writing', 'status' => 'active'],
           ['type' => 'meta_academy', 'name' => 'Data Analysis', 'slug' => 'data-analysis', 'description' => 'Data Analysis', 'status' => 'active'],
           ['type' => 'meta_academy', 'name' => 'Career Development', 'slug' => 'career-development', 'description' => 'Career Development', 'status' => 'active'],
        ];     

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
