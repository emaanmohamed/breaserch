<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['tag_name'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('tag_name', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
