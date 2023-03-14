<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
       
        helper('auth');
        
        if( !auth()->loggedIn())
        {  return  redirect()->to('logged-out');}
         else 
        {
            echo view('common/header');
            return view('home');}
    }
    
}
