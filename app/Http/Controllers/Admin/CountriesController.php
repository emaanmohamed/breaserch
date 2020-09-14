<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\Country;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
         $this->guzzleService = $guzzleService;
    }

    private function checkIfCountryExist($name, $columnName)
    {
        return Country::where($columnName, $name)->count();
    }


    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $countries = $this->guzzleService->get('countries', $params);
        if (optional($countries)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/', optional($countries)->pagination);
            $countries = optional($countries)->data->data;
        } else {
            $countries = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.country.display', compact('countries', 'pagination'));
        }

        return view('admin.country.index', compact('countries', 'pagination'));
    }

    public function edit($id)
    {
        $country = Country::findOrFail($id);
        return view('admin.ajax_includes.country.edit_form', compact('country'));

    }

    public function add()
    {
        return view('admin.ajax_includes.country.add_form');
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
        if (! empty($request->name_en)) {
            if ($this->checkIfCountryExist($request->name_en, 'name_en') < 1) {
                Country::create([
                    'name_en'   => $request->name_en,
                    'name_code' => $request->name_code
                ]);

                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This country {$request->name_en} has been added successfully!",
                    'type'    => "success"
                ]);
            } else {
                return json_encode([
                    'title'   => 'Exist!',
                    'message' => "This country {$request->name_en} already exist!",
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
        if ($this->checkIfCountryExist($request->name_en, 'name_en') < 1) {
            Country::findOrFail($request->id)->update([
                'name_en'   => $request->name_en,
                'name_code' => $request->name_code
            ]);
            if ($request->ajax()) {
                return json_encode([
                    'title'   => "Updating!",
                    'message' => "Country has been updated successfully!",
                    'type'    => "success"
                ]);
            }
        } else {
                return json_encode([
                    'title'   => 'Exist!',
                    'message' => "This country {$request->name_en} already exist!",
                    'type'    => "warning"
                ]);
            }
    }

    public function delete($id)
    {
        Country::findOrFail($id)->delete();
    }
}
