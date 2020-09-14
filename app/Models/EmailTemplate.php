<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';
    protected $fillable = ['title', 'email_template'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('title', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
