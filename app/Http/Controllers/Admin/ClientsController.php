<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\ClientGroup;
use App\Models\ClientType;
use App\Models\Group;
use App\Models\Institution;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfClientPhoneExist($phone, $columnPhone)
    {
        return Client::where($columnPhone, $phone)->count();
    }

    private function checkIfGroupIsExist($clientId, $groupId)
    {
        return ClientGroup::select('client_id')->where('client_id', $clientId)->where('group_id', $groupId)->count();
    }

    public function index(Request $request)
    {
        $params       = [
            'query' => $request->all()
        ];
        $clients      = $this->guzzleService->get('clients', $params);
        $clientTypes  = $this->guzzleService->get('clientTypes/');
        $institutions = $this->guzzleService->get('institutions/');

        $clientTypes  = ($clientTypes->code == 200) ? optional($clientTypes)->data : [];
        $institutions = ($institutions->code == 200) ? optional($institutions)->data->data : [];

        if (optional($clients)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',
                optional($clients)->pagination);
            $clients    = optional($clients)->data->data;
        } else {
            $clients = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.client.display',
                compact('clients', 'pagination', 'clientTypes', 'institutions'));
        }

        return view('admin.client.index', compact('clients', 'pagination', 'clientTypes', 'institutions'));
    }

    public function getClientGroup(Request $request)
    {
        if ($request->ajax()) {
            $clientGroups = ClientGroup::where('client_id', $request->clientId)->get();
            return view('admin.ajax_includes.client.group_client_ajax', compact('clientGroups'));
        }
    }

    public function addClientGroup(Request $request)
    {
        if ($this->checkIfGroupIsExist($request->clientId, $request->group) < 1) {
            ClientGroup::create([
                'client_id' => $request->clientId,
                'group_id'  => $request->group
            ]);
            return json_encode([
                'type'    => 'success',
                'title'   => 'Added Successfully',
                'message' => "The group you selected has been added successfully."
            ]);
        }

        return json_encode([
            'type'    => 'info',
            'title'   => 'Already Exist',
            'message' => "The group you are trying to add it is already exist!"
        ]);


    }

    public function edit($id)
    {
        $client        = Client::findOrFail($id);
        $institutions  = Institution::select('id', 'name')->get();
        $client->email = implode(',',
            DB::table('client_email')->where('client_id', $id)->pluck('email_address')->toArray());
        $clientGroups  = ClientGroup::where('client_id', $id)->get();
        $groups        = Group::select('id', 'name')->get();
        $clientTypes   = ClientType::select('id', 'customer_type_en')->get();

        return view('admin.ajax_includes.client.edit_form',
            compact('client', 'groups', 'clientGroups', 'institutions', 'clientTypes'));
    }

    public function addClientgroups($id)
    {

        $client   = Client::findOrFail($id);
        $groupIds = ClientGroup::select('group_id')->where('client_id', $id)->pluck('group_id')->toArray();
        $groups   = Group::select('id', 'name')->get();
        $groups->map(function ($item) use ($groupIds){
            $item->checkd = (in_array($item->id,$groupIds)) ? true : false;
        });

        return view('admin.ajax_includes.client.add_form_client_group', compact('client', 'groups', 'groupIds'));
    }

    public function updateClientGroup(Request $request)
    {
      //  dd($request);

        DB::table('client_group_rel')->where('client_id', $request->client_id)->delete();
        if(!empty($request->group_id)){
            $groupIds = explode(',', $request->group_id);
            foreach ($groupIds as $groupId) {
                DB::table('client_group_rel')
                    ->insert([
                        'client_id' => $request->client_id,
                        'group_id'  => $groupId
                    ]);

            }
        }


        if ($request->ajax()) {
            return json_encode([
                'title'   => "Updating!",
                'message' => "Client has been added to selected groups successfully!",
                'type'    => "success"
            ]);

        } else {
            return json_encode([
                'title'   => "ERROR!",
                'message' => "Client hasn't been added to selected group :( !",
                'type'    => "warning"
            ]);

        }

    }


    public function add()
    {
        $institutions = Institution::select('id', 'name')->get();
        $clientTypes  = ClientType::select('id', 'customer_type_en')->get();

        return view('admin.ajax_includes.client.add_form', compact('institutions', 'clientTypes'));
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
//            if ($this->checkIfClientPhoneExist($request->phone_number, 'phone_number') < 1) {
                $client = Client::create([
                    'first_name'     => $request->first_name,
                    'last_name'      => $request->last_name,
                    'phone_number'   => $request->phone_number,
                    'mobile_number'  => $request->mobile_number,
                    'title'          => $request->title,
                    'country'        => $request->country,
                    'address'        => $request->address,
                    'client_type'    => $request->client_type,
                    'notes'          => $request->notes,
                    'institution_id' => $request->institution_id
                ]);

                $emails = explode(',', $request->email);
                foreach ($emails as $email) {
                    DB::table('client_email')
                        ->insert([
                            'client_id'     => $client->id,
                            'email_address' => $email
                        ]);
                }

                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This client {$request->first_name} has been added successfully!",
                    'type'    => "success"
                ]);
//            } else {
//                return json_encode([
//                    'title'   => 'Exist!',
//                    'message' => "This client {$request->first_name} already exist!",
//                    'type'    => "warning"
//                ]);
//            }
    }


    public function update(Request $request)
    {
        Client::findOrFail($request->id)->update([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'phone_number'   => $request->phone_number,
            'mobile_number'  => $request->mobile_number,
            'title'          => $request->title,
            'country'        => $request->country,
            'client_type'    => $request->client_type,
            'address'        => $request->address,
            'notes'          => $request->notes,
            'institution_id' => ($request->client_type == 1) ? $request->institution_id : null
        ]);

        DB::table('client_email')->where('client_id', $request->id)->delete();


        $emails = explode(',', $request->email);
        foreach ($emails as $email) {
            DB::table('client_email')
                ->insert([
                    'client_id'     => $request->id,
                    'email_address' => $email
                ]);
        }
        if ($request->ajax()) {
            return json_encode([
                'title'   => "Updating!",
                'message' => "Client has been updated successfully!",
                'type'    => "success"
            ]);

        }
    }

    public function removeClientGroup($groupId, $clientId)
    {
        ClientGroup::where('group_id', $groupId)->where('client_id', $clientId)->delete();

    }

    public function delete($id)
    {
        Client::findOrFail($id)->delete();
        return json_encode("YOUR RECORD HAS BEEN DELETED", 204);

    }

}
