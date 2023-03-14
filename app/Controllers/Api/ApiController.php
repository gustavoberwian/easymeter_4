<?php

namespace App\Controllers\Api;
use CodeIgniter\Shield\Models\UserModel;
use App\Controllers\BaseController;

class ApiController extends BaseController
{
    public function getUsers()
    {
        $user = new UserModel();
        $find = $user->find(1);
        echo $find -> email;
    }
}
