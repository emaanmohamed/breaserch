<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSubType extends Model
{
    protected $table = 'lookup_report_sub_types';
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 0);
    }


}
