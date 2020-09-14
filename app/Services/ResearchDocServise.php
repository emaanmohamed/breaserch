<?php


namespace App\Services;

use App\Models\ResearchDoc;
use Illuminate\Support\Facades\Request;

class ResearchDocServise
{

    public function getResearchDocs()
    {
    }

    public function insertResearchDoc(Request $request)
    {
        $insert = ResearchDoc::create([
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
        ]);
        return $insert;
    }

}
