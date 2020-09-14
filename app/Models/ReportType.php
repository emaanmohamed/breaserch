<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    protected $table = 'lookup_report_types';
    protected $fillable = ['name_en', 'short_name'];
    public $timestamps = false;

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 0);
    }

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('name_en', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }
}
