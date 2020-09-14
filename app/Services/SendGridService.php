<?php

namespace App\Services;


use DateTime;

class SendGridService
{
    private $response;
    private $curl;
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('SENDGRID_API_KEY');

    }

    private function getDataFromURL($url)
    {
        $this->curl = curl_init();

        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{}",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer {$this->apiKey}"
            ),
        ));


        $this->response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        curl_close($this->curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        }

        return $this->response;
    }

    public function __toString()
    {
        try
        {
            return (string) $this->response;
        }
        catch (\Exception $exception)
        {
            return $exception->getMessage();
        }
    }

    public function getState($period = 'day', $startDate = null)
    {
        if (is_null($startDate)) {
            $currentDate = date_format(date_create(), 'Y-m-d');
            $startDate = date('Y-m-d', strtotime('-1 week', strtotime($currentDate)));
        }

        return $this
            ->getDataFromURL("https://api.sendgrid.com/v3/stats?aggregated_by=$period&start_date=$startDate");
    }

    public function FilterByRecipientEmail($email, $limit = 20)
    {
        $email = urlencode($email);

        return $this
            ->getDataFromURL("https://api.sendgrid.com/v3/messages?limit=$limit&query=to_email%3D%22$email%22");
    }

    public function FilterBySubject($subject, $limit = 20)
    {
        $subject = urlencode($subject);

        return $this
            ->getDataFromURL("https://api.sendgrid.com/v3/messages?limit=$limit&query=subject%3D%22$subject%22");
    }

}
