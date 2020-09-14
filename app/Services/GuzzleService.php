<?php

namespace App\Services;

use App\Traits\ApiTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

class GuzzleService
{
    private $client;

    use ApiTrait;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('webService.API_SEARCH_URL')
        ]);
    }

    public function get($url, $header = null)
    {
        try {
            $response = $this->client->get($url, $header);
            $result   = json_decode($response->getBody());
            return $result;

        } catch (ClientException $clientException) {
            return $this->ApiResponseMessage('Invalid Request', 404);
        }

    }

    public function post($url, $param = [], $header = [], $isFile=false)
    {
        try {
            array_push($header, [
                'Accept' => 'application/json',
            ]);
            $content = [
                'headers'       => $header,
                'form_params'   => $param
            ];
            if($isFile){
                unset($content['form_params']);
                $content['multipart'] = $param;
            }


            $response = $this->client->post($url, $content);
            $result = json_decode($response->getBody());
            return $result;

        } catch (ClientException $clientException) {
            $result = json_decode($clientException->getResponse()->getBody()->getContents());
            return $result;
        }

    }
}
