<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table = 'queue';
    protected $guarded = [];


    public function docs()
    {
        return $this->belongsTo('App\Models\ResearchDoc', 'doc_id');

    }

}
