<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    /**
     * Display a paginated listing of translations
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'key' => 'sometimes|string',
            'value' => 'sometimes|string',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:255',
            'language' => 'sometimes|string|exists:languages,code',
            'group' => 'sometimes|string|exists:translation_groups,name'
        ]);

        $cacheKey = 'translations.index.' . md5(json_encode($validated)) . '.page.' . $request->get('page', 1);
        return Cache::remember($cacheKey, now()->addHour(), function () use ($validated) {
            return Translation::with(['language:id,code,name', 'group:id,name'])
                ->filter($validated)
                ->orderBy('key')
                ->paginate($validated['per_page'] ?? 20);
        });
    }

    /**
     * Store a newly created translation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:translation_groups,id',
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255'
        ]);

        $translation = Translation::create($validated);
        
        $this->clearTranslationCaches();
        
        return response()->json($translation->load(['language', 'group']), 201);
    }

    /**
     * Display the specified translation
     */
    public function show(Translation $translation)
    {
        return response()->json($translation->load(['language', 'group']));
    }

    /**
     * Update the specified translation
     */
    public function update(Request $request, Translation $translation)
    {
        $validated = $request->validate([
            'group_id' => 'sometimes|required|exists:translation_groups,id',
            'key' => 'sometimes|required|string|max:255',
            'value' => 'sometimes|required|string',
            'language_id' => 'sometimes|required|exists:languages,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255'
        ]);

        $translation->update($validated);
        
        $this->clearTranslationCaches();
        
        return response()->json($translation->load(['language', 'group']));
    }

    /**
     * Remove the specified translation
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();
        
        $this->clearTranslationCaches();
        
        return response()->json(null, 204);
    }

    /**
     * Clear all relevant translation caches
     */
    protected function clearTranslationCaches()
    {
        Cache::flush(); // For simplicity, flush all caches
        // In production, you might want to be more specific:
        // Cache::tags(['translations'])->flush();
    }
}