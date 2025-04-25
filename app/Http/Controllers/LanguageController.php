<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    /**
     * Display a listing of active languages
     */
    public function index()
    {
        return Cache::remember('languages.active', now()->addDay(), function () {
            return Language::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']);
        });
    }

    /**
     * Store a newly created language
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages',
            'name' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean'
        ]);

        $language = Language::create($validated);
        
        Cache::forget('languages.active');
        
        return response()->json($language, 201);
    }

    /**
     * Display the specified language
     */
    public function show(Language $language)
    {
        return response()->json($language);
    }

    /**
     * Update the specified language
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:10|unique:languages,code,'.$language->id,
            'name' => 'sometimes|required|string|max:255',
            'is_active' => 'sometimes|boolean'
        ]);

        $language->update($validated);
        
        Cache::forget('languages.active');
        
        return response()->json($language);
    }

    /**
     * Remove the specified language
     */
    public function destroy(Language $language)
    {
        if ($language->translations()->exists()) {
            return response()->json([
                'message' => 'Cannot delete language with existing translations'
            ], 422);
        }

        $language->delete();
        
        Cache::forget('languages.active');
        
        return response()->json(null, 204);
    }
}