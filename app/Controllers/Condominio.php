<?php

namespace App\Controllers;

use App\Models\Condominio_model;
use App\Models\Energy_model;
use App\Models\Water_model;
use App\Models\Gas_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Condominio extends UNO_Controller
{
    protected $input;
    protected $email;

    /**
     * @var Datatables
     */
    protected Datatables $datatables;

    /**
     * @var Condominio_model
     */
    private Condominio_model $condominio_model;

    /**
     * @var Energy_model
     */
    private Energy_model $energy_model;

    /**
     * @var Water_model
     */
    private Water_model $water_model;

    /**
     * @var Water_model
     */
    private Gas_model $gas_model;

    public $url;
    public $user;

    public function __construct()
    {
        parent::__construct();

        // load services
        $this->input = \Config\Services::request();
        $this->email = \Config\Services::email();

        // load models
        $this->condominio_model = new Condominio_model();
        $this->energy_model = new Energy_model();
        $this->water_model = new Water_model();
        $this->gas_model = new Gas_model();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);

        // set variables
        $this->url = service('uri')->getSegment(1);

        $this->user->unidade = (object)[];
        $this->user->entity = (object)[];

        if ($this->user->inGroup('superadmin')) {
            $this->user->entity->classificacao = $this->user->page;
        } else if ($this->user->inGroup('admin', 'unity')) {
            $this->user->entity = $this->condominio_model->get_entidade_by_user($this->user->id);
            $this->user->unidade = $this->condominio_model->get_unidade_by_user($this->user->id);
        } else if ($this->user->inGroup('admin')) {
            $this->user->entity = $this->condominio_model->get_entidade_by_user($this->user->id);
        } else if ($this->user->inGroup('unity')) {
            $this->user->unidade = $this->condominio_model->get_unidade_by_user($this->user->id);
        }
        if ($this->user->inGroup('administradora')) {
            $this->user->entitys = $this->condominio_model->get_admin_condos($this->user->id);
            $this->active = true;
        }

        if ($this->user->inGroup("energia"))
            $this->monitoria = 'energy';
        elseif ($this->user->inGroup("agua"))
            $this->monitoria = 'water';
        elseif ($this->user->inGroup("gas"))
            $this->monitoria = 'gas';
        elseif ($this->user->inGroup("nivel"))
            $this->monitoria = 'nivel';
    }

    public function index()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;

        // busca aviso se ultimo aviso recebido não estiver lido
        $data['aviso'] = $this->condominio_model->get_last_aviso($this->user->id);

        if ($this->user->inGroup('unity', 'admin')) {
            // leitura atual do monitamento da unidade
            $data['leitura_agua'] = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'agua', $this->user->unidade->id);
            $data['leitura_gas'] = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'gas', $this->user->unidade->id);
            $data['leitura_energia'] = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'energia', $this->user->unidade->id);

            // busca resumo do consumo da água
            $data['hora_agua'] = $this->condominio_model->get_consumo_ultima_hora($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['hoje_agua'] = $this->condominio_model->get_consumo_hoje($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['last_agua'] = $this->condominio_model->get_consumo_last_24($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['fatu_agua'] = $this->condominio_model->get_consumo_last_fechamento($this->user->unidade->id, $this->user->entity->tabela, 'agua');

            $data['hora_gas'] = $this->condominio_model->get_consumo_ultima_hora($this->user->unidade->id, $this->user->entity->tabela, 'gas');
            $data['hoje_gas'] = $this->condominio_model->get_consumo_hoje($this->user->unidade->id, $this->user->entity->tabela, 'gas');
            $data['last_gas'] = $this->condominio_model->get_consumo_last_24($this->user->unidade->id, $this->user->entity->tabela, 'gas');
            $data['fatu_gas'] = $this->condominio_model->get_consumo_last_fechamento($this->user->unidade->id, $this->user->entity->tabela, 'gas');

            $data['hora_energia'] = $this->condominio_model->get_consumo_ultima_hora($this->user->unidade->id, $this->user->entity->tabela, 'energia');
            $data['hoje_energia'] = $this->condominio_model->get_consumo_hoje($this->user->unidade->id, $this->user->entity->tabela, 'energia');
            $data['last_energia'] = $this->condominio_model->get_consumo_last_24($this->user->unidade->id, $this->user->entity->tabela, 'energia');
            $data['fatu_energia'] = $this->condominio_model->get_consumo_last_fechamento($this->user->unidade->id, $this->user->entity->tabela, 'energia');

            $data['ultima_leitura'] = $this->condominio_model->get_last_leitura($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['central'] = $this->condominio_model->get_central_by_unidade($this->user->unidade->id);
        }

        if ($this->user->inGroup('administradora')) {

        }

        // renderiza pagina
        echo $this->render('index', $data);
    }

    public function agua($unidade_id = false)
    {
        // TODO: verificar se é sindico do condo da unidade pra poder ver

        // se unidade_id não definida, usa a unidade do usuário logado
        $data['acesso'] = $unidade_id;
        if (!$data['acesso']) {
            $unidade_id = $this->user->unidade->id;
            $data['unidade'] = $this->user->unidade;
        } else {
            $data['unidade'] = $this->condominio_model->get_unidade($unidade_id);
        }

        // user
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        // busca lista das entradas de água da unidade do usuário
        $data['entradas'] = $this->condominio_model->get_entradas($this->user->entity->id, 'agua');
        // leitura atual do monitamento da unidade
        $data['leitura'] = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'agua', $unidade_id);
        // busca últimos 3 alertas de água recebidos pelo usuário
        $data['alertas'] = $this->condominio_model->get_alertas($this->user->id, '', 'agua', 3, true);
        // busca dados do último faturamento realizado
        $ultimo = $this->condominio_model->get_ultimo_faturamento($this->user->entity->id);
        // busca data da primeira leitura
        $primeira_leitura = $this->condominio_model->get_primeira_leitura($this->user->entity->tabela, 'agua', $unidade_id);
        $data['primeira_leitura'] = date("d/m/Y", $primeira_leitura);
        $data['ultima_leitura'] = ($data['acesso']) ? time() : $this->condominio_model->get_last_leitura($unidade_id, $this->user->entity->tabela, 'agua');
        $data['central'] = $this->condominio_model->get_central_by_unidade($unidade_id);

        // se possui faturamento usa a data final, senão últimos 30 dias
        $valor_litro = 0;
        if ($ultimo) {
            // se data mais de 35 dias, usa ultimos 30
            if ($ultimo->data_fim < strtotime('-35 days')) {
                $data_desde = strtotime('-30 days');
                $data['aviso'] = '<i class="fas fa-exclamation-circle ml-1 text-danger" data-toggle="tooltip" data-placement="bottom" title="O último faturamento realizado é antigo, usando últimos 30 dias como referência"></i>';
                $valor_litro = $ultimo->v_litro;
            } else {
                $data_desde = $ultimo->data_fim;
                $data['aviso'] = '<i class="fas fa-info-circle ml-1" data-toggle="tooltip" data-placement="bottom" title="Desde ' . date('d/m/Y', $data_desde + 86400) . '"></i>';
                $valor_litro = $ultimo->v_litro;
            }
        } else {
            $data_desde = ($primeira_leitura > strtotime('-30 days')) ? $primeira_leitura : strtotime('-30 days');
            $data['aviso'] = '<i class="fas fa-exclamation-circle ml-1 text-danger" data-toggle="tooltip" data-placement="bottom" title="Nenhum faturamento realizado, usando últimos ' . (floor((time() - $data_desde) / 86400) + 1) . ' dias como referência"></i>';
        }
        // busca consumo unidade desde o último faturamento ou 30 dias
        $consumo_faturamento = $this->condominio_model->get_consumo_desde($unidade_id, $this->user->entity->tabela, 'agua', strtotime(date('Y-m-d', $data_desde) . ' 23:59'));
        $data['voce'] = number_format($consumo_faturamento, 0, '', '.');

        // busca consumo dos vizinhos desde o último faturamento ou 30 dias
        $vizinhos = $this->condominio_model->get_consumo_vizinhos_desde($unidade_id, $this->user->entity->tabela, 'agua', strtotime(date('Y-m-d', $data_desde) . ' 23:59'));
        // busca numero de medidores do condomínio, menos os da unidade
        $unidades_count = $this->condominio_model->get_medidores_count($unidade_id, $this->user->entity->id, 'agua');
        $data['vizinhos'] = ($unidades_count == 0) ? 0 : number_format($vizinhos->consumo / $unidades_count, 0, '', '.');
        // calcular valor consumo brasil: consumo médio x numero de dias do período
        //TODO: como definir o valor médio?
        $data['brasil'] = number_format(2.66 * 110 * (strtotime('now') - $data_desde) / 86400, 0, '', '.'); // 2.66 moradores médios Metrop. Porto Alegre fipe, 110 l/dia ONU

        // previsão
        $data['previsao']['mostra'] = false;
        if ($ultimo) {
            // até agora
            $dias = floor((time() - $data_desde) / 86400);
            if ($dias == 0)
                $dias = 1;

            $previsao_consumo = $consumo_faturamento / $dias * 31;

            $faturamento_unidade = $this->condominio_model->get_faturamento_unidade($ultimo->id, $unidade_id);

            if ($faturamento_unidade) {
                $data['previsao']['ate_agora'] = number_format($consumo_faturamento * $valor_litro, 2, ',', '.');

                $data['previsao']['fechamento'] = number_format($previsao_consumo * $valor_litro + $faturamento_unidade->v_basico, 2, ',', '.');
                $data['previsao']['comparativo'] = ($previsao_consumo > $faturamento_unidade->consumo);

                $data['previsao']['mostra'] = true;
            }
        }

        // renderiza página
        echo $this->render('agua', $data);
    }

    public function administra($condo_id)
    {
        //TODO: verificar se tem acesso ao condo
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['entity'] = $this->condominio_model->get_condo($condo_id);
        echo $this->render('administra', $data);
    }

    public function gas($unidade_id = false)
    {
        // verifica se pode acessar
        if (!$this->user->inGroup('gas')) {
            // mostra proibição
            $this->render('403');
            return;
        }

        // se unidade_id não definida, usa a unidade do usuário logado
        $data['acesso'] = $unidade_id;
        if (!$data['acesso']) {
            $unidade_id = $this->user->unidade->id;
            $data['unidade'] = $this->user->unidade;
        } else {
            $data['unidade'] = $this->condominio_model->get_unidade($unidade_id);
        }


        // user
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        // busca lista das entradas de gás da unidade do usuário
        $data['entradas'] = $this->condominio_model->get_entradas($this->user->entity->id, 'gas');
        // leitura atual do monitoramento da unidade
        $data['leitura'] = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'gas', $unidade_id);
        // busca últimos 3 alertas de gas recebidos pelo usuário
        $data['alertas'] = $this->condominio_model->get_alertas($this->user->id, '', 'gas', 3);
        // busca data da primeira leitura
        $data['primeira_leitura'] = date("d/m/Y", $this->condominio_model->get_primeira_leitura($this->user->entity->tabela, 'gas', $unidade_id));

        if ($this->user->entity->m_gas == 2) {

            $this->render('gas_mensal', $data);
        } else {

            // busca dados do consumo de gás de todas entradas da unidade das últimas 24hs
            $data['bar_data'] = $this->verify_chart_data($this->get_24h_chart($data['entradas'], 'gas'));

            // calcula a direrença % do consumo de hoje em relação a ontem (levando em conta a hora atual)
            $data['diferenca'] = $this->diff_hoje_ontem($this->user->id, $this->user->entity->tabela, 'gas');

            // busca dados da última conta do monitoramento cadastrada
            $ultima = $this->condominio_model->get_ultima_conta($this->user->unidade->id, 'gas');
            $data['notificacao'] = false;
            // se possui conta cadastrada usa a data, senão últimos 30 dias
            if (!is_null($ultima)) {
                // se data mais de 35 dias, usa ultimos 30
                if ($ultima->data_fim < strtotime('-35 days')) {
                    $data_desde = strtotime('-30 days');
                    $data['aviso'] = '<i class="fas fa-exclamation-circle ml-1 text-danger" data-toggle="tooltip" data-placement="bottom" title="A última conta cadastrada é antiga, usando últimos 30 dias como referência"></i>';
                    $data['notificacao'] = true;
                } else {
                    $data_desde = $ultima->data_fim;
                    $data['aviso'] = false;
                }
            } else {
                $data_desde = strtotime('-30 days');
                $data['aviso'] = '<i class="fas fa-exclamation-circle ml-1 text-danger" data-toggle="tooltip" data-placement="bottom" title="Nenhuma conta cadastrada, usando últimos 30 dias como referência"></i>';
            }
            //busca consumos (fator de conversão m3 para kg: 2.3 segundo conta do viver canoas. internet fala em 2.5)
            $data['voce'] = number_format($this->condominio_model->get_consumo_desde($this->user->unidade->id, $this->user->entity->tabela, 'gas', strtotime(date('Y-m-d', $data_desde) . ' 23:59')) * 2.3, 0, '', '.');
            $vizinhos = $this->condominio_model->get_consumo_vizinhos_desde($this->user->unidade->id, $this->user->entity->tabela, 'gas', strtotime(date('Y-m-d', $data_desde) . ' 23:59'));
            $data['vizinhos'] = ($vizinhos->medidores == 0) ? 0 : number_format($vizinhos->consumo / $vizinhos->medidores * 2.3, 0, '', '.');
            $data['brasil'] = number_format(130000, 0, '', '.'); //TODO: como definir esse valor?
            $data['max'] = max($data['voce'], $data['vizinhos'], $data['brasil']) * 1.1; // aumenta em 10% para fins visuais da progress

            // rederiza página
            $this->render('gas', $data);
        }
    }

    public function configuracoes()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;
        $data['token'] = 1;

        echo $this->render('configuracoes', $data);
    }


    /////////////////////////
    /// REQUESTS
    /////////////////////////

    public function md_chamado()
    {
        echo view('Condominio/modals/chamado');
    }

    public function new_chamado()
    {
        $ass = $this->input->getPost('a');
        $msg = $this->input->getPost('m');
        $this->user->email = $this->condominio_model->get_user_email($this->user->id);

        $ret = $this->condominio_model->new_chamado($this->user, $ass, $msg);

        if ($ret['status'] == 'success') {
            //envia email
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'email-ssl.com.br';
            $config['smtp_port'] = '587';
            $config['smtp_timeout'] = '60';
            $config['smtp_user'] = 'contato@easymeter.com.br';
            $config['smtp_pass'] = 'index#1996';
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['mailtype'] = "html";
            $config['smtp_crypto'] = 'tls';

            $this->email->initialize($config);

            $data['cid'] = date('Y') . str_pad($ret['id'], 6, "0", STR_PAD_LEFT);
            $data['titulo'] = $ret['assunto'];
            $data['nome'] = $this->user->username;
            $data['msg'] = $msg;
            $data['prev'] = date('d/m/Y', strtotime("+2 days", time()));

            $this->email->setFrom('contato@easymeter.com.br', "Easymeter");
            $this->email->setTo($this->user->email);
            $this->email->setReplyTo('contato@easymeter.com.br');
            $this->email->setSubject('Suporte Easymeter');
            $this->email->setMessage(view('Condominio/emails/suporte', $data));

            $this->email->send();
        }

        echo json_encode($ret);
    }

    public function get_leitura()
    {
        $leitura = $this->condominio_model->get_leitura_unidade($this->user->entity->tabela, $this->user->unidade->id);

        if ($leitura) {
            //$this->user->unidade->leitura_agua = $leitura->leitura_agua;

            echo json_encode(array('status' => 'success', 'data' => $leitura));
        } else
            echo json_encode(array('status' => 'success', 'data' => false));
    }

    public function get_leituras()
    {
        $entity_id = $this->input->getGet('entity');
        if (is_null($entity_id)) $entity_id = $this->user->entity->id;

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
            LEFT JOIN esm_entidades ON esm_ramais.entidade_id = esm_entidades.id
            WHERE esm_entidades.id = $entity_id AND esm_ramais.nome LIKE \"G%\" ORDER BY esm_fechamentos.id DESC
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

    public function get_unidades_bloco()
    {
        $agrupamento = intval($this->input->getGet('agrupamento'));

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.id, 
                esm_unidades.nome AS apto, 
                esm_unidades.andar, 
                esm_unidades.fracao, 
                esm_unidades.codigo, 
                esm_unidades.tipo,
                esm_agrupamentos.nome AS bloco, 
                user.id AS prop_id, 
                IFNULL(user.nome, 'Não cadastrado') AS nome, 
                IFNULL(user.username, '-') AS email, 
                IFNULL(user.telefone, '-') AS telefone
            FROM esm_unidades
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            LEFT JOIN (
                SELECT auth_users_unidades.unidade_id, auth_users.id, auth_users.nome, auth_users.username, auth_users.telefone
                FROM auth_users
                JOIN auth_users_groups ON auth_users_groups.user_id = auth_users.id
                JOIN auth_users_unidades ON auth_users_unidades.user_id = auth_users.id
                WHERE auth_users_groups.group_id = 3) AS user ON user.unidade_id = esm_unidades.id
            WHERE esm_unidades.agrupamento_id = $agrupamento
            ORDER BY esm_agrupamentos.nome, esm_unidades.nome
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