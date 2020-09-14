<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'lookup_countries';
    protected $fillable = ['name_en', 'name_ar', 'name_code'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('name_en', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
