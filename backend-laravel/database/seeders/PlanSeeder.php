<?php

namespace Database\Seeders;

use App\Models\Plans;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Lite Pack',
                'slug' => 'lite-pack',
                'description' => 'Faqat asosiy funksiyalar va eng arzon narx.',
                'price_usd' => 5.00,
                'included_models' => json_encode(['gpt-4o-mini']),
                'monthly_token_limit' => 20000,
                'max_pages_per_ocr' => 2,
                'has_teacher_panel' => false,
                'has_admin_panel' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Starter Pack',
                'slug' => 'starter-pack',
                'description' => 'Yangi o\'rganuvchilar uchun qulay imkoniyatlar.',
                'price_usd' => 10.00,
                'included_models' => json_encode(['gpt-4o-mini', 'gemini-1.5-flash']),
                'monthly_token_limit' => 50000,
                'max_pages_per_ocr' => 5,
                'has_teacher_panel' => false,
                'has_admin_panel' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Standard (Medium)',
                'slug' => 'standard-medium',
                'description' => 'O\'qituvchilar uchun mos keladigan o\'rtacha tarif.',
                'price_usd' => 25.00,
                'included_models' => json_encode(['gpt-4o-mini', 'gpt-4o', 'gemini-1.5-flash']),
                'monthly_token_limit' => 150000,
                'max_pages_per_ocr' => 20,
                'has_teacher_panel' => true, // O'qituvchi paneli mavjud
                'has_admin_panel' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Katta jamoalar va to\'liq boshqaruv uchun.',
                'price_usd' => 50.00,
                'included_models' => json_encode(['gpt-4o', 'claude-3-5-sonnet', 'gemini-1.5-pro']),
                'monthly_token_limit' => 500000,
                'max_pages_per_ocr' => 100,
                'has_teacher_panel' => true,
                'has_admin_panel' => true, // Admin panel ham qo'shildi
                'is_active' => true,
            ],
            [
                'name' => 'Ultimate AI',
                'slug' => 'ultimate-ai',
                'description' => 'Barcha modellar va maksimal darajadagi boshqaruv.',
                'price_usd' => 99.00,
                'included_models' => json_encode(['gpt-4o', 'o1-preview', 'claude-3-5-sonnet', 'gemini-1.5-pro']),
                'monthly_token_limit' => 2000000,
                'max_pages_per_ocr' => 1000,
                'has_teacher_panel' => true,
                'has_admin_panel' => true,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plans::create($plan);
        }
    }
}
