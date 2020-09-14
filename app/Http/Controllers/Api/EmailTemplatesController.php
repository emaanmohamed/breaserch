<?php

namespace App\Http\Controllers\Api;

use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailTemplatesController extends ApiController
{
    private $emailTemplates;

    public function __construct(EmailTemplateService $emailTemplates)
    {
        $this->emailTemplates = $emailTemplates;
    }

    public function getEmailTemplates()
    {
        $emailTemplates = $this->emailTemplates->getAllEmailTemplate();
        return $this->ApiResponseData($emailTemplates, 200);
    }

    public function index(Request $request)
    {
        $emailTemplates = $this->emailTemplates->getEmailTemplates($request);
        return $this->ApiResponseData($emailTemplates, 200, (string)$emailTemplates->links());
    }

    public function loadTemplate($emailId)
    {
        $emailTemplate = $this->emailTemplates->getTemplateById($emailId);
        return $this->ApiResponseData($emailTemplate, 200);

    }

    public function store(Request $request)
    {
        $rules = [
            'title' => $request->title,
            'email_template' => $request->email_template,
            'user_id' => $request->user_id
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->messages(), 400);
        } else {
            $insert = $this->emailTemplates->insertEmailTemplate($request);
            if ($insert == true) {
                return $this->ApiResponseMessage('Record inserted successfully', 201);
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
