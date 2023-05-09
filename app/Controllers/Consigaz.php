<?php

namespace App\Controllers;

use App\Models\Consigaz_model;
use App\Models\Energy_model;
use App\Models\Water_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Consigaz extends UNO_Controller
{
    protected $input;
    protected Datatables $datatables;

    /**
     * @var Consigaz_model
     */
    private Consigaz_model $consigaz_model;

    /**
     * @var Energy_model
     */
    private Energy_model $energy_model;

    /**
     * @var Water_model
     */
    private Water_model $water_model;

    public $url;

    public function __construct()
    {
        parent::__construct();

        // load requests
        $this->input = \Config\Services::request();

        // load models
        $this->consigaz_model = new Consigaz_model();
        $this->energy_model = new Energy_model();
        $this->water_model = new Water_model();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);

        // set variables
        $this->url = service('uri')->getSegment(1);

        if ($this->user->inGroup('energia'))
            $this->monitoria = 'energy';
        elseif ($this->user->inGroup('agua'))
            $this->monitoria = 'water';
        elseif ($this->user->inGroup('gas'))
            $this->monitoria = 'gas';
        elseif ($this->user->inGroup('nivel'))
            $this->monitoria = 'nivel';
    }

    public function index()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        return $this->render("index", $data);
    }

    public function unidades($entidade_id)
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
        $data['entidade_id'] = $data['entidade']->id;

        return $this->render("unidades", $data);
    }

    public function unidade($uid, $op = "")
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;
        $data['unidade'] = $this->consigaz_model->get_unidade($uid);

        $data['entidade_id'] = $data['unidade']->entidade_id;

        $data['leitura_atual'] = $this->consigaz_model->get_ultima_leitura($uid);

        return $this->render("unidade", $data);
    }

    public function fechamentos($entidade_id, $fechamento = null)
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
        $data['ramal'] = $this->consigaz_model->get_ramal($entidade_id, 'gas');
        $data['entidade_id'] = $data['entidade']->id;

        if (!is_null($fechamento)) {
            $data['fechamento'] = $this->consigaz_model->get_fechamento($fechamento);

            return $this->render("fechamento", $data);
        }

        return $this->render("fechamentos", $data);
    }

    public function relatorio($entidade_id, $relatorio_id)
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
        $data['ramal'] = $this->consigaz_model->get_ramal($entidade_id, 'gas');
        $data['entidade_id'] = $data['entidade']->id;

        $data['relatorio'] = $this->consigaz_model->get_fechamento_entrada($relatorio_id);

        $data['fechamento'] = $this->consigaz_model->get_fechamento($data['relatorio']->fechamento_id);

        $data['unidade'] = $this->consigaz_model->get_unidade_by_medidor($data['relatorio']->medidor_id);

        $data['historico'] = $this->consigaz_model->GetFechamentoHistoricoUnidade("gas", $data['relatorio']->medidor_id, $data['fechamento']->cadastro);

        return $this->render("relatorio", $data);
    }

    public function alertas($entidade_id)
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
        $data['ramal'] = $this->consigaz_model->get_ramal($entidade_id, 'gas');
        $data['entidade_id'] = $entidade_id;

        return $this->render("alertas", $data);
    }

    public function configuracoes($entidade_id)
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
        $data['ramal'] = $this->consigaz_model->get_ramal($entidade_id, 'gas');
        $data['entidade_id'] = $entidade_id;

        return $this->render("configuracoes", $data);
    }

    // Requests

    public function get_entidades()
    {
        $db = \Config\Database::connect('easy_com_br');

        $builder = $db->table('auth_user_relation');
        $builder->join('esm_entidades', 'esm_entidades.id = auth_user_relation.entidade_id');
        $builder->select('esm_entidades.id, esm_entidades.nome');
        $builder->where('auth_user_relation.user_id', $this->user->id);

        // Datatables Php Library
        $dt = new Datatables(new Codeigniter4Adapter);

        // using CI4 Builder
        $dt->query($builder);

        $dt->add("actions", function ($data) {
            return '
                <a href="#" class="text-success" data-id="' . $data['id'] . '"><i class="fas fa-eye" title="Ver"></i></a>
            ';
        });

        echo $dt->generate();
    }

    public function get_unidades()
    {
        $entidade = $this->input->getPost("entidade");

        $db = \Config\Database::connect('easy_com_br');

        $builder = $db->table('esm_medidores');
        $builder->join('esm_unidades', 'esm_unidades.id = esm_medidores.unidade_id');
        $builder->join('esm_agrupamentos', 'esm_agrupamentos.id = esm_unidades.agrupamento_id');
        $builder->join('esm_valves_stats', 'esm_valves_stats.medidor_id = esm_medidores.id');
        $builder->select('esm_medidores.id as m_id, esm_unidades.id as u_id, esm_medidores.nome AS medidor, esm_unidades.nome as unidade, esm_agrupamentos.nome as bloco, esm_valves_stats.state, esm_valves_stats.status');
        $builder->where('esm_agrupamentos.entidade_id', $entidade);

        // Datatables Php Library
        $dt = new Datatables(new Codeigniter4Adapter);

        // using CI4 Builder
        $dt->query($builder);

        $dt->add("state", function ($data) {
            $checked = null;
            if ($data['state']) {
                $checked = "checked";
            }

            $disabled = null;
            $color = null;
            if ($data['status'] === 'vermelho') {
                $color = "danger";
                $disabled = "disabled";
            } elseif ($data['status'] === 'amarelo') {
                $color = "warning";
                $disabled = "disabled";
            }

            return '<form><input type="hidden" value="' . $data['m_id'] . '" name="m_id">
                <div class="switch switch-sm switch-white ' . $disabled . ' ' . $color . '">
                    <input type="checkbox" class="switch-input" name="state" data-plugin-ios-switch ' . $checked . '>
                </div>
            </form>';
        });

        $dt->add("actions", function ($data) {
            return '
                <a class="text-primary reload-table-modal cur-pointer me-1"><i class="fas fa-rotate" title="Atualizar"></i>
                <a href="' . base_url($this->url . '/unidade/' . $data['u_id'] . '/consumo') . '" class="text-primary me-1"><i class="fas fa-eye" title="Consumo"></i></a>
                <a class="text-primary sync-leitura-modal cur-pointer" data-mid="' . $data['m_id'] . '"><i class="fas fa-gear" title="Sincronizar"></i>
            ';
        });

        echo $dt->generate();
    }

    public function edit_valve_stats()
    {
        $state = $this->input->getPost("state") ? 1 : 0;
        $medidor = $this->input->getPost("m_id");

        //TODO -> Chamar função api que aciona a válvula
        //TODO -> retornar só quando a válvula retornar status ok ou algo assim

        echo $this->consigaz_model->edit_valve_stats($medidor, $state);
    }

    public function edit_valve_leitura()
    {
        $leitura = $this->input->getPost("leitura");
        $medidor = $this->input->getPost("mid");

        echo $this->consigaz_model->edit_valve_leitura($medidor, $leitura);
    }

    public function get_alertas()
    {
        $entidade_id = $this->input->getPost("entidade");

        $user_id = auth()->user()->id;

        $dt = $this->datatables->query("
            SELECT 
                esm_alertas.tipo, 
                esm_medidores.nome as medidor,
                esm_unidades.nome as unidade, 
                esm_alertas.titulo, 
                esm_alertas.enviada, 
                0 as actions, 
                IF(ISNULL(esm_alertas_envios.lida), 'unread', '') as DT_RowClass,
                esm_alertas_envios.id AS DT_RowId
            FROM esm_alertas_envios 
            JOIN esm_alertas ON esm_alertas.id = esm_alertas_envios.alerta_id 
            JOIN esm_medidores ON esm_medidores.id = esm_alertas.medidor_id
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id AND esm_agrupamentos.entidade_id = $entidade_id
            WHERE
                esm_alertas_envios.user_id = $user_id AND 
                esm_alertas.visibility = 'normal' AND 
                esm_alertas_envios.visibility = 'normal' AND
                esm_alertas.enviada IS NOT NULL
            ORDER BY esm_alertas.enviada DESC
        ");

        $dt->edit('tipo', function ($data) {
            return alerta_tipo2icon($data['tipo']);
        });

        // formata data envio
        $dt->edit('enviada', function ($data) {
            return time_ago($data['enviada']);
        });

        $dt->edit('actions', function ($data) {
            $show = '';
            if ($data['DT_RowClass'] == 'unread') $show = ' d-none';
            return '<a href="#" class="text-danger action-delete' . $show . '" data-id="' . $data['DT_RowId'] . '"><i class="fas fa-trash" title="Excluir alerta"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function show_alert()
    {
        // pega o id do post
        $id = $this->input->getPost('id');

        // busca o alerta
        $data['alerta'] = $this->consigaz_model->get_user_alert($id, true);

        $data['alerta']->enviada = time_ago($data['alerta']->enviada);

        // verifica e informa erros
        if (!$data['alerta']) {
            return view('modals/erro', array('message' => 'Alerta não encontrado!'));
        }

        // carrega a modal
        return view('modals/alert', $data);
    }

    public function delete_alert()
    {
        echo $this->consigaz_model->delete_alert($this->input->getPost('id'));
    }

    public function read_all_alert()
    {
        echo $this->consigaz_model->read_all_alert(auth()->user()->id);
    }
}