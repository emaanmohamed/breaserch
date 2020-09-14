<?php


namespace App\Services;


use App\Models\Institution;
use Illuminate\Support\Facades\Request;

class InstitutionService
{
    public function getInstitutions($request)
    {
        $params = $request->name;
        $institution = Institution::filter($params)->orderBy('id','desc')->paginate(30);
        return $institution;

    }

    public function insertInstitution(Request $request)
    {
        $insert = Institution::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'notes' => $request->notes,
            'address' => $request->address
        ]);
        return $insert;
    }

}
