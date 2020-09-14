<?php


namespace App\Services;


use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ClientService
{
    public function getClient($request)
    {
        $client = Client::with('clientType','institution', 'clientEmail')
            ->firstName($request->firstName)
            ->lastName($request->lastName)
            ->phone($request->phone)
            ->country($request->country)
            ->email($request->email)
            ->address($request->address)
            ->clientType($request->clientType)
            ->institution($request->institution)
            ->orderBy('id','desc')
            ->paginate(50);
        return $client ?? null;
    }

    public function getGroupClients($group_id,$request){
        $query = DB::table('client_group_rel as rel')
            ->join('clients','clients.id','rel.client_id')
            ->leftJoin('client_email','clients.id','client_email.client_id')
            ->leftJoin('client_types','clients.client_type','client_types.id')
            ->leftJoin('institutions','clients.institution_id','institutions.id')
            ->where('rel.group_id',$group_id);
        if($request->firstName){
            $query->where('firstName',$request->firstName);
        }
        if($request->lastName){
            $query->where('lastName',$request->lastName);
        }
        if($request->phone){
            $query->where('phone',$request->phone);
        }
        $items = $query->select('clients.id','client_email.email_address','clients.first_name', 'clients.last_name', 'clients.phone_number', 'clients.mobile_number', 'clients.client_type','clients.title', 'clients.country', 'clients.address', 'clients.notes', 'clients.institution_id', 'clients.client_type', 'client_types.customer_type_en as client_type_name', 'institutions.name as institution_name')
            ->paginate(50);
        return $items ?? null;
    }

    public function insertClient(Request $request)
    {
        $insert = Client::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'client_type' => $request->client_type,
            'address' => $request->address,
            'notes' => $request->notes,
            'is_active' => $request->is_active,
            'institution_id' => $request->institution_id,
            'migrated_contact_id' => $request->migrated_contact_id
        ]);
        return $insert;
    }

}
