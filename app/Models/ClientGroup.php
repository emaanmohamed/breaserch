<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientGroup extends Model
{
    protected $table = 'client_group_rel';
    protected $guarded = [];
    public $timestamps = false;

    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id');
    }


}
