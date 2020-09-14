<?php

namespace App\Traits;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

trait LDAPTrait {

    private function checkUser($request, $isUsername)
    {
        if ($isUsername === true)
            $user = User::where('username', $request->username)->get();
        else
            $user = User::where('email', $request->username)->get();

        if (isset($user[0]) && count($user)) {
            Auth::loginUsingId($user[0]->id);
            //$user = Auth::user();
            //$user->createToken('MyApp')->accessToken;
        } else {
            $this->createUser($request);
        }
    }

    private function createUser($request)
    {
        $data = $this->getDataFromActiveDirectory($request);

        if (! is_null($data)) {
            $user = User::create([
                'email' => $data->mail,
                'name' => $data->name,
                'username' => $data->username,
                'department' => $data->department,
                'password' => '',
            ]);
        } else {
            $user = User::create([
                'email' => $request->username,
                'name' => $request->username,
                'password' => '',
            ]);
        }

        Auth::loginUsingId($user->id);
    }

    private function getDataFromActiveDirectory($request)
    {
        $client = new Client(['base_uri' => env('LDAP_API_SERVICE_URL')]);

        if (strpos($request->username, '@'))
            $response = $client->request('GET', 'getUserMainInfoByEmail/' . $request->username);
        else
            $response = $client->request('GET', 'getUserMainInfoByUsername/' . $request->username);

        if ($response->getStatusCode() == 200)
            return \GuzzleHttp\json_decode($response->getBody());

        return null;
    }
}
