<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'title' => 'باكيج الصيف - 10% خصم',
                'description' => 'اشترك بأي مشروع بحثي وراح تحصل بشكل تلقائي على صلاحية دخول دائمة لجميع كورسات المنصة... عرض لن يتكرر',
                'icon' => '🚀',
                'promo_code' => 'SUMMER10',
                'discount_percentage' => 10,
                'end_date' => now()->addDays(23)->endOfDay(),
                'background_color' => '#E3F2FD',
                'sort_order' => 1,
                'status' => 'active',
            ],
            [
                'title' => 'Research Starter — 15% Off',
                'description' => 'Get 15% off your first research project subscription. Includes mentor matching and platform course access.',
                'icon' => '📚',
                'promo_code' => 'START15',
                'discount_percentage' => 15,
                'end_date' => now()->addDays(14)->endOfDay(),
                'background_color' => '#E8F5E9',
                'sort_order' => 2,
                'status' => 'active',
            ],
            [
                'title' => 'Meta Academy Bundle — 20% Off',
                'description' => 'Limited-time discount on selected Meta Academy courses when you join an active opportunity chat.',
                'icon' => '🎓',
                'promo_code' => 'META20',
                'discount_percentage' => 20,
                'end_date' => now()->addDays(30)->endOfDay(),
                'background_color' => '#FFF3E0',
                'sort_order' => 3,
                'status' => 'active',
            ],
            [
                'title' => 'Publication Support — 5% Off',
                'description' => 'Save on publication formatting and submission support packages. Valid for one round of journal submission.',
                'icon' => '📝',
                'promo_code' => 'PUB5',
                'discount_percentage' => 5,
                'end_date' => now()->addDays(7)->endOfDay(),
                'background_color' => '#F3E5F5',
                'sort_order' => 4,
                'status' => 'active',
            ],
        ];

        foreach ($offers as $offer) {
            Offer::updateOrCreate(
                ['promo_code' => $offer['promo_code']],
                $offer
            );
        }
    }
}
