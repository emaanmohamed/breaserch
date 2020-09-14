<?php

namespace App\Http\Controllers\Admin;

use App\Models\Analyst;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class AnalystsController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfAnalystExist($name, $columnName)
    {
        return Analyst::where($columnName, $name)->count();
    }

    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];

        $analyst = $this->guzzleService->get('analysts', $params);
        if (optional($analyst)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/' ,optional($analyst)->pagination);
            $analyst = optional($analyst)->data->data;
        } else {
            $analyst = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.analyst.display', compact('analyst', 'pagination'));
        }

        return view('admin.analyst.index', compact('analyst', 'pagination'));
    }

    public function edit($id)
    {
        $analyst = Analyst::findOrFail($id);
        return view('admin.ajax_includes.analyst.edit_form', compact('analyst'));

    }

    public function add()
    {
        return view('admin.ajax_includes.analyst.add_form');
    }

    public function getCreate()
    {

    }
{
    public function store(Request $request)

        if (! empty($request->name)) {
            if ($this->checkIfAnalystExist($request->name, 'name') < 1) {
                Analyst::create([
                    'name' => $request->name
                ]);
                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This analyst {$request->name} has been added successfully!",
                    'type'    => "success"
                ]);
            } else {
                return json_encode([
                    'title'   => 'Exist!',
                    'message' => "This analyst {$request->name} already exist!",
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
       if ($this->checkIfAnalystExist($request->name, 'name') < 1) {
            Analyst::findOrFail($request->id)->update([
                'name' => $request->name
            ]);
            if ($request->ajax()) {
                return json_encode([
                    'title'   => "Updating!",
                    'message' => "Analyst has been updated successfully!",
                    'type'    => "success"
                ]);
            }
        } else {
            return json_encode([
                'title'   => 'Exist!',
                'message' => "This analyst {$request->name} already exist!",
                'type'    => "warning"
            ]);

        }
    }

    public function delete($id)
    {
        Analyst::findOrFail($id)->delete();
    }


}
