<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attachment;
use App\Services\GuzzleService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttachmentsController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    public function getAttachments(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $attachments = $this->guzzleService->get('attachments', $params);
        if (optional($attachments)->code == 200) {
            $pagination  = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',
                optional($attachments)->pagination);
            $attachments = optional($attachments)->data->data;
        } else {
            $attachments = null;
        }
        return view('admin.attachments.index', compact('attachments', 'pagination'));
    }

    public function editAttachments($attachmentId)
    {
        $response   = $this->guzzleService->get("attachments/edit/{$attachmentId}");
        $attachment = optional($response)->data;
        return view('admin.attachments.edit', compact('attachment'));
    }

    public function uploadAttachments(Request $request)
    {
        $attachment = $request->attachments;

        $items      = [];
        $items[]    = [
            'name'      => 'attachments',
            'filename'  => $attachment->getClientOriginalName(),
            'extension' => $attachment->getClientOriginalExtension(),
            'size'      => $attachment->getClientSize(),
            'contents'  => fopen($attachment->getPathname(), 'r')
        ];

        $response = $this->guzzleService->post('attachments/store', $items, [], true);
        if ($response->code == 201) {
            return redirect()->back()->with([
                    'status' => "Attachment Added successfully! ",
                    'statusType' => "success"
            ]);
        } else {
            return redirect()->back()->withStatus('Nothing has been added! ');
        }
    }

    public function updateAttachments(Request $request)
    {
        $attachment = $request->attachments;
        $items      = [];
        $items[]    = [
            'name'      => 'id',
            'contents'  => $request->id
        ];
        $items[]    = [
            'name'      => 'attachments',
            'filename'  => $attachment->getClientOriginalName(),
            'extension' => $attachment->getClientOriginalExtension(),
            'size'      => $attachment->getClientSize(),
            'contents'  => fopen($attachment->getPathname(), 'r')
        ];
        $response   = $this->guzzleService->post('attachments/update', $items, [], true);
        return response()->json($response);
    }

    public function removeAttachments($attachmentId)
    {
        $response = $this->guzzleService->get("attachments/delete/{$attachmentId}");
        if ($response->code == 200) {
            return "Record deleted successfully";
        } else {
            return "Record not deleted yet";
        }
    }

}
