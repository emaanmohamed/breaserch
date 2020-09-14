<?php

namespace App\Http\Controllers\Api;

use App\Services\AttachmentService;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DocumentsController extends ApiController
{
    private $documentService;
    private $attachmentService;

    public function __construct(DocumentService $documentService, AttachmentService $attachmentService)
    {
        $this->documentService   = $documentService;
        $this->attachmentService = $attachmentService;
    }

    public function getDocuments(Request $request)
    {
        $documents = $this->documentService->getAllDocuments($request);
        return $this->ApiResponseData($documents, 200, (string)$documents->links());
    }

    public function store(Request $request)
    {
        $rules     = [
            'email_subject'     => 'required',
            'description'       => 'required',
            'report_type_id'    => 'required',
            'doc_date'          => 'required',
            'analyst_id'        => 'required',
            'email_template_id' => 'required',
            'language_id'       => 'required',
            'html_content'      => 'required',
//            'report_sub_type'   => 'required',
        ];
        $validator =  Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages()->first(), 400);
        } else {
            $insert = $this->documentService->createNewDocument($request);
            if ($insert == true) {
                return $this->ApiResponseData($insert, 201);
            } else {
                return $this->ApiResponseMessage('Record not inserted yet', 400);
            }
        }
    }

    public function update(Request $request, $documentId)
    {
       $document = $this->documentService->updateDoc($request, $documentId);
       return $this->ApiResponseMessage('Document updated successfully!', 200);

    }

    public function getDocumentByID($documentId)
    {
        $document = $this->documentService->getDocumentByID($documentId);
        $document = $this->documentService->ParsDocumentVariables($document);
        $document->html_content = $this->documentService->generateAttachmentLinksForHTML($document->id, $document->html_content);
       return $this->ApiResponseData($document, 200);
    }

    public function getDocumentByIDWithoutAttachment($documentId)
    {
       $document = $this->documentService->getDocumentByID($documentId);
       return $this->ApiResponseData($document, 200);
    }


}
