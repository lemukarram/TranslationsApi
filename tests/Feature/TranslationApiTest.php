<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_can_create_translation()
    {
        $language = Language::factory()->create();
        $group = TranslationGroup::factory()->create();

        $response = $this->postJson('/api/translations', [
            'key' => 'welcome_message',
            'value' => 'Welcome to our application',
            'language_id' => $language->id,
            'group_id' => $group->id,
            'tags' => ['web', 'mobile']
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'key', 'value', 'tags'
            ]);
    }

    public function test_can_export_translations()
    {
        $language = Language::factory()->create(['code' => 'en']);
        $group = TranslationGroup::factory()->create(['name' => 'auth']);
        
        Translation::factory()->create([
            'language_id' => $language->id,
            'group_id' => $group->id,
            'key' => 'login',
            'value' => 'Please login',
            'tags' => ['web']
        ]);

        $response = $this->getJson('/api/export?language=en&group=auth', [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'auth' => ['login']
            ]);
    }

    public function test_export_performance()
    {
        // Generate test data
        $language = Language::factory()->create(['code' => 'en']);
        $group = TranslationGroup::factory()->create(['name' => 'auth']);
        
        Translation::factory()->count(1000)->create([
            'language_id' => $language->id,
            'group_id' => $group->id,
            'tags' => ['web']
        ]);

        // Test performance
        $start = microtime(true);
        $response = $this->getJson('/api/export?language=en&group=auth', [
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $duration = (microtime(true) - $start) * 1000; // in milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, "Export took {$duration}ms, expected <500ms");
    }
}