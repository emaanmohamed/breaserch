<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchAttachmentRel extends Model
{
    protected $table = 'research_attachments_rel';
    protected $guarded = [];
    public $timestamps = false;

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id');
    }


}
