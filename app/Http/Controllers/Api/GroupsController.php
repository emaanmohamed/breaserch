<?php

namespace App\Http\Controllers\Api;

use App\Services\GroupService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupsController extends ApiController
{
    private $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function index(Request $request)
    {
       $groups = $this->groupService->getGroups($request);
        return $this->ApiResponseData($groups, 200, (string)$groups->links());
    }

    public function getAllGroups()
    {
        $groups = $this->groupService->getAllGroups();
        return $this->ApiResponseData($groups, 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => $request->name,
            'description' => $request->description
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->groupService->insertGroup($request);
            if ($insert == true) {
                return $this->ApiResponseSuccessMessage('Record inserted successfully', 201);
            } else {
                return $this->ApiResponseMessage('Record not inserted yet', 400);
            }
        }

    }

    public function update($id, Request $request)
    {

    }

    public function delete($id)
    {

    }
}
