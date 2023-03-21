<?php

namespace App\Controllers;

use App\Models\Shopping_model;
use App\Models\Energy_model;
use App\Models\Water_model;

class Shopping extends UNO_Controller
{
    /**
     * @var Shopping_model
     */
    private Shopping_model $shopping_model;

    /**
     * @var Energy_model
     */
    private Energy_model $energy_model;

    /**
     * @var Water_model
     */
    private Water_model $water_model;

    public function __construct()
    {
        parent::__construct();

        $this->shopping_model = new Shopping_model();
        $this->energy_model = new Energy_model();
        $this->water_model = new Water_model();
    }

    public function index()
    {
        $this->setHistory('Acesso à página inicial', 'acesso');

        $data['user'] = $this->user;

        if ($this->user->inGroup('shopping', 'admin')) {

            $data['entity_id'] = $this->user->type->entity_id;
            $data['groups'] = $this->shopping_model->get_groups_by_entity($this->user->type->entity_id);
            $data['area_comum'] = 'Área Comum';//$this->user->config->area_comum;

            foreach ($data['groups'] as $grp) {
                $data['overall_c'][] = $this->energy_model->GetOverallConsumption(1, $grp->bloco_id);
                $data['overall_l'][] = $this->energy_model->GetOverallConsumption(2, $grp->bloco_id);
            }

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

    public function energy($group_id = null)
    {
        $data['permission'] = $this->shopping_model->get_user_permission($this->user->id);

        $data['user']    = $this->user;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['area_comum'] = "Área Comum";

        $data['unidades'] = $this->shopping_model->get_units($group_id, "energia");
        $data['device_groups'] = $this->shopping_model->get_device_groups(72);

        //echo "<pre>"; print_r($data); echo "</pre>";

        return $this->render('energy', $data);
    }

    public function water($group_id)
    {
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['user']    = $this->user;

        $data['unidades'] = $this->shopping_model->get_units($group_id, "agua");
        $data['device_groups'] = $this->shopping_model->get_device_groups(73);

        $data['area_comum'] = "Área Comum";

        return $this->render('water', $data);
    }

    public function faturamentos($group_id)
    {
        $data['group_id']   = $group_id;
        $data['group']      = $this->shopping_model->get_group_info($group_id);
        $data['unidades']   = $this->shopping_model->get_unidades($group_id);
        $data['area_comum'] = "Área Comum";

        return $this->render('faturamentos', $data);
    }

    public function lancamento($type, $group_id, $id)
    {
        if ($type == "energia") {

            $data['group_id']   = $group_id;
            $data['group']      = $this->shopping_model->get_group_info($group_id);
            $data['fechamento'] = $this->energy_model->GetLancamento($id);
            $data['area_comum'] = "Área Comum";

            return $this->render('lancamento_energy', $data);

        } else if ($type == "agua") {

            $data['group_id']   = $group_id;
            $data['group']      = $this->shopping_model->get_group_info($group_id);
            $data['fechamento'] = $this->water_model->GetLancamento($id);
            $data['area_comum'] = "Área Comum";

            return $this->render('lancamento_water', $data);
        }
    }

    public function relatorio($type, $group_id, $fechamento_id, $relatorio_id)
    {
        $data['group_id']   = $group_id;
        $data['shopping']   = $this->shopping_model->GetGroup($group_id);

        if ($type == "energia") {

            $data['unidade']    = $this->shopping_model->GetFechamentoUnidade("energia", $relatorio_id);
            $data['fechamento'] = $this->shopping_model->GetFechamento("energia", $fechamento_id);
            $data['historico']  = $this->shopping_model->GetFechamentoHistoricoUnidade("energia", $data['unidade']->device, $data['fechamento']->cadastro);

            return $this->render('relatorio_energy', $data);

        } else if ($type == "agua") {

            $data['unidade']    = $this->shopping_model->GetFechamentoUnidade("agua", $relatorio_id);
            $data['fechamento'] = $this->shopping_model->GetFechamento("agua", $fechamento_id);
            $data['historico']  = $this->shopping_model->GetFechamentoHistoricoUnidade("agua", $data['unidade']->device, $data['fechamento']->cadastro);

            $data['equivalencia'][0] = floor($data['unidade']->consumo / 10000);
            $resto = $data['equivalencia'][0] * 10000;
            $data['equivalencia'][1] = floor(($data['unidade']->consumo - $resto) / 1000);
            $resto += $data['equivalencia'][1] * 1000;
            $data['equivalencia'][2] = floor(($data['unidade']->consumo - $resto) / 100);
            $resto += $data['equivalencia'][2] * 100;
            $data['equivalencia'][3] = floor(($data['unidade']->consumo - $resto) / 10);

            return $this->render('relatorio_water', $data);
        }
    }
}