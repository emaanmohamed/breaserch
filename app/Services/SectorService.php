<?php


namespace App\Services;


use App\Models\Sector;
use Illuminate\Support\Facades\Request;

class SectorService
{
    public function getAllSectors()
    {
        $sectors = Sector::all();
        return $sectors;
    }

    public function getSectors($request)
    {
        $params = $request->name_en;
        $sector = Sector::filter($params)->paginate(50);
        return $sector;
    }

    public function insertSector(Request $request)
    {
        $insert = Sector::create([
            'name_en' => $request->name_en
        ]);
        return $insert;
    }

}
