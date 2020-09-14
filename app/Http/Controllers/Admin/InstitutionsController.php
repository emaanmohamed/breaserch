<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\ClientEmail;
use App\Models\Institution;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstitutionsController extends Controller
{

    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfInstitutionPhoneExist($columnPhone, $phone)
    {
        return Institution::where($columnPhone, $phone)->count();
    }

    public function index(Request $request)
    {
        $params       = [
            'query' => $request->all()
        ];
        $institutions = $this->guzzleService->get('institutions', $params);
        if (optional($institutions)->code == 200) {
            $pagination   = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',
                optional($institutions)->pagination);
            $institutions = optional($institutions)->data->data;
        } else {
            $institutions = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.institution.display', compact('institutions', 'pagination'));
        }

        return view('admin.institution.index', compact('institutions', 'pagination'));
    }



    public function add()
    {
        return view('admin.ajax_includes.institution.add_form');
    }
    public function store(Request $request)
    {
        if (!empty($request->name)) {
            if ($this->checkIfInstitutionPhoneExist('phone_number', $request->phone_number) < 1) {
                $institution = Institution::create([
                    'name'         => $request->name,
                    'phone_number' => $request->phone_number,
                    'notes'        => $request->notes,
                    'address'      => $request->address
                ]);
                $client      = Client::create([
                    'first_name' => $request->name,
                    'mobile_number'  => $request->phone_number,
                    'client_type'    => 1,
                    'institution_id' => $institution->id
                ]);

                ClientEmail::create([
                    'client_id'     => $client->id,
                    'email_address' => $request->email
                ]);
                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This institution {$request->name} has been added successfully!",
                    'type'    => "success"
                ]);
            } else {
                return json_encode([
                    'title'   => 'Exist!',
                    'message' => "institution {$request->name} already exist!",
                    'type'    => "warning"
                ]);
            }
        } else {
            return json_encode([
                'title'   => 'Error',
                'message' => "Please enter valid name!",
                'type'    => "warning"
            ]);
        }
    }

    public function edit($id)
    {
        $institution = Institution::findOrFail($id);
        $client      = Client::where('institution_id', $id)->first();
        $clientEmail = ClientEmail::select('email_address')->where('client_id', $client->id)->get();
        return view('admin.ajax_includes.institution.edit_form', compact('institution', 'clientEmail'));

    }
    public function update(Request $request)
    {
        $institution = Institution::findOrFail($request->id)->update([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
            'notes'        => $request->notes,
            'address'      => $request->address
        ]);
        $client = Client::where('institution_id', $request->id)->first();
        if (!empty($client)) {
            $client->update([
                'mobile_number' => $request->phone_number,
                'client_type'   => 1,
            ]);
            ClientEmail::where('client_id', $client->id)->delete();
            ClientEmail::create([
                'client_id'     => $client->id,
                'email_address' => $request->email
            ]);
        } else {
            return json_encode([
                'title'   => 'Error',
                'message' => "Can't update institution email!",
                'type'    => "warning"
            ]);
        }

        if ($request->ajax()) {
            return json_encode([
                'title'   => "Updating!",
                'message' => "Institution has been updated successfully!",
                'type'    => "success"
            ]);
        }
    }

    public function delete($id)
    {
//        $client      = Client::where('institution_id', $id)->first();
//        $clientEmail = ClientEmail::where('client_id', $client->id)->first();
//        $clients     = Client::where('institution_id', $id)->get();
//        if (isset($clients)) {
//            foreach ($clients as $client) {
//                $client->update([
//                    'institution_id' => null
//                ]);
//            }
//        }
//
//        $client->delete();
//        $clientEmail->delete();
//
//
//        Institution::findOrFail($id)->delete();
//        $clients = Client::where('institution_id', $id)->get();
//        if (isset($clients)) {
//            foreach ($clients as $client) {
//                ClientEmail::where('client_id', $client->id)->delete();
//                $client->delete();
//            }
//        }
//
//
//

    }

}
