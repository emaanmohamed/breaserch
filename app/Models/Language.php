<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'lookup_languages';
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 0);
    }
}
