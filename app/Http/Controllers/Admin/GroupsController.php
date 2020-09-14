<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupsController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfGroupExist($name, $columnName)
    {
        return Group::where($columnName, $name)->count();
    }

    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $groups = $this->guzzleService->get('groups', $params);
        if (optional($groups)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',optional($groups)->pagination);
            $groups = optional($groups)->data->data;
        } else {
            $groups = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.group.display', compact('groups', 'pagination'));
        }

        return view('admin.group.index', compact('groups', 'pagination'));
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);
        return view('admin.ajax_includes.group.edit_form', compact('group'));

    }

    public function add()
    {
        return view('admin.ajax_includes.group.add_form');
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
        if (! empty($request->name)) {
            if ($this->checkIfGroupExist($request->name, 'name') < 1) {
                Group::create([
                    'name' => $request->name,
                    'description' => $request->description
                ]);
                return json_encode([
                    'title'   => "Adding!",
                    'message' => "This group {$request->name} has been added successfully!",
                    'type'    => "success"
                ]);
            }
        } else {
            return json_encode([
                'title'   => 'Exist!',
                'message' => "This group {$request->name} already exist!",
                'type'    => "warning"
            ]);
        }
    }


    public function update(Request $request)
    {
        Group::findOrFail($request->id)->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        if ($request->ajax()) {
            return json_encode([
                'title'    => "Updating!",
                'message' => "Group has been updated successfully!",
                'type'    => "success"
            ]);

        }
    }

    public function delete($id)
    {
        Group::findOrFail($id)->delete();
    }


    public function detail($id,Request $request)
    {
        $group = Group::findOrFail($id);
        $params = [
            'query' => $request->all()
        ];
        $clients = $this->guzzleService->get('groups/getGroupClients/'.$id, $params);

        if (optional($clients)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',optional($clients)->pagination);
            $clients = optional($clients)->data->data;
        } else {
            $clients = [];
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.group.display_group_clients', compact('clients', 'pagination'));
        }
            return view('admin.group.detail', compact('group','clients', 'pagination'));
    }


}
