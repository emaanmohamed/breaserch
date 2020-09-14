<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'lookup_companies';
    protected $fillable = ['name', 'description'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('name', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
