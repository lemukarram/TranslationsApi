<?php

namespace Database\Seeders;

use App\Models\TranslationGroup;
use Illuminate\Database\Seeder;

class TranslationGroupSeeder extends Seeder
{
    protected $essentialGroups = [
        'auth' => 'Authentication translations',
        'validation' => 'Validation messages',
        'ui' => 'User interface texts',
        'emails' => 'Email templates',
        'notifications' => 'Notification messages'
    ];

    protected $additionalGroups = [
        'products', 'checkout', 'admin', 'public', 'mobile',
        'dashboard', 'settings', 'reports', 'errors', 'api'
    ];

    public function run()
    {
        // Create essential groups if they don't exist
        foreach ($this->essentialGroups as $name => $description) {
            TranslationGroup::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Create additional groups that don't exist
        $existingGroups = TranslationGroup::pluck('name')->toArray();
        
        foreach ($this->additionalGroups as $group) {
            if (!in_array($group, $existingGroups)) {
                TranslationGroup::firstOrCreate(
                    ['name' => $group],
                    ['description' => "Translations for {$group} section"]
                );
            }
        }

        // Create random groups if you need more
        $this->createRandomGroups(5);
    }

    protected function createRandomGroups($count)
    {
        $existingGroups = TranslationGroup::pluck('name')->toArray();
        
        for ($i = 0; $i < $count; $i++) {
            $name = $this->generateUniqueGroupName($existingGroups);
            $existingGroups[] = $name;
            
            TranslationGroup::firstOrCreate(
                ['name' => $name],
                ['description' => "Auto-generated group: {$name}"]
            );
        }
    }

    protected function generateUniqueGroupName(&$existingNames)
    {
        $prefixes = ['app', 'web', 'system', 'user', 'admin'];
        $suffixes = ['texts', 'messages', 'content', 'labels', 'strings'];
        
        do {
            $name = strtolower(
                $prefixes[array_rand($prefixes)] . '_' . 
                $suffixes[array_rand($suffixes)] . '_' .
                rand(1, 1000)
            );
        } while (in_array($name, $existingNames));
        
        return $name;
    }
}