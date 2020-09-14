<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id');
    }

    public function scopeFilename($query, $value)
    {
        if (! is_null($value) && ! empty($value)) {
            return $query->where('original_file_name', 'like', '%' . $value . '%');
        }
        return $query;
    }

    public function scopeAddedby($query, $value)
    {
        if (! is_null($value) && ! empty($value)) {
            return $query->where('created_by_user_id', $value);
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



}
