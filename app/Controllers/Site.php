<?php

namespace App\Controllers;

class Site extends UNO_Controller
{
    public function index()
    {
        helper('auth');
        $data['user'] = $this->user;
        // echo "<pre>"; print_r(auth()->user()); echo "</pre>"; return;
        return $this->render('home', $data, false);
    }
}
