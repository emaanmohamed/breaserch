<?php


namespace App\Services;

use App\Models\Analyst;
use Illuminate\Http\Request;

class AnalystService
{
    public function getAllAnalyst($request)
    {
        $params = $request->analystName;
        $analyst = Analyst::filter($params)->paginate(50);
        return $analyst;
    }

    public function getAnalyst()
    {
        $analyst = Analyst::all();
        return $analyst;
    }

    public function insertAnalyst(Request $request)
    {
        $insert = Analyst::create([
            'name' => $request->name
        ]);
        return $insert;
    }
}
