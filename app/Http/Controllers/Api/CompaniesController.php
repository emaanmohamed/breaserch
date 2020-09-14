<?php

namespace App\Http\Controllers\Api;

use App\Services\CompanyService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompaniesController extends ApiController
{
    private $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function getCompanies()
    {
        $companies = $this->companyService->getAllCompanies();
        return $this->ApiResponseData($companies, 200);
    }

    public function index(Request $request)
    {
        $companies = $this->companyService->getCompanies($request);
        return $this->ApiResponseData($companies, 200, (string)$companies->links());
    }

    public function store(Request $request)
    {
        $rules     = [
            'name'      => 'required',
            'sector_id' => 'min:1'
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->CompanyService->insertCompany($request);
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
