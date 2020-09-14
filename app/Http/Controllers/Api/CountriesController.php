<?php

namespace App\Http\Controllers\Api;

use App\Services\CountryService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends ApiController
{
    private $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function getCompanies()
    {
        $countries = $this->countryService->getAllCountries();
        return $this->ApiResponseData($countries, 200);
    }
    public function getCountries()
    {
        $countries = $this->countryService->getAllCountries();
        return $this->ApiResponseData($countries, 200);
    }

    public function index(Request $request)
    {
        $countries = $this->countryService->getCountries($request);
        return $this->ApiResponseData($countries, 200, (string)$countries->links());

    }

    public function store(Request $request)
    {
        $rules = [
            'name_en'   => $request->name_en,
            'name_ar'   => $request->name_ar,
            'name_code' => $request->name_code
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->countryService->insertCountry($request);
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
