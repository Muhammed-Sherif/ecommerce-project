<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Hero Section
            ['key' => 'landing.hero_title', 'value' => 'Design Your Dream Space', 'group' => 'landing', 'type' => 'text'],
            ['key' => 'landing.hero_subtitle', 'value' => 'Discover furniture that blends perfectly with your personality. Minimalist, modern, and timeless designs for every room.', 'group' => 'landing', 'type' => 'text'],
            ['key' => 'landing.cta_text', 'value' => 'Shop Now', 'group' => 'landing', 'type' => 'text'],
            ['key' => 'landing.hero_image', 'value' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1200&q=80', 'group' => 'landing', 'type' => 'image'],
            
            // About Section (Why Choose Us)
            ['key' => 'landing.about_title', 'value' => 'Why Choose Us?', 'group' => 'landing', 'type' => 'text'],
            ['key' => 'landing.about_text', 'value' => 'We provide an exceptional experience from browsing to delivery, ensuring you get the best value and quality.', 'group' => 'landing', 'type' => 'text'],
            
            // General
            ['key' => 'site.name', 'value' => 'AR-FURNITURE', 'group' => 'general', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
