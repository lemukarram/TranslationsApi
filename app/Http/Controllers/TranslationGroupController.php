<?php

namespace App\Http\Controllers;

use App\Models\TranslationGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationGroupController extends Controller
{
    /**
     * Display a listing of translation groups
     */
    public function index()
    {
        return Cache::remember('translation_groups.all', now()->addDay(), function () {
            return TranslationGroup::orderBy('name')
                ->get(['id', 'name', 'description']);
        });
    }

    /**
     * Store a newly created translation group
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:translation_groups',
            'description' => 'nullable|string'
        ]);

        $group = TranslationGroup::create($validated);
        
        Cache::forget('translation_groups.all');
        
        return response()->json($group, 201);
    }

    /**
     * Display the specified translation group
     */
    public function show(TranslationGroup $translationGroup)
    {
        return response()->json($translationGroup);
    }

    /**
     * Update the specified translation group
     */
    public function update(Request $request, TranslationGroup $translationGroup)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:translation_groups,name,'.$translationGroup->id,
            'description' => 'nullable|string'
        ]);

        $translationGroup->update($validated);
        
        Cache::forget('translation_groups.all');
        
        return response()->json($translationGroup);
    }

    /**
     * Remove the specified translation group
     */
    public function destroy(TranslationGroup $translationGroup)
    {
        if ($translationGroup->translations()->exists()) {
            return response()->json([
                'message' => 'Cannot delete group with existing translations'
            ], 422);
        }

        $translationGroup->delete();
        
        Cache::forget('translation_groups.all');
        
        return response()->json(null, 204);
    }
}