<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'is_active'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}