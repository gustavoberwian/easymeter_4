<?php

namespace App\Controllers;

class UNO_Controller extends BaseController {

    protected $user = false;

    public function __construct()
    {
        if (auth()->loggedIn()) {

            $this->user           = auth()->user();
            $this->user->group    = auth()->user()->getGroups()[0];
            $this->user->nickname = explode(' ', trim(auth()->user()->username))[0];
            $this->user->alerts   = 0;

            date_default_timezone_set('America/Sao_Paulo');
        }
    }

    protected function render($view, $data = NULL, $menu = true): string
    {
        $controller = explode('\\', service('router')->controllerName());
        $data['class']  = end($controller);
        $data['method'] = service('router')->methodName();
        $data['user']   = $this->user;

        if ($menu) {
            return view($data['class'] . '/template/header', $data)
                . view($data['class'] . '/template/menu', $data)
                . view($data['class'] . '/' . $view, $data)
                . view($data['class'] . '/template/footer', $data);
        } else {
            return view($data['class'] . '/template/header', $data)
                . view($data['class'] . '/' . $view, $data)
                . view($data['class'] . '/template/footer', $data);
        }
    }

    protected function setHistory($msg, $type)
    {
        $data = array(
            'user_id' => $this->user->id,
            'mensagem' => $msg,
            'tipo' => $type,
            'lido' => 0
        );

        $db = \Config\Database::connect();
        //TODO: uncomment after project ready
        //$db->table('esm_user_logs')->insert($data);
    }
}