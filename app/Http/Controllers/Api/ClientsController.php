<?php

namespace App\Http\Controllers\Api;

use App\Services\ClientService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientsController extends ApiController
{
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        $clients = $this->clientService->getClient($request);
        if ($clients != null) {
            return $this->ApiResponseData($clients, 200, (string) $clients->links());
        } else {
            return $this->ApiResponseMessage('There is no records', 404);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'client_type' => $request->client_type,
            'address' => $request->address,
            'notes' => $request->notes,
            'is_active' => $request->is_active,
            'institution_id' => $request->institution_id,
            'migrated_contact_id' => $request->migrated_contact_id
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->clientService->insertClient($request);
            if ($insert == true) {
                return $this->ApiResponseSuccessMessage('Record inserted successfully', 201);
            } else {
                return $this->ApiResponseMessage('Record not inserted yet', 400);
            }
        }

    }

    public function update($id, Request $request)
    {

    }

    public function delete($id)
    {

    }


    public function getGroupClients($id,Request $request)
    {
        $clients = $this->clientService->getGroupClients($id,$request);
        if ($clients != null) {
            return $this->ApiResponseData($clients, 200, (string) $clients->links());
        } else {
            return $this->ApiResponseMessage('No records', 404);
        }
    }

}
