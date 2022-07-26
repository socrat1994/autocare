<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Database;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;
use function MongoDB\BSON\toJSON;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $database;

    public function __construct()
    {
        $this->database = \App\FirebaseService::connect();
    }

    public function create1(Request $request)
    {
        $this->database
            ->getReference('q')
            ->set([
                'a' => 1,
            ]);
        return response()->json('blog has been created');
    }

    public function get(Auth $auth)//get many users
    {
        $users = $auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);
        foreach ($users as $user) {
            return response()->json($user);
        }
    }

    public function getuser(Auth $auth)//get one user
    {
        $access_token="EAAPCm9ZBnJIoBALkNwFK6KEhXyx6EyhQ258koVhM4X1cBv42F12IdIXzcL6xGZBaAhd8Mq55Ayn4nfc0dhIOdN0Kvw3rLEBddLZBEiL7X8ry3wBPwm5RU9oXDTsidZCeRVyf292YQz4I4GsubEFsP9jqpUrbKg2LjK6pyiWrhQdSjeVnxToUSJd4YcGtZCL2NTpHzlsajg93DxRZCyxzYBJxxO99RnkJAwyh1LDqz5r5Aqu1o6hJWN";
        //$user = $auth->getUser('IgIY8OC3BzNYa3bDOBz03AruqeB2');
        $user = Socialite::driver('facebook')->userFromToken($access_token);
        //foreach ($user as $user)
        $url = "https://www.facebook.com/100007222008628";
        $resp = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
        $data = curl_exec($ch);
        curl_close($ch);
        // Load HTML to DOM Object
        $dom = new DOMDocument();
        @$dom->loadHTML($data); //$url = url("/"); // http://localhost:8000/template/1/11

        return response()->json($user);
    }
}
