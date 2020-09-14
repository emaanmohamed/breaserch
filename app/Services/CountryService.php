<?php


namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Request;

class CountryService
{

    public function getAllCountries()
    {
        $countries = Country::all();
        return $countries;
    }

    public function getCountries($request)
    {
        $params = $request->name_en;
        $countries = Country::filter($params)->paginate(30);
        return $countries;

    }

    public function insertCountry(Request $request)
    {
        $insert = Country::create([
            'name_en'   => $request->name_en,
            'name_ar'   => $request->name_ar,
            'name_code' => $request->name_code
        ]);
        return $insert;
    }

}
