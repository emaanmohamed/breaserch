<?php

namespace App\Http\Controllers\Admin;

use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentsController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    public function index()
    {
        $companies = $this->guzzleService->get('companies/list');
        $sectors   = $this->guzzleService->get('sectors/list');
        $languages = $this->guzzleService->get('languages/list');
        $countries = $this->guzzleService->get('countries/list');
        $documents = $this->guzzleService->get('documents/list');

        return view('admin.documents.ajax_includes.display_researchDocuments',
            compact('companies', 'sectors', 'languages', 'countries', 'documents'));


    }

    public function getDocuments(Request $request)
    {
        $analysts       = $this->guzzleService->get('analysts/list');
        $companies      = $this->guzzleService->get('companies/list');
        $countries      = $this->guzzleService->get('countries/list');
        $languages      = $this->guzzleService->get('languages/list');

        $analysts       = ($analysts->code == 200) ? optional($analysts)->data : [];
        $companies      = ($companies->code == 200) ? optional($companies)->data : [];
        $countries      = ($countries->code == 200) ? optional($countries)->data : [];
        $languages      = ($languages->code == 200) ? optional($languages)->data : [];

        $params = [
            'query' => $request->all()
        ];

        $documents = $this->guzzleService->get('documents', $params);
        if (optional($documents)->code == 200) {
            $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',
                optional($documents)->pagination);
            $documents  = optional($documents)->data->data;
        } else {
            $documents = null;
        }
        if ($request->ajax()) {
            return view('admin.ajax_includes.document.display_researchDocuments', compact('documents', 'pagination'));
        }

        return view('admin.documents.index', compact('documents', 'analysts', 'companies', 'countries', 'languages','pagination'));
    }

    public function getEmailTemplate($id)
    {
        $emailTemplate = $this->guzzleService->get("emailTemplates/{$id}");
        $emailTemplate = ($emailTemplate->code == 200) ? optional($emailTemplate)->data->email_template : [];

        return view('admin.documents.doc_includes.doc_editor', compact('emailTemplate'));
    }

    public function addDocuments()
    {
        $templates      = $this->guzzleService->get('emailTemplates/list');
        $languages      = $this->guzzleService->get('languages/list');
        $countries      = $this->guzzleService->get('countries/list');
        $reportTypes    = $this->guzzleService->get('reports/list');
        $reportSubTypes = $this->guzzleService->get('reportSubType/list');
        $companies      = $this->guzzleService->get('companies/list');
        $sectors        = $this->guzzleService->get('sectors/list');
        $analysts       = $this->guzzleService->get('analysts/list');

        $templates      = ($templates->code == 200) ? optional($templates)->data : [];
        $languages      = ($languages->code == 200) ? optional($languages)->data : [];
        $countries      = ($countries->code == 200) ? optional($countries)->data : [];
        $reportTypes    = ($reportTypes->code == 200) ? optional($reportTypes)->data : [];
        $reportSubTypes = ($reportSubTypes->code == 200) ? optional($reportSubTypes)->data : [];
        $companies      = ($companies->code == 200) ? optional($companies)->data : [];
        $sectors        = ($sectors->code == 200) ? optional($sectors)->data : [];
        $analysts       = ($analysts->code == 200) ? optional($analysts)->data : [];

        return view('admin.documents.add',
            compact('templates', 'languages', 'countries', 'reportTypes', 'reportSubTypes', 'companies', 'sectors',
                'analysts'));
    }

    public function editDocuments($documentId)
    {
        $templates      = $this->guzzleService->get('emailTemplates/list');
        $languages      = $this->guzzleService->get('languages/list');
        $countries      = $this->guzzleService->get('countries/list');
        $reportTypes    = $this->guzzleService->get('reports/list');
        $reportSubTypes = $this->guzzleService->get('reportSubType/list');
        $companies      = $this->guzzleService->get('companies/list');
        $sectors        = $this->guzzleService->get('sectors/list');
        $analysts       = $this->guzzleService->get('analysts/list');
        $tags           = $this->guzzleService->get('tags/get');
        $document       = $this->guzzleService->get("documents/detail/{$documentId}");
        $attachments    = $this->guzzleService->get("attachments/get/{$documentId}");


        $templates      = ($templates->code == 200) ? optional($templates)->data : [];
        $languages      = ($languages->code == 200) ? optional($languages)->data : [];
        $countries      = ($countries->code == 200) ? optional($countries)->data : [];
        $reportTypes    = ($reportTypes->code == 200) ? optional($reportTypes)->data : [];
        $reportSubTypes = ($reportSubTypes->code == 200) ? optional($reportSubTypes)->data : [];
        $companies      = ($companies->code == 200) ? optional($companies)->data : [];
        $sectors        = ($sectors->code == 200) ? optional($sectors)->data : [];
        $analysts       = ($analysts->code == 200) ? optional($analysts)->data : [];
        $tags           = ($tags->code == 200) ? optional($tags)->data : [];
        $document       = ($document->code == 200) ? optional($document)->data : [];
        $attachments    = ($attachments->code == 200) ? optional($attachments)->data : [];

        if ($document) {
            return view('admin.documents.edit',
                compact('templates', 'languages', 'countries', 'reportTypes', 'reportSubTypes',
                    'companies', 'sectors', 'analysts', 'document', 'attachments', 'tags'));
        } else {
            return redirect()->route('get-document')->withStatus('Cannot find document: '.$documentId);
        }

    }

    public function duplicateDocument($documentId)
    {

        $templates      = $this->guzzleService->get('emailTemplates/list');
        $languages      = $this->guzzleService->get('languages/list');
        $countries      = $this->guzzleService->get('countries/list');
        $reportTypes    = $this->guzzleService->get('reports/list');
        $reportSubTypes = $this->guzzleService->get('reportSubType/list');
        $companies      = $this->guzzleService->get('companies/list');
        $sectors        = $this->guzzleService->get('sectors/list');
        $analysts       = $this->guzzleService->get('analysts/list');
        $tags           = $this->guzzleService->get('tags/get');
        $document       = $this->guzzleService->get("documents/details/{$documentId}");
        $attachments    = $this->guzzleService->get("attachments/get/{$documentId}");


        $templates      = ($templates->code == 200) ? optional($templates)->data : [];
        $languages      = ($languages->code == 200) ? optional($languages)->data : [];
        $countries      = ($countries->code == 200) ? optional($countries)->data : [];
        $reportTypes    = ($reportTypes->code == 200) ? optional($reportTypes)->data : [];
        $reportSubTypes = ($reportSubTypes->code == 200) ? optional($reportSubTypes)->data : [];
        $companies      = ($companies->code == 200) ? optional($companies)->data : [];
        $sectors        = ($sectors->code == 200) ? optional($sectors)->data : [];
        $analysts       = ($analysts->code == 200) ? optional($analysts)->data : [];
        $tags           = ($tags->code == 200) ? optional($tags)->data : [];
        $document       = ($document->code == 200) ? optional($document)->data : [];
        $attachments    = ($attachments->code == 200) ? optional($attachments)->data : [];

        if ($document) {
            return view('admin.documents.duplicate',
                compact('templates', 'languages', 'countries', 'reportTypes', 'reportSubTypes',
                    'companies', 'sectors', 'analysts', 'document', 'attachments', 'tags'));
        } else {
            return redirect()->route('get-document')->withStatus('Cannot find document: '.$documentId);
        }


    }

    public function getEditor($emailId)
    {
        $emailTemplate = $this->guzzleService->get("emailTemplates/{$emailId}");
        $emailTemplate = ($emailTemplate->code == 200) ? optional($emailTemplate)->data->email_template : [];
        return view('admin.documents.doc_includes.doc_editor', compact('emailTemplate'));
    }


    public function storeDocuments(Request $request)
    {
        $document = $this->guzzleService->post('documents/store', $request->all(), [], false);

        if ($document->code == 201) {
            $req   = [];
            $req[] = [
                'name'     => 'docId',
                'contents' => optional($document)->data->id
            ];
            if (!empty($request->attachments)) {
                foreach ($request->attachments as $attachment) {
                    $req[] = [
                        'name'      => 'attachments[]',
                        'filename'  => $attachment->getClientOriginalName(),
                        'extension' => $attachment->getClientOriginalExtension(),
                        'size'      => $attachment->getClientSize(),
                        'contents'  => fopen($attachment->getPathname(), 'r')
                    ];
                }

                $files = $this->guzzleService->post('documents/attachment/add', $req, [], true);
            }
            return redirect()->route('get-document')->with(['status' => 'Document Added successfully!',
                                                             'statusType' => 'danger']);
        } else {
            return redirect()->route('get-document')->withStatus($document->message);
        }
    }

    public function updateDocuments(Request $request, $documentId)
    {
        $document = $this->guzzleService->post("documents/update/{$documentId}", $request->all(), [], false);

        if ($document->code == 200) {
            $req   = [];
            $req[] = [
                'name'     => 'docId',
                'contents' => $documentId
            ];
            if (!empty($request->attachments)) {
                foreach ($request->attachments as $attachment) {
                    $req[] = [
                        'name'      => 'attachments[]',
                        'filename'  => $attachment->getClientOriginalName(),
                        'extension' => $attachment->getClientOriginalExtension(),
                        'size'      => $attachment->getClientSize(),
                        'contents'  => fopen($attachment->getPathname(), 'r')
                    ];
                }
                $this->guzzleService->post('documents/attachment/add', $req, [], true);
            }
            return redirect()->route('get-document')->withStatus('Document Updated successfully! ');

        } else {
            return redirect()->route('get-document')->withStatus($document->message);
        }
    }

    public function removeAttach($attachId)
    {
        $this->guzzleService->post("attachments/delete/{$attachId}");

    }

    public function displayTemplateByDocId($documentId)
    {
        $document = $this->guzzleService->get("documents/details/{$documentId}");
//        $document->html_content = $this->guzzleService->post("attachments/generateAttachLink");
        $document = ($document->code == 200) ? optional($document)->data : [];
        return $document->html_content;
    }

    public function ajaxDisplaySendMailModal($documentId)
    {
        $document = $this->guzzleService->get("documents/details/{$documentId}");
        $groups   = $this->guzzleService->get('groups/get');
        $document = ($document->code == 200) ? optional($document)->data : [];
        $groups   = ($groups->code == 200)   ? optional($groups)->data : [];


        return view('admin.ajax_includes.document.modals.ajax_display_send_mail_content',
            compact('document', 'groups'));


    }
}
