<?php

namespace App\Controllers;

use App\Models\Admin_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;
use Viacep;

class Admin extends UNO_Controller
{
    private Admin_model $admin_model;

    protected $input;

    protected Datatables $datatables;

    protected Viacep $viacep;

    public function __construct()
    {
        parent::__construct();

        // load requests
        $this->input = \Config\Services::request();

        // load models
        $this->admin_model = new Admin_model();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);
        $this->viacep = new Viacep();
    }

    public function index(): string
    {
        return $this->render("index");
    }

    public function entities($param1 = null, $param2 = null)
    {
        if ( intval($param1) > 0 ) {
            if ($param2 == "editar") {
                return $this->render('entity_edit');
            } else {
                return $this->render('entity_view');
            }
        } elseif ($param1 === 'incluir') {
            return $this->render('entity_add');
        }

        return $this->render('entities');
    }

    public function entitiess($param1 = null, $param2 = null): string
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

            return $this->render('entity_add');
        } else {

            return $this->render('entities');
        }
    }

    public function users()
    {
        return $this->render("users");
    }



    // POST PART FILE

    public function get_entities()
    {
        // processa filtros externos ao DT
        $inactives = " AND esm_entidades.status = 'ativo'";
        if (filter_var($this->input->getGet('inactives'), FILTER_VALIDATE_BOOLEAN))
            $inactives = "";

        $mode = "";
        if ($this->input->getGet('mode') == 1) $mode = " AND esm_entidades.m_agua = 1";
        if ($this->input->getGet('mode') == 2) $mode = " AND esm_entidades.m_gas > 0";
        if ($this->input->getGet('mode') == 3) $mode = " AND esm_entidades.m_energia = 1";
        if ($this->input->getGet('mode') == 4) $mode = " AND esm_entidades.m_nivel = 1";

        // realiza a query via dt
        $dt = $this->datatables->query(
            "SELECT
                esm_entidades.id AS id,
                esm_entidades.nome AS nome,
                esm_entidades.tipo AS tipo,
	            esm_entidades.classificacao AS classificacao,
                esm_entidades.m_agua AS agua,
                esm_entidades.m_gas AS gas,
                esm_entidades.m_energia AS energia,
                esm_entidades.m_nivel AS nivel,
                esm_entidades.cidade AS cidade,
                esm_entidades.uf AS uf,
                esm_administradoras.nome AS nome_adm,
                esm_pessoas.nome AS nome_gestor,
                esm_entidades.STATUS AS status 
            FROM
                esm_entidades
                LEFT JOIN esm_pessoas ON esm_entidades.gestor_id = esm_pessoas.id
                LEFT JOIN esm_administradoras ON esm_entidades.admin_id = esm_administradoras.id 
            WHERE
                esm_entidades.visibility = 'normal'" . $inactives . $mode);
        // inclui monitoramento
        $dt->add('monitoramento', function ($data) {
            $ret = '';
            if ($data['agua'] == 1) $ret .= '<i class="fas fa-tint text-primary" title="Água"></i> ';
            if ($data['gas'] == 1) $ret .= '<i class="fas fa-fire text-success" title="Gás"></i> ';
            if ($data['gas'] == 2) $ret .= '<i class="fas fa-fire text-success" title="Gás Mensal"></i> ';
            if ($data['energia'] == 1) $ret .= '<i class="fas fa-bolt text-warning" title="Energia Elétrica"></i>';
            if ($data['nivel'] == 1) $ret .= '<i class="fas fa-ruler-vertical text-info" title="Nível de Reservatório"></i>';

            return $ret;
        });
        // configura campo cidade
        $dt->edit('cidade', function ($data) {
            if (is_null($data['cidade']) || is_null($data['uf']))
                return '';

            return $data['cidade'] . '/' . $data['uf'];
        });
        // configura campo tipo
        $dt->edit('tipo', function ($data) {
            return '<span class="badge badge-info">' . ucfirst($data['tipo']) . '</span>';
        });
        $dt->edit('classificacao', function ($data) {
            return '<span class="badge badge-primary">' . ucfirst($data['classificacao']) . '</span>';
        });
        // inclui actions
        $dt->add('action', function ($data) {
            return '<a href="' . site_url('admin/entities/') . $data['id'] . '/editar" class="action-edit" data-id="' . $data['id'] . '"><i class="fas fa-pencil-alt" title="Editar"></i></a>
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

    public function delete_entity()
    {
        $id = $this->input->getPost('id');

        echo $this->admin_model->delete_entity($id);
    }

    public function get_admnistadoras()
    {
        $q = $this->input->getGet('q');
        //realiza consulta
        $p = $this->admin_model->get_administradoras($q);

        // retorna dados formatados
        echo '{ "results" : ' . json_encode($p) . '}';
    }

    public function get_gestor()
    {
        $q = $this->input->getGet('q');
        // realiza consulta
        $p = $this->admin_model->get_pessoas($q);

        // retorna dados formatados
        echo '{ "results" : ' . json_encode($p) . '}';
    }

    public function add_adm()
    {
        $dados['nome']          = $this->input->getPost('nome-adm') ?? '';
        $dados['cnpj']          = $this->input->getPost('cnpj-adm') ?? '';
        $dados['contato']       = $this->input->getPost('contato-adm') ?? '';
        $dados['email']         = $this->input->getPost('email-adm') ?? '';
        $dados['site']          = $this->input->getPost('site-adm') ?? '';
        $dados['cep']           = $this->input->getPost('cep-adm') ?? '';
        $dados['logradouro']    = $this->input->getPost('logradouro-adm') ?? '';
        $dados['numero']        = $this->input->getPost('numero-adm') ?? '';
        $dados['complemento']   = $this->input->getPost('complemento-adm') ?? '';
        $dados['bairro']        = $this->input->getPost('bairro-adm') ?? '';
        $dados['cidade']        = $this->input->getPost('cidade-adm') ?? '';
        $dados['tel_1']         = $this->input->getPost('telefone1-adm') ?? '';
        $dados['tel_2']         = $this->input->getPost('telefone2-adm') ?? '';

        echo $this->admin_model->add_adm($dados);
    }

    public function add_gestor()
    {
        $dados['nome']          = $this->input->getPost('nome-gestor') ?? '';
        $dados['cpf']           = $this->input->getPost('cpf-gestor') ?? '';
        $dados['aniversario']   = $this->input->getPost('nasc-gestor') ?? '';
        $dados['email']         = $this->input->getPost('email-gestor') ?? '';
        $dados['tipo']          = $this->input->getPost('tipo-gestor') ?? '';
        $dados['site']          = $this->input->getPost('site-gestor') ?? '';
        $dados['tel_1']         = $this->input->getPost('telefone1-gestor') ?? '';
        $dados['tel_2']         = $this->input->getPost('telefone2-gestor') ?? '';
        $dados['tel_3']         = $this->input->getPost('celular1-gestor') ?? '';
        $dados['tel_4']         = $this->input->getPost('celular2-gestor') ?? '';

        echo $this->admin_model->add_gestor($dados);
    }

    public function add_entity()
    {
        $dados['classificacao']  = $this->input->getPost('classificacao-entity') ?? '';
        $dados['nome']           = $this->input->getPost('nome-entity') ?? '';
        $dados['cnpj']           = $this->input->getPost('cnpj-entity') ?? '';
        $dados['tipo']           = $this->input->getPost('tipo-entity') ?? '';
        $dados['cep']            = $this->input->getPost('cep-entity') ?? '';
        $dados['logradouro']     = $this->input->getPost('logradouro-entity') ?? '';
        $dados['numero']         = $this->input->getPost('numero-entity') ?? '';
        $dados['complemento']    = $this->input->getPost('complemento-entity') ?? '';
        $dados['bairro']         = $this->input->getPost('bairro-entity') ?? '';
        $dados['cidade']         = $this->input->getPost('cidade-entity') ?? '';
        $dados['uf']             = $this->input->getPost('estado-entity') ?? '';
        $dados['inicio']         = $this->input->getPost('inicio-entity') ?? '';
        $dados['fim']            = $this->input->getPost('fim-entity') ?? '';
        $dados['m_agua']         = $this->input->getPost('agua-entity') === 'on' ? 1 : 0;
        $dados['m_gas']          = $this->input->getPost('gas-entity') === 'on' ? 1 : 0;
        $dados['m_energia']      = $this->input->getPost('energia-entity') === 'on' ? 1 : 0;
        $dados['m_nivel']        = $this->input->getPost('nivel-entity') === 'on' ? 1 : 0;
        $dados['fracao_ideal']   = $this->input->getPost('fracao-entity') === 'on' ? 1 : 0;
        $dados['status']         = $this->input->getPost('switch') === 'on' ? 'ativo' : 'inativo';
        $dados['admin_id']       = $this->input->getPost('select-adm') ?? '';
        $dados['gestor_id']      = $this->input->getPost('select-gestor') ?? '';

        echo $this->admin_model->add_entity($dados);
    }

    public function busca_endereco()
    {
        $cep = $this->input->getPost('cep');

        // executa o curl e retorna os dados
        echo $this->viacep->busca_cep($cep);
    }
}