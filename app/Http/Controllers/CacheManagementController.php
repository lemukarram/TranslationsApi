<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class CacheManagementController extends Controller
{
    /**
     * Get cache statistics
     */
    public function stats()
    {
        return response()->json([
            'memory_usage' => memory_get_usage(),
            'memory_peak' => memory_get_peak_usage(),
            'cache_driver' => config('cache.default'),
            'cache_prefix' => config('cache.prefix')
        ]);
    }

    /**
     * Clear specific caches
     */
    public function clear(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:translations,exports,all'
        ]);

        switch ($validated['type']) {
            case 'translations':
                Cache::tags(['translations'])->flush();
                break;
            case 'exports':
                Cache::tags(['exports'])->flush();
                break;
            case 'all':
                Cache::flush();
                break;
        }

        return response()->json(['message' => "{$validated['type']} cache cleared"]);
    }
}