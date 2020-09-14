<?php

namespace App\Http\Controllers\Api;

use App\Models\Attachment;
use App\Services\AttachmentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttachmentsController extends ApiController
{
    private $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function index(Request $request)
    {
        $attachments = $this->attachmentService->getAttachments($request);
        return $this->ApiResponseData($attachments, 200, (string) $attachments->appends(request()->input())->links());
    }

    public function addAttachmentWithDocId(Request $request)
    {
        $attachment = $this->attachmentService->saveAttachmentAndGetFilename($request);
        return $this->ApiResponseData($attachment, 201);
    }

    public function edit($attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        return $this->ApiResponseData($attachment, 200);

    }

    public function download($attachmentId, $originalName)
    {
        $downloadAttach = $this->attachmentService->download($attachmentId);
        
        return $downloadAttach;

    }
    public function store(Request $request)
    {
        $attachment = $this->attachmentService->saveAttachment($request);
        if ($attachment) {
            return $this->ApiResponseMessage('Record inserted successfully', 201);
        } else {
            return $this->ApiResponseMessage('Record not inserted yet', 400);
        }

    }

    public function update(Request $request)
    {
        $attachment = $this->attachmentService->updateAttachment($request);
        if ($attachment) {
            return $this->ApiResponseMessage('Record updated successfully', 200);
        } else {
            return $this->ApiResponseMessage('Record not updated yet', 400);
        }
    }

    public function delete($id)
    {
        Attachment::findOrFail($id)->delete();
        return $this->ApiResponseMessage('YOUR RECORD HAS BEEN DELETED', 200);
    }

    public function findAttachmentByDocId($documentId)
    {
        $attachments = $this->attachmentService->findAttachmentByDocId($documentId);
        return $this->ApiResponseData($attachments, 200);
    }

    public function deleteAttachByDocId($attachId)
    {
        $attachments = $this->attachmentService->removeAttachmentDocRel($attachId);
        return $this->ApiResponseMessage('YOUR ATTACH HAS BEEN DELETED', 200);
    }

    public function getSearch(Request $request)
    {
       $search = $this->attachmentService->getSearch($request);
       return $search;


    }

    public function generateAttachmentLinksForHTML(Request $request)
    {
       $test =  $this->attachmentService->generateAttachmentLinksForHTML($request->docID, $request->content_html);
       return $test;

    }


}
