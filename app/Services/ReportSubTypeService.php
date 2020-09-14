<?php


namespace App\Services;

use App\Models\ReportSubType;

class ReportSubTypeService
{
    public function getReportSubType()
    {
        $reportSubType = ReportSubType::active()->get();
        return $reportSubType;
    }

}
