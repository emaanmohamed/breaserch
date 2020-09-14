<?php

namespace App\Services;

use App\Enum\Status;
use App\Models\ResearchDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function foo\func;

class DocumentService
{
    public function getAllDocuments($request)
    {
        $documents = ResearchDoc::with('attachments', 'language')->subject($request->subject)
            ->analyst($request->analyst)
            ->company($request->company)
            ->country($request->country)
            ->language($request->language)
            ->docID($request->docID)
            ->date([$request->dateFromAttachment, $request->dateToAttachment])
            ->orderBy('doc_date', 'desc')
            ->paginate(30);

        $documents->map(function ($item) {
            $item->Status = sendStatus($item->status);
        });


        return $documents;
    }

    public function createNewDocument($request)
    {
        $data        = [
            'country_id'        => $request->country_id,
            'subject'           => $request->email_subject,
            'description'       => $request->description,
            'status'            => Status::Pending,
            'report_type_id'    => $request->report_type_id,
            'doc_date'          => $request->doc_date,
            'analyst_id'        => $request->analyst_id,
            'email_template_id' => $request->email_template_id,
            'language_id'       => $request->language_id,
            'html_content'      => $request->html_content,
            'report_sub_type'   => $request->report_sub_type,
            'company_id'        => $request->company_id,
            'sector_id'         => $request->sector_id,
            'sector_region_id'  => $request->sector_region_id,
        ];
        $researchDoc = ResearchDoc::create($data);
        return $researchDoc;
    }

    public function getDocumentByID($documentId)
    {
        $document = ResearchDoc::findOrFail($documentId);
        return $document;
    }

    public function updateDoc($request, $documentId)
    {
        $data = [
            'country_id'        => $request->country_id,
            'subject'           => $request->email_subject,
            'status'            => Status::Pending,
            'description'       => $request->description,
            'report_type_id'    => $request->report_type_id,
            'doc_date'          => $request->doc_date,
            'analyst_id'        => $request->analyst_id,
            'email_template_id' => $request->email_template_id,
            'language_id'       => $request->language_id,
            'html_content'      => $request->html_content,
            'report_sub_type'   => $request->report_sub_type,
            'company_id'        => $request->company_id,
            'sector_id'         => $request->sector_id,
            'sector_region_id'  => $request->sector_region_id,
        ];

        $researchDoc      = ResearchDoc::findOrFail($documentId);
        $researchDocument = $researchDoc->update($data);
        return $researchDocument;
    }

    public function generateAttachmentLinksForHTML($docID, $content_html)
    {
        $attachments = DB::table('attachments')
            ->select('attachments.server_file_name', 'attachments.original_file_name', 'attachments.id')
            ->leftJoin('research_attachments_rel', 'attachments.id', '=', 'research_attachments_rel.attachment_id')
            ->where('research_attachments_rel.research_doc_id', $docID)->get();
        $link        = "";
        foreach ($attachments as $attachment) {
            $link .= '<a href="'.route('attachment-download', [
                    $attachment->id, $attachment->original_file_name
                ]).'">'.$attachment->original_file_name.'</a><br />';
        }
        if (strpos($content_html, '__REPORTLINK__') !== false) {
            $newHTML = str_replace('__REPORTLINK__', $link, $content_html);
        }  else {
            $newHTML = $content_html.'<br />'.$link;
        }
        return $newHTML;
    }

    public function ParsDocumentVariables($doc)
    {
        if (strpos($doc->html_content, '__COMPANY__') !== false) {
            $doc->html_content = str_replace('__COMPANY__', optional($doc->company)->name, $doc->html_content);
        }

        if (strpos($doc->html_content, '__REPORTTITLE__') !== false) {
            $doc->html_content = str_replace('__REPORTTITLE__', $doc->subject, $doc->html_content);
        }

        if (strpos($doc->html_content, '__ANALYSTNAME__') !== false) {
            $doc->html_content = str_replace('__ANALYSTNAME__', optional($doc->analyst)->name, $doc->html_content);
        }


        if (strpos($doc->html_content, '__COUNTRY__') !== false) {
            $doc->html_content = str_replace('__COUNTRY__', optional($doc->country)->name_en, $doc->html_content);
        }

        if (strpos($doc->html_content, '__SECTOR__') !== false) {
            $doc->html_content = str_replace('__SECTOR__', optional($doc->sector)->name_en, $doc->html_content);
        }

        if (strpos($doc->html_content, '__DATE__') !== false) {
            $doc->html_content = str_replace('__DATE__', $doc->doc_date, $doc->html_content);
        }
        return $doc;
    }


}
