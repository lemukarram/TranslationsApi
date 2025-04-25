<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\TranslationGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = \App\Models\Translation::class;

    public function definition()
    {
        // Use existing languages first
        $language = Language::inRandomOrder()->first() ?? Language::factory()->create();
        
        // Use existing groups first
        $group = TranslationGroup::inRandomOrder()->first() ?? TranslationGroup::factory()->create();

        $tags = ['web', 'mobile', 'desktop', 'admin', 'public', 'backend', 'frontend', 'api'];
        
        return [
            'group_id' => $group->id,
            'key' => $this->faker->unique()->word . '_' . $this->faker->randomNumber(3),
            'value' => $this->faker->sentence,
            'language_id' => $language->id,
            'tags' => $this->faker->randomElements($tags, $this->faker->numberBetween(1, 3)),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}