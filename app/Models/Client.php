<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    protected $fillable = ['first_name', 'last_name', 'phone_number', 'mobile_number', 'client_type','title', 'country', 'address', 'notes', 'institution_id'];
    public $timestamps = false;

    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('first_name', 'LIKE', '%' . trim($params) . '%');
        }
        return $query;
    }

    public function institution()
    {
        return $this->belongsTo('App\Models\Institution');
    }

    public function clientType()
    {
        return $this->belongsTo('App\Models\ClientType', 'client_type');
    }

    public function clientEmail()
    {
        return $this->hasMany('App\Models\ClientEmail', 'client_id');
    }

    public function scopeFirstName($query, $value)
    {
        if (!empty($value)) {
            return $query->where('first_name', 'like', '%' . $value . '%');
        }
        return $query;
    }

    public function scopeLastName($query, $value)
    {
        if (!empty($value)) {
            return $query->where('last_name', 'like', '%' . $value . '%');
        }
        return $query;
    }

    public function scopePhone($query, $value)
    {
        if (!empty($value)) {
            return $query->where('phone_number', $value);
        }
        return $query;
    }

    public function scopeCountry($query, $value)
    {
        if (!empty($value)) {
          //  dump($query->where('country', 'like', '%' . $value . '%'));
            return $query->where('country', 'like', '%' . $value . '%');
        }
        return $query;
    }

    public function scopeEmail($query, $value) {
        if (!empty($value)) {
           $clientIDS =  ClientEmail::select('client_id')->where('email_address', 'like', '%' . $value . '%')->get();
            return  $query->whereIn('id', $clientIDS->pluck('client_id')->toArray());
        }
        return $query;
    }

    public function scopeAddress($query, $value)
    {
        if (!empty($value)) {
            return $query->where('address', 'like', '%' . $value . '%');
        }
        return $query;
    }

    public function scopeClientType($query, $value)
    {
        if (!empty($value)) {
            return $query->where('client_type', $value);
        }
        return $query;
    }
    public function scopeInstitution($query, $value)
    {
        if (!empty($value)) {
            return $query->where('institution_id', $value);
        }
        return $query;
    }



}














