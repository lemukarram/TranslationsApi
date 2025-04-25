<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        // Create default required languages if they don't exist
        Language::firstOrCreate(['code' => 'en'], [
            'name' => 'English', 
            'is_active' => true
        ]);
        
        Language::firstOrCreate(['code' => 'fr'], [
            'name' => 'French', 
            'is_active' => true
        ]);
        
        Language::firstOrCreate(['code' => 'es'], [
            'name' => 'Spanish', 
            'is_active' => true
        ]);
        
        // Create additional random languages that don't exist
        $existingCodes = Language::pluck('code')->toArray();
        $possibleCodes = ['de', 'it', 'pt', 'ru', 'ja', 'zh', 'ar'];
        
        foreach ($possibleCodes as $code) {
            if (!in_array($code, $existingCodes)) {
                Language::firstOrCreate(['code' => $code], [
                    'name' => $this->getCountryName($code),
                    'is_active' => true
                ]);
            }
        }
        
        // Create completely random languages if you need more
        Language::factory()
            ->count(3) // Adjust based on how many you need
            ->create(['is_active' => true]);
    }
    
    protected function getCountryName($code)
    {
        $countries = [
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
            'ar' => 'Arabic'
        ];
        
        return $countries[$code] ?? $code;
    }
}