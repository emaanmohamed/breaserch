<?php

namespace App\Http\Controllers\Admin\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\LDAPTrait;
use Illuminate\Support\Facades\DB;

class LDAPController extends Controller
{
    use LDAPTrait;

    public function ldapLogin(Request $request)
    {
        try {
            $client = new Client();
            $isUsername = false;
            if (strpos($request->username, '@')) {
                $uri = config('LDAPApi.LDAP_API_SERVICE_URL') . 'loginByEmail';
                $credentials = ['email' => $request->username, 'password' => $request->password];
            } else {
                $isUsername = true;
                $uri = config('LDAPApi.LDAP_API_SERVICE_URL') . 'loginByUsername';

                $credentials = ['username' => $request->username, 'password' => $request->password];
            }
            $response = $client->post($uri, [
               RequestOptions::JSON => $credentials
            ]);
            if(DB::table('admins')->where('email',trim($request->username))->count() == 0){
                return redirect()->back()->with(['status' => "you don't have permission",
                    'statusType' => 'danger']);
            }
            if ($response->getStatusCode() == 200)
                $this->checkUser($request, $isUsername);

        } catch (RequestException $exception) {
            if (is_null($exception->getResponse()))
                return redirect()->back()->with(['status' => 'Please enter your credentials.',
                                                 'statusType' => 'danger']);

            if (is_null(json_decode($exception->getResponse()->getBody())))
                return redirect()->back()->with(['status' => 'Error: Please contact System Admin because we can\'t contact LDAP service right now :( "Response => Null (Empty Response)"',
                                                 'statusType' => 'danger']);

            $message = json_decode($exception->getResponse()->getBody())->message;
            return redirect()->back()->with(['status' => $message, 'statusType' => 'danger']);
        }

      return redirect()->route('dashboard');

    }
}

