<?php


namespace App\Services;

use App\Enum\Status;
use App\Models\ClientEmail;
use App\Models\ClientGroupRel;
use App\Models\Group;
use App\Models\Queue;
use App\Models\ResearchDoc;
use App\Models\ScheduleSendingEmailLog;
use App\Models\SendEmailJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class QueueService
{
    private $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }


    public function sendTestEmail($docId, $emailFrom, $emailTo)
    {
        $doc          = ResearchDoc::find($docId);
        $doc          = $this->documentService->ParsDocumentVariables($doc);
        $html_content = optional($doc)->html_content;
        $html_content = $this->documentService->generateAttachmentLinksForHTML($doc->id, $html_content);
        $subject      = optional($doc)->subject;
        if (!empty($html_content)) {
            if($emailFrom == 'beltonetrading@beltonefinancial.com'){
                $name = "Research Management System";
            }else{
                $name = "Research Management System";
            }
            DB::table('email_logs')->insert([
                'email_to' => json_encode($emailTo),
                'email_from' => $emailFrom,
                'email_subject' => $subject,
                'email_body' => $html_content,
                'research_doc_id' => $docId,
                'notes'  => 'test email'
            ]);
            Mail::send('emails.send', ['data' => $html_content],
                function ($callback) use ($emailFrom, $emailTo, $subject,$name) {
                    $callback->from($emailFrom,$name);
                    $callback->to($emailTo);
                    $callback->subject($subject);
                });
            Log::error(json_encode(Mail::failures()));
            return true;
        }
        return false;
    }


    public function sendScheduledAndLiveEmail()
    {
       // DB::beginTransaction();
        try {
            $emails = SendEmailJob::join('queue', 'queue.id', 'sent_email_job.queue_id')
                ->leftjoin('research_docs AS docs', 'docs.id', 'sent_email_job.doc_id')
                ->select('sent_email_job.id', 'sent_email_job.email', 'docs.subject', 'docs.html_content',
                    'queue.email_from', 'sent_email_job.doc_id')->where('sent', '=', null)->take(500)->get();
            $IDs    = [];
            foreach ($emails as $email) {
                $html_content = $this->documentService->generateAttachmentLinksForHTML($email->doc_id,
                    $email->html_content);
                if (empty($email->email_from)) {
                    $email->email_from = config('mail.from.address');
                }
                //$name = $email->email_from;
                if($email->email_from == 'beltonetrading@beltonefinancial.com'){
                    $name = "Beltone Trading";
                }else{
                    $name = "Beltone Research";
                }
                try{
                    DB::table('email_logs')->insert([
                        'email_to' => $email->email,
                        'email_from' => $email->email_from,
                        'email_subject' => $email->subject,
                        'email_body' => "",//$html_content,
                        'research_doc_id' => $email->doc_id
                    ]);
                    if(DB::table('email_logs')->where('email_to',$email->email)->where('research_doc_id',$email->doc_id)->count() > 1){
                        continue;
                    }
                    SendEmailJob::where('id', $email->id)->update([
                        'sent'      => 1,
                        'sent_date' => Carbon::now()
                    ]);
                    Mail::send('emails.send', ['data' => $html_content], function ($callback) use ($email,$name) {
                        $callback->from($email->email_from,$name);
                        $callback->to($email->email);
                        $callback->subject($email->subject);
                    });

                } catch (\Exception $exception) {
                    // DB::rollBack();
                    Log::error(json_encode(Mail::failures()));
                    report($exception);
                    continue;
                }
                $IDs[]  = $email->id;

            }
            $queueIds = SendEmailJob::distinct('queue_id')->whereIn('id', $IDs)->pluck('queue_id')->toArray();
            foreach ($queueIds as $queue){
                $emailsAlreadySent = SendEmailJob::select('email')
                    ->where('queue_id', $queue)
                    ->where('sent', 1)->count();
                $totalEmails  = Queue::select('total_emails')->where('id', $queue)->value('total_emails');
                $remainingEmails = $totalEmails - $emailsAlreadySent;
                Queue::where('id', $queue)
                    ->update([
                        'remaining' => $remainingEmails
                    ]);
            }


            $this->updateQueueStatusFromSendingToComplete();
         //   DB::commit();
            return 'done';

        } catch (\Exception $exception) {
           // DB::rollBack();
            report($exception);
            return $exception->getMessage();
        }
    }


    public function updateQueueStatusFromSendingToComplete()
    {
        /*
      $sendQueueIDs = Queue::select(DB::raw("id, (SELECT COUNT(sent_email_job.id) FROM sent_email_job WHERE sent_email_job.sent IS NULL AND sent_email_job.queue_id = queue.id) AS countEmails"))
          ->where('sent_status', Status::Sending)->where(DB::raw('(SELECT COUNT(sent_email_job.id) FROM sent_email_job WHERE sent_email_job.sent IS NULL AND sent_email_job.queue_id = queue.id)'), 0)->get()->toArray();
        $sendQueueIDs = array_map(function ($item){return $item['id'];}, $sendQueueIDs);
      Queue::whereIn('id', $sendQueueIDs)->update([
          'sent_status' => Status::Complete
      ]);*/
       $queues =  Queue::where('sent_status', Status::Sending)
            ->where(DB::raw('(SELECT COUNT(sent_email_job.id) FROM sent_email_job
                                    WHERE sent_email_job.sent IS NULL
                                    AND sent_email_job.queue_id = queue.id)'),
                0)->get();

        Queue::where('sent_status', Status::Sending)
            ->where(DB::raw('(SELECT COUNT(sent_email_job.id) FROM sent_email_job
                                    WHERE sent_email_job.sent IS NULL
                                    AND sent_email_job.queue_id = queue.id)'),
                0)
            ->update([
                'sent_status' => Status::Complete
            ]);


        foreach ($queues as $queue) {
            ResearchDoc::where('id', $queue->doc_id)->update([
                'status' => Status::Complete
            ]);
        }

        return 'send status updated to complete';

    }


    public function getScheduledEmails()
    {
        $scheduledEmails = Queue::select('rd.subject', 'queue.*')
            ->leftJoin('research_docs AS rd', 'queue.doc_id', '=', 'rd.id')
            ->orderBy('queue.id','desc')
            ->paginate(50);

        $scheduledEmails->map(function ($item) {
            $item->Status = sendStatus($item->sent_status);
            $item->groups = Group::getGroupsIn($item->groups)->pluck('name');
        });
        return $scheduledEmails;
    }

    public function ScheduledEmail($docId, $mailFrom, $actionDate, $groups)
    {
        try {
            DB::transaction(function () use ($docId, $mailFrom, $actionDate, $groups) {
                $status     = (!empty($actionDate)) ? Status::Scheduled : Status::Pending;
                $emailQueue = Queue::create([
                    'doc_id'      => $docId,
                    'email_from'  => $mailFrom,
                    'action_date' => $actionDate,
                    'groups'      => $groups,
                    'sent_status' => $status,
                ]);
                ResearchDoc::where('id', $docId)->update(['status' => $status]);

                if ($status == Status::Pending) {
                    $request = (object) ['id' => $emailQueue->id, 'doc_id' => $docId];
                    $this->insertEmailInQueueJob($request);
                }

            }, 3);
            return 'sent email job filled successfully by scheduled email';

        } catch (\Exception $exception) {
            report($exception);
            return $exception->getMessage();
        }

    }

    public function updateQueueStatusFromScheduleToSending()
    {
        $queues = Queue::select('id', 'doc_id')->where('sent_status', Status::Scheduled)->where('action_date', '<=',
            Carbon::now())->get();
        foreach ($queues as $queue) {
            $request = (object) ['id' => $queue->id, 'doc_id' => $queue->doc_id];
            $this->insertEmailInQueueJob($request);
        }
        return 'done ';
    }

    public function insertEmailInQueueJob($request)
    {

        $queue  = Queue::findOrFail($request->id);
        $queue->sent_status = Status::Sending;

        $clientEmails = ClientEmail::distinct()
            ->whereRaw("
            client_id IN (
            select clientGroup.client_id FROM client_group_rel AS clientGroup JOIN clients ON clients.id = clientGroup.client_id WHERE
            clients.is_active = 1 AND clientGroup.group_id IN ({$queue->groups})
            )
            ")->get(['email_address']);

        $queue->total_emails = $clientEmails->count();
        $queue->remaining    = $clientEmails->count();
        $queue->save();

        $emailItem = [];
        foreach ($clientEmails as $clientEmail) {
            $emailItem[] = [
                'queue_id' => $request->id,
                'email'    => $clientEmail->email_address,
                'doc_id'   => $request->doc_id
            ];
        }

        $sendEmailJob = SendEmailJob::insert($emailItem);

        return $sendEmailJob;

    }

    public function terminateSendingEmails($id)
    {
       SendEmailJob::where('queue_id', $id)->update(['sent' => Status::Canceled]);
        Queue::where('id', $id)->update(['sent_status' => Status::Canceled]);
        return 'emails terminated successfully';
    }

    public function removeQueue($request)
    {
        $queue = Queue::findOrFail($request->id)->delete();
        return $queue;
    }

}
