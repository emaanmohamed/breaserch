<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sector;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SectorsController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfSectorExist($name, $columnName)
    {
        return Sector::where($columnName, $name)->count();
    }

    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $sectors = $this->guzzleService->get('sectors', $params);
        if (optional($sectors)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',optional($sectors)->pagination);
            $sectors = optional($sectors)->data->data;
        } else {
            $sectors = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.sector.display', compact('sectors', 'pagination'));
        }

        return view('admin.sector.index', compact('sectors', 'pagination'));
    }

    public function edit($id)
    {
        $sector = Sector::findOrFail($id);
        return view('admin.ajax_includes.sector.edit_form', compact('sector'));

    }

    public function add()
    {
        return view('admin.ajax_includes.sector.add_form');
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
        if (! empty($request->name)) {
            if ($this->checkIfSectorExist($request->name, 'name_en') < 1) {
                Sector::create([
                    'name_en' => $request->name
                ]);
                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This sector {$request->name_en} has been added successfully!",
                    'type'    => "success"
                ]);
            }
        } else {
            return json_encode([
                'title'   => 'Exist!',
                'message' => "This sector {$request->name_en} already exist!",
                'type'    => "warning"
            ]);
        }
    }


    public function update(Request $request)
    {
        Sector::findOrFail($request->id)->update([
            'name_en' => $request->name_en
        ]);
        if ($request->ajax()) {
            return json_encode([
                'title'    => "Updating!",
                'message' => "Sector has been updated successfully!",
                'type'    => "success"
            ]);

        }
    }

    public function delete($id)
    {
        Sector::findOrFail($id)->delete();
    }

}
