<?php

namespace App\Http\Controllers\Admin;

use App\Models\SendEmailJob;
use App\Services\SendGridService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Promise\queue;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request)
    {
        $state = json_decode((new SendGridService())->getState());
        $currentDate = date_format(date_create(), 'Y-m-d');
        $yesterdayDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));

        $days = []; $opens = []; $delivered = []; $invalidEmails = []; $blocks = []; $spamReportDrops = [];
        $yesterdayData = null; $stateToday = null;

        if(!empty($state->errors)){

            $state = [];
        }

        for ($i = 0, $len = count($state); $i < $len; $i++) {
            array_push($days, getDayFromDate($state[$i]->date));

            if ($state[$i]->date == $yesterdayDate ) {
                $yesterdayData = [
                    getStateByType($state, 'opens',             $i),
                    getStateByType($state, 'delivered',         $i),
                    getStateByType($state, 'invalid_emails',    $i),
                    getStateByType($state, 'blocks',            $i),
                    getStateByType($state, 'spam_report_drops', $i)
                ];
            }

            if ($state[$i]->date == $currentDate ) {
                $stateToday['opens']                = getStateByType($state, 'opens', $i);
                $stateToday['delivered']            = getStateByType($state, 'delivered', $i);
                $stateToday['invalid_emails']       = getStateByType($state, 'invalid_emails', $i);
                $stateToday['blocks']               = getStateByType($state, 'blocks', $i);
                $stateToday['unsubscribe_drops']    = getStateByType($state, 'unsubscribe_drops', $i);
                $stateToday['spam_report_drops']    = getStateByType($state, 'spam_report_drops', $i);
                $stateToday['spam_reports']         = getStateByType($state, 'spam_reports', $i);
                $stateToday['bounces']              = getStateByType($state, 'bounces', $i);
                $stateToday['clicks']               = getStateByType($state, 'clicks', $i);
                $stateToday['requests']             = getStateByType($state, 'requests', $i);
                $stateToday['unsubscribes']         = getStateByType($state, 'unsubscribes', $i);
            }

            array_push($opens,              getStateByType($state, 'opens',             $i));
            array_push($delivered,          getStateByType($state, 'delivered',         $i));
            array_push($invalidEmails,      getStateByType($state, 'invalid_emails',    $i));
            array_push($blocks,             getStateByType($state, 'blocks',            $i));
            array_push($spamReportDrops,    getStateByType($state, 'spam_report_drops', $i));
        }


        if ($request->ajax()) {
            return view('ajax_statistics', compact('state', 'days', 'opens', 'delivered', 'stateToday',
                'invalidEmails', 'blocks', 'spamReportDrops', 'yesterdayData', 'yesterdayDate'));
        }

        return view('admin.statistics.index', compact('state', 'days', 'opens', 'delivered', 'stateToday',
            'invalidEmails', 'blocks', 'spamReportDrops', 'yesterdayData', 'yesterdayDate'));
    }

    public function checkRMSActivity($email = null)
    {

        $rmsActivity = DB::table('sent_email_job')
                     ->select('queue.*', 'sent_email_job.email', 'research_docs.subject')
                     ->leftJoin('queue', 'queue.id', '=', 'sent_email_job.queue_id')
                     ->leftJoin('research_docs', 'research_docs.id', '=', 'queue.doc_id')
                     ->where('email', 'like', "%$email%")
                     ->get();

        $rmsActivity->map(function ($item){
            $item->Status = sendStatus($item->sent_status);

        });
        return view('admin.ajax_includes.statistics.modals.ajax_rms_activity', compact('rmsActivity'));

    }

    public function test($email)
    {
        $sendgridActivity = json_decode((new SendGridService())->FilterByRecipientEmail($email));
        $sendgridActivitys = $sendgridActivity->messages;

        $sendGridArray = [];
        foreach ($sendgridActivitys as $sendgridActivity)
        {
            if ($sendgridActivity->from_email == 'research@beltonefinancial.com')
            {
                $sendGridArray[] = $sendgridActivity;
            }
        }
        return view('admin.ajax_includes.statistics.modals.ajax_sendgrid_activity', compact('sendGridArray'));


    }


}
