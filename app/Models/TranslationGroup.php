<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}