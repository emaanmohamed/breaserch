<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $table = 'institutions';
    protected $fillable = ['name','email', 'phone_number', 'notes', 'address'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('name', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
