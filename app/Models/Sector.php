<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectors';
    protected $fillable = ['name_en'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('name_en', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
