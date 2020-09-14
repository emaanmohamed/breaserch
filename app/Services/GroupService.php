<?php


namespace App\Services;

use App\Models\Group;
use Illuminate\Support\Facades\Request;

class GroupService
{
    public function getAllGroups()
    {
        $groups = Group::all();
        return $groups;
    }

    public function getGroups($request)
    {
        $params = $request->name;
        $group = Group::filter($params)->orderBy('id', 'DESC')->paginate(40);
        return $group;
    }

    public function insertGroup(Request $request)
    {
        $insert = Group::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return $insert;
    }


}
