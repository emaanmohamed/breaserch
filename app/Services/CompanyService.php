<?php


namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Request;

class CompanyService
{
    public function getAllCompanies()
    {
        $companies = Company::all();
        return $companies;
    }

    public function getCompanies($request)
    {
        $params = $request->name;
        $company = Company::filter($params)->paginate(50);
        return $company;
    }

    public function insertCompany(Request $request)
    {
        $insert = Company::create([
            'name'        => $request->name,
            'sector_id'   => $request->sector_id,
            'company_id'  => $request->company_id,
            'description' => $request->description
        ]);
        return $insert;
    }

}
