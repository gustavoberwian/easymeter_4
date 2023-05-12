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

    public function forum()
    {
        helper('auth');
        $data['user'] = $this->user;
        // echo "<pre>"; print_r(auth()->user()); echo "</pre>"; return;
        return $this->render('forum', $data, 'forum');
    }

    public function assuntoforum()
    {
        $data['user'] = $this->user;
        // echo "<pre>"; print_r(auth()->user()); echo "</pre>"; return;
        return $this->render('assuntoforum', $data, 'forum');
    }
}
