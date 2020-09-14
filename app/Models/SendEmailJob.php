<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendEmailJob extends Model
{
    protected $table = 'sent_email_job';
    protected $guarded = [];
    public $timestamps = false;

    public function queue()
    {
        return $this->belongsTo('App/Models/Queue', 'queue_id');
    }

}
