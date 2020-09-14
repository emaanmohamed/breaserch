<?php

namespace App\Http\Controllers\Api;

use App\Services\QueueService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QueuesController extends ApiController
{
    private $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function index()
    {
        $scheduledEmails = $this->queueService->getScheduledEmails();
        return $this->ApiResponseData($scheduledEmails, 200, (string)$scheduledEmails->links());
    }


    public function store(Request $request)
    {
        $rules  = [
            'doc_id'          => 'required',
            'send_status'     => 'required',
            'research_doc_id' => 'required'
        ];
        $validator = $this->validate($request->all(), $rules);
        if ($validator->fails()) {
            return $this->ApiResponseMessage($validator->message(), 400);
        } else {
            $insert = $this->queueService->insertQueue($request);
            if ($insert == true) {
                $this->ApiResponseMessage('Record inserted successfully', 201);
            } else {
                return $this->ApiResponseMessage('Record not inserted yet', 400);
            }
        }

    }


    public function sendTestEmail($docId, Request $request)
    {
        $response = $this->queueService->sendTestEmail($docId, $request->emailFrom, $request->emailTo);
        return ($response) ? $this->ApiResponseMessage('Email has been sent successfully :)', 200) : $this->ApiResponseMessage('Email not sent :(', 400);
    }

    public function ScheduledEmail($docId, Request $request)
    {
        $response = $this->queueService->ScheduledEmail($docId, $request->mailFrom, $request->actionDate, $request->groups);
        return ($response) ? $this->ApiResponseMessage('Email has been scheduled successfully :)', 200) : $this->ApiResponseMessage('Email not scheduled :(', 400);

    }

    public function sendScheduledAndLiveEmail()
    {
        $response =  $this->queueService->sendScheduledAndLiveEmail();
        return ($response) ? $this->ApiResponseMessage('Pending Email has been scheduled successfully :)', 200) : $this->ApiResponseMessage('Pending Email not scheduled :(', 400);
    }

    public function update($id, Request $request)
    {

    }

    public function insertEmailInQueueJob(Request $request)
    {
        $res = $this->queueService->insertEmailInQueueJob($request);
        return ($res) ? $this->ApiResponseMessage('Your email has been scheduled successfully', 200) : $this->ApiResponseMessage('your record not deleted yet', 400);
    }

    public function delete(Request $request)
    {
        $queue = $this->queueService->removeQueue($request);
        return ($queue) ? $this->ApiResponseMessage('Your record has been deleted successfully', 200) : $this->ApiResponseMessage('your record not deleted yet', 400);

    }

    public function updateQueueStatusFromSendingToComplete()
    {
      return $this->queueService->updateQueueStatusFromSendingToComplete();
    }

    public function updateQueueStatusFromScheduleToSending()
    {
        return $this->queueService->updateQueueStatusFromScheduleToSending();
    }

    public function terminateSendingEmails($id)
    {
        $terminateEmails = $this->queueService->terminateSendingEmails($id);
        return ($terminateEmails) ? $this->ApiResponseMessage('Emails has been terminate successfully', 200) : $this->ApiResponseMessage('Emails not terminated yet', 400);
    }


}
