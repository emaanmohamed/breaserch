<?php

namespace App\Http\Controllers\Api;

use App\Services\ClientTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientTypeController extends ApiController
{
    private $clientTypeService;

    public function __construct(ClientTypeService $clientTypeService)
    {
        $this->clientTypeService = $clientTypeService;
    }

    public function getClientType()
    {
        $clientTypes = $this->clientTypeService->getClientTypes();
        return $this->ApiResponseData($clientTypes, 200);
    }


}
