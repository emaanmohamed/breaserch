<?php

namespace App\Http\Controllers\Api;

use App\Services\ReportSubTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportSubTypeController extends ApiController
{
    private $reportSubTypeService;

    public function __construct(ReportSubTypeService $reportSubTypeService)
    {
        $this->reportSubTypeService = $reportSubTypeService;
    }

    public function getReportType()
    {
        $reportTypes = $this->reportSubTypeService->getReportSubType();
        return $this->ApiResponseData($reportTypes, 200);
    }
}
