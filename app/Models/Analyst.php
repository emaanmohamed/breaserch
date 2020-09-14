<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyst extends Model
{
    protected $table = 'analyst';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('name', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
