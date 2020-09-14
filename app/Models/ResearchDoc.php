<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchDoc extends Model
{
    protected $table = 'research_docs';
    protected $guarded = [];

    public function language()
    {
        return $this->belongsTo('App\Models\Language', 'language_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector', 'sector_id');
    }

    public function analyst()
    {
        return $this->belongsTo('App\Models\Analyst', 'analyst_id');
    }


    public function attachments()
    {
        return $this->hasManyThrough(
            'App\Models\Attachment',
            'App\Models\ResearchAttachmentRel',
            'research_doc_id', // Foreign key on ResearchAttachmentRel table...
            'id', // Foreign key on docs table...
            'id', // Local key on Attachment table...
            'attachment_id' // Local key on ResearchAttachmentRel table...
        );

    }
    public function scopeSubject($query, $value)
    {
        if (!empty($value)) {
            return $query->where('subject', 'like', '%' . $value . '%');
        }
        return $query;
    }

    public function scopeDocID($query, $value)
    {
        if (!empty($value)) {
            return $query->where('id', $value);
        }
        return $query;
    }

    public function scopeDate($query, Array $value)
    {
        if (! is_null($value) && ! empty($value) && ! empty($value[0]) && ! empty($value[1])) {
            return $query->whereDate('created_at', '>=', $value[0])
                ->whereDate('created_at', '<=', $value[1]);
        }
        return $query;
    }

    public function scopeCompany($query, $value)
    {
        if (!empty($value)) {
            return $query->where('company_id', $value);
        }
        return $query;
    }

    public function scopeAnalyst($query, $value)
    {
        if (!empty($value)) {
            return $query->where('analyst_id', $value);
        }
        return $query;
    }

    public function scopeCountry($query, $value)
    {
        if (!empty($value)) {
            return $query->where('country_id', $value);
        }
        return $query;
    }

    public function scopeLanguage($query, $value)
    {
        if (!empty($value)) {
            return $query->where('language_id', $value);
        }
        return $query;
    }

}
