<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExportController extends Controller
{
    /**
     * Export translations in JSON format
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|string|exists:languages,code',
            'group' => 'nullable|string|exists:translation_groups,name',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
            'minify' => 'sometimes|boolean'
        ]);

        $cacheKey = $this->generateCacheKey($validated);
        
        return Cache::remember($cacheKey, now()->addHour(), function () use ($validated) {
            $startTime = microtime(true);
            
            $query = Translation::with(['language', 'group'])
                ->whereHas('language', function($query) use ($validated) {
                    $query->where('code', $validated['language']);
                });

            if (isset($validated['group'])) {
                $query->whereHas('group', function($query) use ($validated) {
                    $query->where('name', $validated['group']);
                });
            }

            if (isset($validated['tags'])) {
                foreach ($validated['tags'] as $tag) {
                    $query->whereJsonContains('tags', $tag);
                }
            }

            $translations = $query->get(['key', 'value', 'group_id', 'tags']);

            $result = [];
            foreach ($translations as $translation) {
                $result[$translation->group->name][$translation->key] = $translation->value;
            }

            $duration = (microtime(true) - $startTime) * 1000;
            Log::info("Export generated", [
                'language' => $validated['language'],
                'group' => $validated['group'] ?? 'all',
                'tags' => $validated['tags'] ?? [],
                'duration_ms' => $duration,
                'count' => $translations->count()
            ]);

            return $result;
        });
    }

    /**
     * Generate a unique cache key for the export request
     */
    protected function generateCacheKey(array $params): string
    {
        ksort($params);
        return 'translations.export.' . md5(json_encode($params));
    }

    /**
     * Clear export cache
     */
    public function clearCache(Request $request)
    {
        $validated = $request->validate([
            'language' => 'sometimes|string|exists:languages,code',
            'group' => 'sometimes|string|exists:translation_groups,name'
        ]);

        if (empty($validated)) {
            Cache::flush();
            return response()->json(['message' => 'All export caches cleared']);
        }

        $cacheKey = $this->generateCacheKey($validated);
        Cache::forget($cacheKey);

        return response()->json(['message' => 'Specific export cache cleared']);
    }
}