<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TranslationGroup;

class TranslationGroupFactory extends Factory
{
    protected $model = TranslationGroup::class;

    public function definition()
    {
        $existingNames = TranslationGroup::pluck('name')->toArray();
        $baseNames = ['auth', 'validation', 'ui', 'emails', 'notifications'];
        
        // Try to use base names first
        foreach ($baseNames as $name) {
            if (!in_array($name, $existingNames)) {
                return [
                    'name' => $name,
                    'description' => $this->faker->sentence,
                ];
            }
        }

        // If all base names are taken, generate unique ones
        do {
            $name = $this->faker->unique()->word . '_' . $this->faker->randomNumber(3);
        } while (in_array($name, $existingNames));
        
        return [
            'name' => $name,
            'description' => $this->faker->sentence,
        ];
    }
}