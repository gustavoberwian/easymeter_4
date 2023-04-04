<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class UpdatePassword extends BaseController
{
    public function index()
    {
        $data['teste'] = 'teste';
        
        return view('update_password', $data);
    }
}
