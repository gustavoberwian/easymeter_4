<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        helper('auth');
        // echo "<pre>"; print_r(auth()->user()); echo "</pre>"; return;
        return $this->render('home');
    }
}
