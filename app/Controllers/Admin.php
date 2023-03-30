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

    public function entities($param1 = null, $param2 = null): string
    {
        if ( intval($param1) > 0 ) {

            $data['geral'] = false;
            $data['entity'] = $this->admin_model->get_entity($param1);
            $data['groups'] = $this->admin_model->get_groups($data['entity']->id);

            if ($param2 == "editar") {

                $data['ramais'] = $this->admin_model->get_ramais($param1, true);
                $data['centrais'] = $this->admin_model->get_centrais($param1, true);
                $data['entradas'] = $this->admin_model->get_entradas($param1);
                $data['readonly'] = '';

                $labels = array();
                for($i = 0; $i < 40; $i++) {
                    $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
                }
                $data['leituras'] = $labels;

                return $this->render('entity_edit', $data);

            } else {

                $data['readonly'] = 'readonly disabled';

                $labels = array();
                for($i = 0; $i < 40; $i++) {
                    $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
                }
                $data['leituras'] = $labels;

                return $this->render('entity_edit', $data);
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
            $data['blocos'] = $this->admin_model->get_groups($data['condo']->id);

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
                    IFNULL(user.username, 'Não cadastrado') AS nome
            FROM esm_unidades
            JOIN esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
            LEFT JOIN (
                    SELECT auth_user_relation.unity_id, users.id, users.username
                    FROM users
                    JOIN auth_user_relation ON auth_user_relation.user_id = users.id 
            ) AS user ON user.unity_id = esm_unidades.id
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
                } else if ($e->tipo == 'energia') {
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
            return '
                <div class="dropdown">
                    <a class="" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars" title="Ações"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" data-id="' . $data['id'] . '" href="' . site_url('admin/unidades/' . $data['id']) . '" target="_blank"><i class="fas fa-eye mr-2"></i> Consumo</a>
                        <a class="dropdown-item action-edit" data-id="' . $data['id'] . '" href="#"><i class="fas fa-pencil-alt mr-2"></i> Editar</a>
                        <a class="dropdown-item action-delete" data-id="' . $data['id'] . '" href="#"><i class="fas fa-trash mr-2"></i> Excluir</a>
                    </div>
                </div>';
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

    public function get_fechamentos()
    {
        $condo_id = $this->input->getGet('condo');
        $this->user = auth()->user();
        if (is_null($condo_id)) $condo_id = $this->user->condo->id;
        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT esm_fechamentos.id AS DT_RowId, esm_fechamentos.competencia,
			DATE_FORMAT(FROM_UNIXTIME(esm_fechamentos.data_inicio),'%d/%m/%Y') AS data_inicio,
			DATE_FORMAT(FROM_UNIXTIME(esm_fechamentos.data_fim),'%d/%m/%Y') AS data_fim, 
            LPAD(esm_fechamentos.leitura_anterior, 6, '0') AS leitura_anterior, LPAD(esm_fechamentos.leitura_atual, 6, '0') AS leitura_atual, 
            CONCAT(esm_fechamentos.leitura_atual - esm_fechamentos.leitura_anterior, ' m<sup>3</sup>') AS consumo,
            CONCAT('<span class=\"float-left\">R$</span> ', FORMAT(esm_fechamentos.v_concessionaria, 2, 'de_DE')) AS v_concessionaria, 
            DATE_FORMAT(esm_fechamentos.cadastro,'%d/%m/%Y') AS cadastro, esm_ramais.nome AS ramal,
            (SELECT IFNULL(GROUP_CONCAT(DATE_FORMAT(data, '%d/%m/%Y') SEPARATOR '<br/>'), 'Não Enviados') FROM esm_fechamentos_envios WHERE fechamento_id = esm_fechamentos.id) AS envios
            FROM esm_fechamentos
			LEFT JOIN esm_ramais ON esm_fechamentos.ramal_id = esm_ramais.id
            LEFT JOIN esm_entidades ON esm_ramais.condo_id = esm_entidades.id
            WHERE esm_entidades.id = $condo_id AND esm_ramais.tipo = 'agua' ORDER BY esm_fechamentos.id DESC
        ");

        $dt->edit('envios', function ($data) {
            if ($data['envios'] == 'Não Enviados')
                return '<span class="badge badge-warning">Não Enviados</span>';
            else
                return '<span class="badge badge-success" title="' . $data['envios'] . '" data-toggle="tooltip" data-html="true">Enviados</span>';
        });

        $dt->edit('competencia', function ($data) {
            return competencia_nice($data['competencia']);
        });

        // inclui actions
        $dt->add('action', function ($data) {
            $dis = "";
            if ($this->user->inGroup('demo')) {
                $dis = " disabled";
            }

            return '<a href="#" class="action-download-agua ' . $dis . '" data-id="' . $data['DT_RowId'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
				<a href="#" class="action-delete ' . $dis . '" data-id="' . $data['DT_RowId'] . '"><i class="fas fa-trash" title="Excluir"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function get_leituras()
    {
        $condo_id = $this->input->getGet('condo');
        $this->user = auth()->user();
        if (is_null($condo_id)) $condo_id = $this->user->condo->id;

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                UNIX_TIMESTAMP(STR_TO_DATE(CONCAT('01/', competencia), '%d/%m/%Y')) AS competencia,
                esm_fechamentos.data_inicio,
                esm_fechamentos.data_fim, 
                esm_fechamentos.leitura_atual - esm_fechamentos.leitura_anterior AS consumo,
                esm_fechamentos.cadastro AS leitura,
                esm_fechamentos.id AS DT_RowId
            FROM esm_fechamentos
            LEFT JOIN esm_ramais ON esm_fechamentos.ramal_id = esm_ramais.id
            LEFT JOIN esm_entidades ON esm_ramais.condo_id = esm_entidades.id
            WHERE esm_entidades.id = $condo_id AND esm_ramais.nome LIKE \"G%\" ORDER BY esm_fechamentos.id DESC
        ");

        $dt->edit('competencia', function ($data) {
            return competencia_nice(date("m/Y", $data['competencia']));
        });

        $dt->edit('data_inicio', function ($data) {
            return date("d/m/Y", $data['data_inicio']);
        });

        $dt->edit('data_fim', function ($data) {
            return date("d/m/Y", $data['data_fim']);
        });

        $dt->edit('leitura', function ($data) {
            return date_format(date_create($data['leitura']), "d/m/Y");
        });

        $dt->edit('consumo', function ($data) {
            return number_format($data['consumo'] / 1000, 3, ',', '.') . ' m<sup>3</sup>';
        });

        // inclui actions
        $dt->add('action', function ($data) {
            return '<a href="#" class="action-download-gas" data-id="' . $data['DT_RowId'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function md_bloco()
    {
        $bid = $this->input->getPost('bid');
        $cid = $this->input->getPost('cid');

        if ($bid > 0) {
            $data['modal_title'] = "Editar Bloco";
        } else {
            $data['modal_title'] = "Incluir Bloco";
        }
        $data['bloco'] = $this->admin_model->get_bloco($bid);
        $data['ramais'] = $this->admin_model->get_ramais($cid);

        echo view('Admin/modals/bloco', $data);
    }

    public function md_unidade()
    {
        $cid = $this->input->getPost('cid');
        $bloco = $this->input->getPost('bid');
        $uid = $this->input->getPost('uid');
        $modo = $this->input->getPost('md');

        // dados de config do condominio pelo bloco para ter o nome do bloco
        $data['entity'] = $this->admin_model->get_entity_config_by_bloco($bloco, ', esm_entidades.d_proprietarios, esm_entidades.tipo_unidades, esm_blocos.nome as b_nome');
        // entradas do condominio
        $data['entradas'] = $this->admin_model->get_entradas($cid);
        // centrais do condominio
        $data['centrais'] = $this->admin_model->get_centrais($cid);
        // fraçoes já cadastradas no condominio
        $data['fracoes'] = $this->admin_model->get_fracoes_condominio($cid);
        // modo
        $data['modo'] = $modo;
        // edição ou inclusão?
        if ($uid > 0) {
            // dados da unidade e medidores
            $data['unidade'] = $this->admin_model->get_unidade($uid);
            $entradas = $this->admin_model->get_medidores_unidade($uid);
            if (count($entradas) > 0) $data['entradas'] = $entradas;

            if ($modo) {
                $data['modal_title'] = 'Visualizar Unidade';
                echo view('Admin/modals/view_unidade', $data);
            } else {
                $data['modal_title'] = 'Editar Unidade';
                echo view('Admin/modals/unidade_edit', $data);
            }
            return;
        }

        // titulo da modal
        $data['modal_title'] = 'Incluir Unidade - Bloco ' . $data['entity']->b_nome;
        // renderiza modal
        echo view('Admin/modals/unidade_add', $data);
    }

    public function get_portas()
    {
        $out = '';
        $central = $this->input->getPost('id', true);
        $porta = $this->input->getPost('porta', true);

        $portas = $this->admin_model->get_portas($central);
        if (!$portas) {
            for ($i = 1; $i < 65; $i++) $out .= '<option value="' . $i . '">' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>';
            echo $out;
            return;
        }

        $portas = array_column($portas, 'posicao');

        //TODO: verificar numero de portas da central,
        // pois qdo tiver sensores sem fio vão ser mais
        for ($i = 1; $i < 65; $i++) {
            $status = in_array($i, $portas) ? 'disabled' : '';
            $sel = ($porta == $i) ? 'selected' : '';
            $out .= '<option value="' . $i . '" ' . $status . ' ' . $sel . '>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>';
        }

        echo $out;
    }

    public function edit_unidade()
    {
        $id = $this->input->getPost('id') ?? "";
        $cid = $this->input->getPost('cid') ?? "";
        $bid = $this->input->getPost('bid') ?? "";

        $nome = $this->input->getPost('nome-unidade') ?? "";
        $andar = $this->input->getPost('andar-unidade') ?? "";
        $propr = $this->input->getPost('proprietario-unidade') ?? "";
        $email = $this->input->getPost('email-unidade') ?? "";
        $telef = $this->input->getPost('telefone-unidade') ?? "";
        $fracao = $this->input->getPost('fracao-unidade') ?? "";
        $tipo = $this->input->getPost('tipo-unidade') ?? "";

        // $agua = $this->input->post('tipo-unidade', true);
        // $ramal_id = $this->input->post('tipo-unidade', true);

        if ($id) {
            //edita unidade
            $agua_edit = $this->input->getPost('id-prumada-edit') ?? "";
            $agua_delete = $this->input->getPost('id-prumada-delete') ?? "";

            // atualiza bloco na tabela blocos
            echo $this->admin_model->update_bloco($id, $nome, $agua, $agua_edit, $agua_delete, $ramal_id);
        } else {
            $unidade = array(
                'bloco_id' => $bid,
                'nome' => $nome,
                'andar' => $andar,
                'fracao' => is_null($fracao) ? 1 : $fracao,
                'tipo' => $tipo
            );
            $dados = array(
                'nome' => $propr,
                'email' => $email,
                'telefone' => preg_replace('/[^0-9]/', '', $telef)
            );

            // insere bloco
            echo $this->admin_model->add_unidade($unidade, $dados);
        }
    }

    public function md_medidor()
    {
        $id = $this->input->getPost('id');

        $data['medidor'] = $this->admin_model->get_medidor($id);

        echo view('Admin/modals/medidor', $data);
    }
}