<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\Services\SugarcrmService;
use App\Console\Commands\SugarDataMigration;
use App\Services\AnalystService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AnalystsController extends ApiController
{
    private $analystService;

    public function __construct(AnalystService $analystService)
    {
        $this->analystService = $analystService;
    }

    public function getAnalysts()
    {
        $analysts = $this->analystService->getAnalyst();
        return $this->ApiResponseData($analysts, 200);
    }

    public function index(Request $request)
    {
        $AllAnalysts = $this->analystService->getAllAnalyst($request);

        return $this->ApiResponseData($AllAnalysts, 200, (string)$AllAnalysts->links());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->analystService->insertAnalyst($request);

            if ($insert == true) {
                return $this->ApiResponseMessage('Record inserted successfully', 201);
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
