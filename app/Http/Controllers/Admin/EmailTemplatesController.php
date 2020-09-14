<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmailTemplate;
use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailTemplatesController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    private function checkIfEmailTemplateExist($columnName, $name)
    {
        return EmailTemplate::where($columnName, $name)->count();
    }

    public function index(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $emailTemplates = $this->guzzleService->get('emailTemplates', $params);
        if (optional($emailTemplates)->code == 200) {
            $pagination     = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',
                optional($emailTemplates)->pagination);
            $emailTemplates = optional($emailTemplates)->data->data;
        } else {
            $emailTemplates = null;
        }
        if ($request->ajax()) {
            return view('admin.emailTemplate.index', compact('emailTemplates', 'pagination'));
        }

        return view('admin.emailTemplate.index', compact('emailTemplates', 'pagination'));
    }

    public function edit($id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        return view('admin.ajax_includes.emailTemplate.edit_form', compact('emailTemplate'));

    }

    public function add()
    {
        return view('admin.ajax_includes.emailTemplate.add_form');
    }

    public function getCreate()
    {

    }

    public function store(Request $request)
    {
        if (empty($request->template_name) || empty($request->area))
            return redirect()->back()
                ->withStatus("Please make sure that you entered template name and body");

        if ($this->checkIfEmailTemplateExist('title', $request->template_name) < 1) {
                EmailTemplate::create([
                    'title'          => $request->template_name,
                    'email_template' => $request->area
//                  'user_id' => Auth::user()->id
                ]);

            $emailTemplates = EmailTemplate::orderBy('created_at', 'desc')->get();
            return redirect()->route('emailTemplate-list')->with(['emailTemplates' => $emailTemplates]);

            } else {

            return redirect()->back()
                ->withStatus("We have Email Template have the same name ({$request->template_name}). Please change it and try again.")
                ->withTemplate($request->area);
            }
        }


    public function update(Request $request, $templateId)
    {
        EmailTemplate::findOrFail($templateId)->update([
            'title'          => $request->template_name,
            'email_template' => $request->area
        ]);

        $emailTemplates = EmailTemplate::orderBy('created_at', 'desc')->get();
        return redirect()->route('emailTemplate-list')->with(['emailTemplates' => $emailTemplates]);
    }

    public function delete($id)
    {
        EmailTemplate::findOrFail($id)->delete();
        return redirect()->route('emailTemplate-list');

    }

    public function ajaxEmailTemplate(Request $request, $templateId)
    {
        $emailTemplate = EmailTemplate::findOrFail($templateId);
        return json_encode(['title' => $emailTemplate->title, 'template' => $emailTemplate->email_template]);
    }



}
