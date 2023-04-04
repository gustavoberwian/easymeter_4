<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Login extends BaseController
{
    public function index()
    {
        return view('login');
    }
}
