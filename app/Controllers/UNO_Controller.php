<?php

namespace App\Controllers;

use App\Models\Admin_model;
use App\Models\Shopping_model;

class UNO_Controller extends BaseController {

    protected $user = false;
    
    /**
     * @var Shopping_model
     */
    private Shopping_model $shopping_model;

    private Admin_model $admin_model;

    public function __construct()
    {
        $this->shopping_model = new Shopping_model();

        $this->admin_model = new Admin_model();
        

        if (auth()->loggedIn()) {

            $this->user             = auth()->user();

            $this->user->demo = false;
            if ($this->user->inGroup('demo')) {
                $this->user->demo = true;
            }

            if  (auth()->user()->getGroups()) {
                $this->user->group  = auth()->user()->getGroups()[0];
            }

            $this->user->nickname   = explode(' ', trim(auth()->user()->username))[0];
            $this->user->alerts     = $this->shopping_model->CountAlerts($this->user->id);
            $this->user->type       = $this->shopping_model->get_user_relation($this->user->id);

            if (service('router')->methodName() !== 'index') {
                $this->user->config = $this->shopping_model->get_client_config(service('uri')->getSegment(3));
            }

            date_default_timezone_set('America/Sao_Paulo');
        }
    }

    protected function render($view, $data = NULL, $menu = true)//: string
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();

        $builder = $db->table('esm_user_logs');
        $controller = explode('\\', service('router')->controllerName());

        $data['class']  = end($controller);
        $data['method'] = service('router')->methodName();
        $data['user']   = $this->user;

        // $data['chamados']        = $this->admin_model->get_chamados("aberto", 5);
        // $data['chamados_count']  = $this->admin_model->count_chamados();
        // $data['chamados_unread'] = $this->admin_model->count_chamados("aberto");

        $data['logs']   = $builder->get()->getNumRows();

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