<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    public function run()
    {
        $batchSize = 1000; // Process in batches of 1000
        $totalRecords = 100000;
        $batches = ceil($totalRecords / $batchSize);

        // Get existing languages and groups
        $languageIds = Language::pluck('id')->toArray();
        $groupIds = TranslationGroup::pluck('id')->toArray();

        if (empty($languageIds)) {
            throw new \Exception('No languages found. Run LanguageSeeder first.');
        }

        if (empty($groupIds)) {
            throw new \Exception('No translation groups found. Run TranslationGroupSeeder first.');
        }

        for ($i = 0; $i < $batches; $i++) {
            $translations = [];
            
            for ($j = 0; $j < $batchSize; $j++) {
                $translations[] = [
                    'group_id' => $groupIds[array_rand($groupIds)],
                    'key' => 'key_' . ($i * $batchSize + $j) . '_' . uniqid(),
                    'value' => 'Translation value for ' . ($i * $batchSize + $j),
                    'language_id' => $languageIds[array_rand($languageIds)],
                    'tags' => json_encode([$this->getRandomTag()]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('translations')->insert($translations);
            $this->command->info("Inserted " . ($i + 1) * $batchSize . " of $totalRecords translations");
        }
    }

    protected function getRandomTag()
    {
        $tags = ['web', 'mobile', 'desktop', 'admin', 'public'];
        return $tags[array_rand($tags)];
    }
}