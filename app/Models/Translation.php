<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'key', 'value', 'language_id', 'tags'];

    protected $casts = [
        'tags' => 'array',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function group()
    {
        return $this->belongsTo(TranslationGroup::class);
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['key'] ?? false, fn($query, $key) => 
            $query->where('key', 'like', "%{$key}%")
        );

        $query->when($filters['value'] ?? false, fn($query, $value) => 
            $query->where('value', 'like', "%{$value}%")
        );

        $query->when($filters['tags'] ?? false, fn($query, $tags) => 
            $query->whereJsonContains('tags', $tags)
        );

        $query->when($filters['language'] ?? false, fn($query, $language) => 
            $query->whereHas('language', fn($query) => 
                $query->where('code', $language)
            )
        );

        $query->when($filters['group'] ?? false, fn($query, $group) => 
            $query->whereHas('group', fn($query) => 
                $query->where('name', $group)
            )
        );
    }
}