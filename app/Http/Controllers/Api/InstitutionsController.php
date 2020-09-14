<?php

namespace App\Http\Controllers\Api;

use App\Services\InstitutionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstitutionsController extends ApiController
{
    private $institutionService;

    public function __construct(InstitutionService $institutionService)
    {
        $this->institutionService = $institutionService;
    }

    public function index(Request $request)
    {
        $institutions = $this->institutionService->getInstitutions($request);
        return $this->ApiResponseData($institutions, 200, (string)$institutions->links());
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'notes' => $request->notes,
            'address' => $request->address
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->institutionService->insertInstitution($request);
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
}
