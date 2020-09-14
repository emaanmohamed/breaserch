<?php

namespace App\Http\Controllers\Admin;

use App\Services\GuzzleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QueuesController extends Controller
{
    private $guzzleService;

    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    public function sendTestMail($docId, Request $request)
    {
        $response = $this->guzzleService->post("queue/send-test-email/{$docId}", $request->all(), [], false);
        return ($response->code == 200) ? response()->json([
            'type' => 'Success',
            'title' => 'Done',
            'message' => $response->message
        ]) :  response()->json([
                'type' => 'Success',
                'title' => 'Done',
                'message' => $response->message
        ]) ;
    }

    public function ScheduledEmail($docId, Request $request)
    {
        $response = $this->guzzleService->post("queue/scheduled-email/{$docId}", $request->all(), [], false);

        return ($response->code == 200) ? response()->json([
            'type' => 'Success',
            'title' => 'Done',
            'message' => $response->message
        ]) :  response()->json([
            'type' => 'Failed',
            'title' => 'Try Again',
            'message' => $response->message
        ]) ;

    }


    public function getScheduledEmails(Request $request)
    {
        $params = [
            'query' => $request->all()
        ];
        $scheduledEmails =  $this->guzzleService->get('queue', $params);
        $pagination = str_replace(config('webService.API_SEARCH_URL'), url('/').'/',optional($scheduledEmails)->pagination);
        $scheduledEmails =  ($scheduledEmails->code == 200) ? optional($scheduledEmails)->data->data : [];

        if ($request->ajax()){
            return view('admin.ajax_includes.queue.display', compact('scheduledEmails', 'pagination'));

        }

        return view('admin.queue.index', compact('scheduledEmails', 'pagination'));
    }

    public function removeQueue(Request $request)
    {
        $this->guzzleService->post('queue/delete', $request->all(), [], false);
        return json_encode([
            'type' => 'success',
            'title' => 'Removed',
            'message' => 'Document has ID: ' . $request->docId . ' has been removed'
        ]);
    }

    public function insertEmailInQueueJob(Request $request)
    {
        $scheduledEmail = $this->guzzleService->post('queue/schedule-email-job', $request->all(), [], false);
        if ($scheduledEmail->code == 200) {
            return json_encode([
                'title'   => "Sending!",
                'message' => "Email has been scheduled successfully!",
                'type'    => "success"
            ]);
        } else {
            return json_encode([
                'title'   => 'Error',
                'message' => "Please try again!",
                'type'    => "warning"
            ]);

        }
    }

    public function terminateSendingEmails($id, Request $request)
    {
        $response = $this->guzzleService->post("queue/email-terminate/{$id}", $request->all(), [],false);
        if ($response->code == 200) {
            return json_encode([
                'title'   => "Terminated!",
                'message' => "Emails has been terminated successfully!",
                'type'    => "success"
            ]);
        } else {
            return json_encode([
                'title'   => 'Error',
                'message' => "Please try again!",
                'type'    => "warning"
            ]);

        }
    }

}
