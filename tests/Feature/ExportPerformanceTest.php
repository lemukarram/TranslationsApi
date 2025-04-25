<?php

namespace Tests\Performance;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExportPerformanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_large_export_performance()
    {
        // Create test data
        $language = Language::factory()->create(['code' => 'en']);
        $group = TranslationGroup::factory()->create(['name' => 'auth']);
        
        Translation::factory()->count(100000)->create([
            'language_id' => $language->id,
            'group_id' => $group->id,
            'tags' => ['web']
        ]);

        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Warm up cache
        $this->getJson('/api/export?language=en&group=auth', [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Measure performance
        $start = microtime(true);
        $response = $this->getJson('/api/export?language=en&group=auth', [
            'Authorization' => 'Bearer ' . $token
        ]);
        $duration = (microtime(true) - $start) * 1000; // in milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, "Export of 100k records took {$duration}ms");
    }
}