<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Services\GuzzleService;
use App\Http\Controllers\Controller;

class CompaniesController extends Controller
{ private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfCompanyExist($name, $columnName)
    {
        return Company::where($columnName, $name)->count();
    }

    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $companies = $this->guzzleService->get('companies', $params);
        if (optional($companies)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',optional($companies)->pagination);
            $companies = optional($companies)->data->data;
        } else {
            $companies = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.company.display', compact('companies', 'pagination'));
        }

        return view('admin.company.index', compact('companies', 'pagination'));
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('admin.ajax_includes.company.edit_form', compact('company'));

    }

    public function add()
    {
        return view('admin.ajax_includes.company.add_form');
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
        if (! empty($request->name)) {
            if ($this->checkIfCompanyExist($request->name, 'name') < 1) {
                Company::create([
                    'name'        => $request->name,
                    'description' => $request->description
                ]);
                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This company {$request->name} has been added successfully!",
                    'type'    => "success"
                ]);
            } else {
                return json_encode([
                    'title'   => 'Exist!',
                    'message' => "This company {$request->name} already exist!",
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
        Company::findOrFail($request->id)->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        if ($request->ajax()) {
            return json_encode([
                'title'    => "Updating!",
                'message' => "Company has been updated successfully!",
                'type'    => "success"
            ]);

        }
    }

    public function delete($id)
    {
        Company::findOrFail($id)->delete();
    }



}
