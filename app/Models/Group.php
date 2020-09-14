<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    protected $fillable = ['name', 'description'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('name', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }

    public function scopeGetGroupsIn($query, $IDs)
    {

        $IDs = (is_array($IDs))? $IDs : explode(',',$IDs);

        return $query->whereIn('id', $IDs);
    }
}
