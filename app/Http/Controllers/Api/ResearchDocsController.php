<?php

namespace App\Http\Controllers\Api;

use App\Services\ResearchDocServise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResearchDocsController extends ApiController
{

    private $researchDocServise;

    public function __construct(ResearchDocServise $researchDocServise)
    {
        $this->researchDocServise = $researchDocServise;
    }

    public function index()
    {

    }

    public function store(Request $request)
    {
        $rules = [
            'country_id' => $request->country_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'report_type_id' => $request->report_type_id,
            'target_segment_id' => $request->target_segment_id,
            'doc_date' => $request->doc_date,
            'analyst_id' => $request->analyst_id,
            'from_template' => $request->from_template,
            'language_id' => $request->language_id,
            'html_content' => $request->html_content,
            'status_id' => $request->status_id,
            'report_sub_type' => $request->report_sub_type,
            'company_id' => $request->company_id,
            'sector_id' => $request->sector_id,
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->researchDocServise->insertResearchDoc($request);
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
