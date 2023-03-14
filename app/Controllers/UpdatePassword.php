<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class UpdatePassword extends BaseController
{
    public function index()
    {
        return view('update_password');
    }
}
