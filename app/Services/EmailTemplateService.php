<?php


namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Request;

class EmailTemplateService
{
    public function getAllEmailTemplate()
    {
        $emailTemplate = EmailTemplate::all();
        return $emailTemplate;
    }

    public function getTemplateById($id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);
        return $emailTemplate;
    }

    public function getEmailTemplates($request)
    {
        $params = $request->title;
        $emailTemplate = EmailTemplate::filter($params)->paginate(50);
        return $emailTemplate;
    }

    public function insertEmailTemplate(Request $request)
    {
        $insert = EmailTemplate::create([
            'title'          => $request->title,
            'email_template' => $request->email_template,
            'user_id'        => $request->user_id
        ]);
        return $insert;

    }

}
