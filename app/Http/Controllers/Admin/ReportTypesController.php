<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportType;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportTypesController extends Controller
{

    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfReportExist($name, $columnName)
    {
        return ReportType::where($columnName, $name)->count();
    }

    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $reports = $this->guzzleService->get('reports', $params);
        if (optional($reports)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',optional($reports)->pagination);
            $reports = optional($reports)->data->data;
        } else {
            $reports = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.report.display', compact('reports', 'pagination'));
        }

        return view('admin.report.index', compact('reports', 'pagination'));
    }

    public function edit($id)
    {
        $report = ReportType::findOrFail($id);
        return view('admin.ajax_includes.report.edit_form', compact('report'));

    }

    public function add()
    {
        return view('admin.ajax_includes.report.add_form');
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
        if (! empty($request->name_en)) {
            if ($this->checkIfReportExist($request->name_en, 'name_en') < 1) {
                ReportType::create([
                    'name_en'    => $request->name_en,
                    'short_name' => $request->short_name
                ]);
                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This report type {$request->name_en} has been added successfully!",
                    'type'    => "success"
                ]);
            } else {
                return json_encode([
                    'title'   => 'Exist!',
                    'message' => "This report type {$request->name_en} already exist!",
                    'type'    => "warning"
                ]);
            }
        } else {
            return json_encode([
                'title'   => 'Error',
                'message' => "Please enter valid name!",
                'type'    => "warning"
            ]);
        }
    }


    public function update(Request $request)
    {
        ReportType::findOrFail($request->id)->update([
            'name_en' => $request->name_en,
            'short_name' => $request->short_name
        ]);
        if ($request->ajax()) {
            return json_encode([
                'title'    => "Updating!",
                'message' => "Report has been updated successfully!",
                'type'    => "success"
            ]);

        }
    }

    public function delete($id)
    {
        ReportType::findOrFail($id)->delete();
    }

}
