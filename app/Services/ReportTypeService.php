<?php


namespace App\Services;


use App\Models\ReportType;
use Illuminate\Support\Facades\Request;

class ReportTypeService
{
    public function getAllReports()
    {
        $reports = ReportType::all();
        return $reports;
    }

    public function getReports($request)
    {
        $params = $request->name;
        $report = ReportType::filter($params)->paginate(30);
        return $report;
    }

    public function insertReportTypes(Request $request)
    {
        $insert = ReportType::create([
            'name_en' => $request->name_en,
            'short_name' => $request->short_name
        ]);
        return $insert;
    }

}
