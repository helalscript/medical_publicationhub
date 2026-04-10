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
                'answer' => 'Meta Scholars is an online platform designed to connect students, researchers, and institutions with academic resources, research opportunities, and learning programs.',
            ],
            [
                'category_id' => 1,
                'question' => 'Who can apply for research opportunities?',
                'answer' => 'Students, graduates, and early-career researchers from any discipline are eligible to apply for available research opportunities.',
            ],
            [
                'category_id' => 2,
                'question' => 'How do I apply for a research opportunity?',
                'answer' => 'You can apply by creating an account, visiting the research opportunities section, and submitting the required application form.',
            ],
            [
                'category_id' => 2,
                'question' => 'What documents do I need to apply?',
                'answer' => 'Typically, you will need an updated CV, academic transcripts, a statement of purpose, and any additional documents specified in the opportunity description.',
            ],
            [
                'category_id' => 2,
                'question' => 'How long does the application review process take?',
                'answer' => 'The review process usually takes 1–3 weeks, depending on the volume of applications and the specific research program.',
            ],
            [
                'category_id' => 3,
                'question' => 'What is Meta Academy?',
                'answer' => 'Meta Academy is the learning wing of Meta Scholars, offering curated courses, skill-building programs, and educational resources.',
            ],
            [
                'category_id' => 3,
                'question' => 'How do I access premium courses?',
                'answer' => 'Premium courses can be accessed by subscribing to a plan or purchasing individual course access from your account dashboard.',
            ],
            [
                'category_id' => 3,
                'question' => 'Are course certificates recognized?',
                'answer' => 'Yes, certificates issued by Meta Academy are recognized by partnering institutions and industry collaborators.',
            ],
            [
                'category_id' => 4,
                'question' => 'How do I create an account?',
                'answer' => 'To create an account, click the Sign Up button, fill in your basic information, and verify your email address.',
            ],
            [
                'category_id' => 4,
                'question' => 'How can I contact support?',
                'answer' => 'You can reach our support team through the Contact Us page, live chat, or by emailing support@metascholars.com.',
            ],
            [
                'category_id' => 4,
                'question' => 'Can institutions partner with Meta Scholars?',
                'answer' => 'Yes, institutions can partner with us for research collaboration, training programs, and academic initiatives.',
            ],
            [
                'category_id' => 4,
                'question' => 'I forgot my password. What should I do?',
                'answer' => 'Click on the Forgot Password option on the login page and follow the steps to reset your password.',
            ],
        ];



        // Existing rows (seeded earlier without faq_category_id) should become general.
        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

       
    }
}
