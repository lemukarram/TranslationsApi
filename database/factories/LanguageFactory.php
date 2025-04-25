<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    public function definition()
    {
        $codes = ['en', 'fr', 'es', 'de', 'it', 'pt', 'ru', 'ja', 'zh', 'ar'];
        $existingCodes = \App\Models\Language::pluck('code')->toArray();
        $availableCodes = array_diff($codes, $existingCodes);
        
        // If no unique codes left, generate random ones
        if (empty($availableCodes)) {
            return [
                'code' => $this->faker->unique()->languageCode,
                'name' => $this->faker->country,
                'is_active' => $this->faker->boolean(90),
            ];
        }
        
        return [
            'code' => $this->faker->unique()->randomElement($availableCodes),
            'name' => $this->faker->country,
            'is_active' => $this->faker->boolean(90),
        ];
    }
}