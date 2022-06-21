<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Database;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Request;




class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $database;

    public function __construct()
    {
        $this->database = \App\FirebaseService::connect();
    }

    public function create(Request $request)
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
        $user = $auth->getUser('nnvV9ElMVbPwslDQUotpbRqYbqX2');
        return $user;
    }
}
