<?php

namespace App\Http\Controllers\Api;

use App\Services\SectorService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SectorsController extends ApiController
{
    private $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function getSectors()
    {
        $sectors = $this->sectorService->getAllSectors();
        return $this->ApiResponseData($sectors, 200);
    }

    public function index(Request $request)
    {
        $sectors = $this->sectorService->getSectors($request);
        return $this->ApiResponseData($sectors, 200, (string)$sectors->links());

    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->message(), 400);
        } else {
            $insert = $this->sectorService->insertSector($request);
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
