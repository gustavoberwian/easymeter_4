<?php

namespace App\Controllers;

use App\Models\Shopping_model;
use App\Models\Energy_model;

class Shopping extends UNO_Controller
{
    private Shopping_model $shopping_model;
    private Energy_model $energy_model;

    public function __construct()
    {
        parent::__construct();

        $this->shopping_model = new Shopping_model();
        $this->energy_model = new Energy_model();
    }

    public function index()
    {
        $this->setHistory('Acesso à página inicial', 'acesso');

        $data['user'] = $this->user;

        if ($this->user->inGroup('shopping', 'admin')) {

            $data['entity_id'] = $this->shopping_model->get_entity_by_user($this->user->id);
            $data['groups'] = $this->shopping_model->get_groups_by_entity($data['entity_id']);
            $data['overall_c'] = $this->energy_model->GetOverallConsumption(1);
            $data['overall_l'] = $this->energy_model->GetOverallConsumption(2);
            $data['area_comum'] = '';//$this->user->config->area_comum;

            //echo "<pre>"; print_r($data); echo "</pre>"; return;

            return $this->render("index", $data);

        } else if ($this->user->inGroup('group', 'shopping')) {

            $group_id = $this->shopping_model->get_group_by_user($this->user->id);

            redirect('shopping/energy/' . $group_id, 'refresh');

        } else if ($this->user->inGroup('unity', 'shopping')) {

            $unidade_id = $this->shopping_model->get_unidade_id_by_user($this->user->id);
            $group_id = $this->shopping_model->get_group_id_by_unity($unidade_id);

            redirect('shopping/unidade/' . $group_id . '/' . $unidade_id, 'refresh');

        }
    }
}