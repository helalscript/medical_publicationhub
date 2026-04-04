<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        



        $faqs = [
            [
                'category_id' => 1,
                'question' => 'What is Meta Scholars?',
                'answer' => "",
            ],
            [
                'category_id' => 1,
                'question' => 'Who can apply for research opportunities?',
                'answer' => "",
            ],
            [
                'category_id' => 2,
                'question' => 'How do I apply for a research opportunity?',
                'answer' => "",
            ],
            [
                'category_id' => 2,
                'question' => 'What documents do I need to apply?',
                'answer' => "",
            ],
            [
                'category_id' => 2,
                'question' => 'How long does the application review process take?',
                'answer' => "",
            ],
            [
                'category_id' => 3,
                'question' => 'What is Meta Academy?',
                'answer' => "",
            ],
            [
                'category_id' => 3,
                'question' => 'How do I access premium courses?',
                'answer' => "",
            ],
            [
                'category_id' => 3,
                'question' => 'Are course certificates recognized?',
                'answer' => "",
            ],
            [
                'category_id' => 4,
                'question' => 'How do I create an account?',
                'answer' => "",
            ],
            
            [
                'category_id' => 4,
                'question' => 'How can I contact support?',
                'answer' => "",
            ],
            
            [
                'category_id' => 4,
                'question' => 'Can institutions partner with Meta Scholars?',
                'answer' => "",
            ],
            
            [
                'category_id' => 4,
                'question' => 'I forgot my password. What should I do?',
                'answer' => "",
            ],
            
        ];



        // Existing rows (seeded earlier without faq_category_id) should become general.
        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

       
    }
}
