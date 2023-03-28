<?php

namespace App\Controllers;

use App\Models\Admin_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Admin extends UNO_Controller
{
    private Admin_model $admin_model;

    protected $input;

    protected Datatables $datatables;

    public function __construct()
    {
        parent::__construct();

        // load requests
        $this->input = \Config\Services::request();

        // load models
        $this->admin_model = new Admin_model();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);
    }

    public function index(): string
    {
        $data['centrais'] = $this->admin_model->get_all_centrais();
        return $this->render("index", $data);
    }

    public function entities($param1 = null, $param2 = null): string
    {
        if ( intval($param1) > 0 ) {

            $data['condo'] = $this->admin_model->get_condo($param1);
            $data['blocos'] = $this->admin_model->get_blocos($data['condo']->id);

            if ($param2 == "editar") {

                $data['ramais'] = $this->admin_model->get_ramais($param1, true);
                $data['centrais'] = $this->admin_model->get_centrais($param1, true);
                $data['entradas'] = $this->admin_model->get_entradas($param1);
                $data['readonly'] = '';
                $data['title'] = 'Editar Condomínio';
                $data['geral'] = false;
                $labels = array();
                for($i = 0; $i < 40; $i++) {
                    $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
                }
                $data['leituras'] = $labels;

                return $this->render('entity_edit', $data);
            } else {

                $data['readonly'] = 'readonly';
                $data['title'] = 'Visualizar Condomínio';
                $data['ramais'] = $this->admin_model->get_ramais($param1, true);
                $data['centrais'] = $this->admin_model->get_centrais($param1, true);
                $data['entradas'] = $this->admin_model->get_entradas($param1);
                $data['geral'] = false;

                foreach ($data['entradas'] as $e) {
                    if ($e->entrada == "Geral") {
                        $data['geral'] = true;
                        break;
                    }
                }

                if ($data['geral']) {
                    $data['unidade_geral'] = $this->painel_model->get_unidade_medidor_principal($param1);
                    $data['central_geral'] = $this->painel_model->get_central_by_unidade($data['unidade_geral'])->central;
                    $data['primeira_leitura'] = date("d/m/Y", $this->painel_model->get_primeira_leitura($data['condo']->tabela, 'agua', $data['unidade_geral']));

                }

                $labels = array();
                for($i = 0; $i < 40; $i++) {
                    $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
                }
                $data['leituras'] = $labels;

                return $this->render('entity_edit', $data);
            }

        } else if ($param1 == "incluir") {

            $data['readonly'] = 'readonly';

            return $this->render('entity_add', $data);

        } else {
            return $this->render('entities');
        }
    }

    public function users()
    {
        return $this->render("users");
    }



    // POST PART FILE
    public function get_condos()
    {
        // processa filtros externos ao DT
        $inactives = " AND esm_condominios.status = 'ativo'";
        if (filter_var($this->input->getGet('inactives'), FILTER_VALIDATE_BOOLEAN))
            $inactives = "";

        $mode = "";
        if ($this->input->getGet('mode') == 1) $mode = " AND esm_condominios.m_agua = 1";
        if ($this->input->getGet('mode') == 2) $mode = " AND esm_condominios.m_gas > 0";

        // realiza a query via dt
        $dt = $this->datatables->query(
            "
			SELECT esm_condominios.id AS id, esm_condominios.nome AS nome, esm_condominios.tipo AS tipo, esm_condominios.m_agua AS agua, esm_condominios.m_gas AS gas,
            esm_condominios.m_energia AS energia, esm_condominios.cidade AS cidade, esm_condominios.uf AS uf, esm_administradoras.nome AS nome_adm, 
            esm_pessoas.nome AS nome_sindico, esm_condominios.inicio AS inicio, esm_condominios.fim AS fim, esm_condominios.status AS status
			FROM esm_condominios
			LEFT JOIN esm_pessoas ON esm_condominios.sindico_id = esm_pessoas.id
			LEFT JOIN esm_administradoras ON esm_condominios.admin_id = esm_administradoras.id
			WHERE esm_condominios.visibility = 'normal'" . $inactives . $mode
        );
        // inclui monitoramento
        $dt->add('monitoramento', function ($data) {
            $ret = '';
            if ($data['agua'] == 1) $ret .= '<i class="fas fa-tint color-agua" title="Água"></i> ';
            if ($data['gas'] == 1) $ret .= '<i class="fas fa-fire color-gas" title="Gás"></i> ';
            if ($data['gas'] == 2) $ret .= '<i class="fas fa-fire color-gas" title="Gás Mensal"></i> ';
            if ($data['energia'] == 1) $ret .= '<i class="fas fa-bolt color-energia" title="Energia Elétrica"></i>';

            return $ret;
        });
        // configura campo cidade
        $dt->edit('cidade', function ($data) {
            return $data['cidade'] . '/' . $data['uf'];
        });
        // configura campo tipo
        $dt->edit('tipo', function ($data) {
            return '<span class="badge badge-info">' . ucfirst($data['tipo']) . '</span>';
        });
        // inclui actions
        $dt->add('action', function ($data) {
            return '<a href="' . site_url('admin/condominios/') . $data['id'] . '/editar" class="action-edit" data-id="' . $data['id'] . '"><i class="fas fa-pencil-alt" title="Editar"></i></a>
				<a href="#" class="action-delete" data-id="' . $data['id'] . '" data-toggle="confirmation" data-title="Certeza?"><i class="fas fa-trash" title="Excluir"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function get_unidades_bloco()
    {
        $bloco = intval($this->input->getGet('bloco'));

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.id, 
                esm_unidades.nome AS apto, 
                esm_unidades.andar, 
                esm_unidades.fracao, 
                esm_unidades.codigo, 
                esm_unidades.tipo,
                esm_blocos.nome AS bloco, 
                user.id AS prop_id, 
                IFNULL(user.nome, 'Não cadastrado') AS nome, 
                IFNULL(user.username, '-') AS email, 
                IFNULL(user.telefone, '-') AS telefone
            FROM esm_unidades
            JOIN esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
            LEFT JOIN (
                SELECT auth_users_unidades.unidade_id, auth_users.id, auth_users.nome, auth_users.username, auth_users.telefone
                FROM auth_users
                JOIN auth_users_groups ON auth_users_groups.user_id = auth_users.id
                JOIN auth_users_unidades ON auth_users_unidades.user_id = auth_users.id
                WHERE auth_users_groups.group_id = 3) AS user ON user.unidade_id = esm_unidades.id
            WHERE esm_unidades.bloco_id = $bloco
            ORDER BY esm_blocos.nome, esm_unidades.nome
        ");

        // inclui campo medidores
        $dt->add('medidores', function ($data) {
            $entradas = $this->admin_model->get_medidores_unidade($data['id']);
            $medidores = '';
            foreach ($entradas as $e) {
                if ($e->tipo == 'agua') {
                    if (is_null($e->central))
                        $medidores .= '<span class="badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '<br/>Não monitorado">&nbsp;</span>';
                    else {
                        $s = is_null($e->sensor_id) ? '' : '<b>Sensor:</b> ' . $e->sensor_id;
                        $medidores .= '<span class="badge badge-' . ($e->posicao == 0 ? 'secondary' : $e->tipo) . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '<br/><b>Central</b>: ' . $e->central . '<br/><b>Posição:</b> ' . $e->posicao . '<br/>' . $s . '">' . $e->nome . '</span>';
                    }
                } else if ($e->tipo == 'gas') {
                    if (is_null($e->central))
                        $medidores .= '<span class="badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '">' . $e->nome . '</span>';
                    else {
                        $s = is_null($e->sensor_id) ? '' : '<b>Sensor:</b> ' . $e->sensor_id;
                        $medidores .= '<span class="badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '<br/><b>Central</b>: ' . $e->central . '<br/><b>Posição:</b> ' . $e->posicao . '<br/>' . $s . '">' . $e->nome . '</span>';
                    }
                }
            }

            return $medidores;
        });

        // inclui campo status
        $dt->add('action', function ($data) {
            return '<div class="dropdown"><a class="" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"><i class="fas fa-bars" title="Ações"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" data-id="' . $data['id'] . '" href="' . site_url('admin/unidades/' . $data['id']) . '" target="_blank"><i class="fas fa-eye mr-2"></i> Consumo</a>
                        <a class="dropdown-item action-edit" data-id="' . $data['id'] . '" href="#"><i class="fas fa-pencil-alt mr-2"></i> Editar</a>
                        <a class="dropdown-item action-delete" data-id="' . $data['id'] . '" href="#"><i class="fas fa-trash mr-2"></i> Excluir</a>
                    </div></div>';
        });

        // gera resultados
        echo $dt->generate();
    }
}