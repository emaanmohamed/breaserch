<?php

namespace App\Http\Controllers\Api;

use App\Services\ReportTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportTypesController extends ApiController
{
    private $reportTypeService;

    public function __construct(ReportTypeService $reportTypeService)
    {
        $this->reportTypeService = $reportTypeService;
    }

    public function getReports()
    {
        $reports = $this->reportTypeService->getAllReports();
        return $this->ApiResponseData($reports, 200);
    }

    public function index(Request $request)
    {
        $reports = $this->reportTypeService->getReports($request);
        return $this->ApiResponseData($reports, 200, (string)$reports->links());
    }

    public function store(Request $request)
    {
        $rules     = [
            'name_en'    => 'required',
            'short_name' => 'required'
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->message(), 400);
        } else {
            $insert = $this->reportTypeService->insertReportTypes($request);
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
