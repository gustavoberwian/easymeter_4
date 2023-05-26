<?php

namespace App\Controllers;

use App\Models\Admin_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;
use Viacep;
use CodeIgniter\Shield\Entities\User;
use Config\Database;




class Admin extends UNO_Controller
{
    private Admin_model $admin_model;

    protected $input;

    protected $email;

    protected Datatables $datatables;

    protected Viacep $viacep;

    public $url;

    public $db;

    public $db2;

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

        // set variables
        $this->url = service('uri')->getSegment(1);


        $this->db = Database::connect();

        $this->db2 = Database::connect('easy_com_br');

    }

    public function index(): string
    {
        $data['centrais'] = $this->admin_model->get_all_centrais();
        return $this->render("index", $data);
    }

    public function centrais($id = false)
    {
        if ($id) {
            $data['central'] = $this->admin_model->get_central_entidade($id);
            $data['data'] = $this->admin_model->get_last_data($data['central']->nome, $data['central']->ultimo_envio);
            $data['erros'] = $this->admin_model->get_error_leitura($id, $data['central']->tabela);
            $labels = array();
            for ($i = 0; $i < 40; $i++) {
                $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
            }
            $data['leituras'] = $labels;

            return $this->render('central', $data);
        } else
            return $this->render('centrais', array('count' => $this->admin_model->get_centrais_count()));
    }

    public function entities($param1 = null, $param2 = null): string
    {
        if (intval($param1) > 0) {

            $data['geral'] = false;
            $data['entity'] = $this->admin_model->get_entity($param1);
            $data['groups'] = $this->admin_model->get_groups($data['entity']->id);

            if ($param2 == "editar") {

                $data['ramais'] = $this->admin_model->get_ramais($param1, true);
                $data['centrais'] = $this->admin_model->get_centrais($param1, true);
                $data['entradas'] = $this->admin_model->get_entradas($param1);
                $data['readonly'] = '';

                $labels = array();
                for ($i = 0; $i < 40; $i++) {
                    $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
                }
                $data['leituras'] = $labels;

                return $this->render('entity_edit', $data);
            } else {

                $data['readonly'] = 'readonly disabled';

                $labels = array();
                for ($i = 0; $i < 40; $i++) {
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

    // public function entitiess($param1 = null, $param2 = null): string
    // {
    //     if ( intval($param1) > 0 ) {

    //         $data['condo'] = $this->admin_model->get_condo($param1);
    //         $data['agrupamentos'] = $this->admin_model->get_groups($data['condo']->id);

    //         if ($param2 == "editar") {

    //             $data['ramais'] = $this->admin_model->get_ramais($param1, true);
    //             $data['centrais'] = $this->admin_model->get_centrais($param1, true);
    //             $data['entradas'] = $this->admin_model->get_entradas($param1);
    //             $data['readonly'] = '';
    //             $data['title'] = 'Editar Condomínio';
    //             $data['geral'] = false;
    //             $labels = array();
    //             for($i = 0; $i < 40; $i++) {
    //                 $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
    //             }
    //             $data['leituras'] = $labels;

    //             return $this->render('entity_edit', $data);
    //         } else {

    //             $data['readonly'] = 'readonly';
    //             $data['title'] = 'Visualizar Condomínio';
    //             $data['ramais'] = $this->admin_model->get_ramais($param1, true);
    //             $data['centrais'] = $this->admin_model->get_centrais($param1, true);
    //             $data['entradas'] = $this->admin_model->get_entradas($param1);
    //             $data['geral'] = false;

    //             foreach ($data['entradas'] as $e) {
    //                 if ($e->entrada == "Geral") {
    //                     $data['geral'] = true;
    //                     break;
    //                 }
    //             }

    //             if ($data['geral']) {
    //                 $data['unidade_geral'] = $this->admin_model->get_unidade_medidor_principal($param1);
    //                 $data['central_geral'] = $this->admin_model->get_central_by_unidade($data['unidade_geral'])->central;
    //                 $data['primeira_leitura'] = date("d/m/Y", $this->admin_model->get_primeira_leitura($data['condo']->tabela, 'agua', $data['unidade_geral']));

    //             }

    //             $labels = array();
    //             for($i = 0; $i < 40; $i++) {
    //                 $labels[] = date("d/m/Y", strtotime("+$i days", strtotime("-40 days ")));
    //             }
    //             $data['leituras'] = $labels;

    //             return $this->render('entity_edit', $data);
    //         }

    //     } else if ($param1 == "incluir") {

    //         return $this->render('entity_add');
    //     } else {

    //         return $this->render('entities');
    //     }
    // }

    public function users($param1 = null, $param2 = null)
    {

        if (intval($param1) > 0) {
            $users = auth()->getProvider();
            $user = $users->findById($param1);
            $groups = $this->admin_model->get_user_info($param1);

            $data['usuario'] = $user;
            $data['id'] = $param1;
            $data['email'] = $groups['email'];
            $data['classificacao'] = $groups['classificacao'];
            $data['groups'] = $this->admin_model->get_groups_for_user($param1);



            if ($data['classificacao'] == 'entidades') {
                $data['val'] = $this->admin_model->get_name_by_id($data['classificacao'], $this->admin_model->get_user_relations($param1, 'entidade'));

            } elseif ($data['classificacao'] == 'agrupamentos') {
                $data['val'] = $this->admin_model->get_name_by_id($data['classificacao'], $this->admin_model->get_user_relations($param1, 'agrupamento'));
            } elseif ($data['classificacao'] == 'unidade') {
                $data['val'] = $this->admin_model->get_code_by_unity_id($this->admin_model->get_user_relations($param1, $data['classificacao']));
            } else {
                $data['val'] = '';
            }


            if ($param2 == 'editar') {
                $data['readonly'] = '';
                return $this->render('edit_user', $data);
            } else {
                $data['readonly'] = 'readonly disabled';
                return $this->render('edit_user', $data);
            }

        } elseif ($param1 === 'incluir') {
            return $this->render("add_user");
        }
        return $this->render('users');
    }


    public function profile()
    {
        $data['validation'] = \Config\Services::validation();
        $data['session'] = \Config\Services::session();
        $data['set'] = false;
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['emails'] = $this->admin_model->get_user_emails($this->user->id);
        helper('form');
        $user_id = $this->user->id;

        if ($this->user->inGroup('shopping', 'admin')) {
            $data['condo'] = $this->admin_model->get_condo($this->user->type->entity_id);
        } elseif ($this->user->inGroup('group')) {
            $data['condo'] = $this->admin_model->get_condo_by_group($this->user->type->group_id);
        } elseif ($this->user->inGroup('unity')) {
            $data['condo'] = $this->admin_model->get_condo_by_unity($this->user->type->unity_id);
        } else {
            $data['condo'] = '';
        }

        if ($this->input->getMethod() == 'post') {
            $image = $this->input->getPost('crop-image');
            $senha = $this->input->getPost('password');

            if ($image) {
                // valida se é imagem...

                // salva avatar
                list($type, $image) = explode(';', $image);
                list(, $image) = explode(',', $image);
                $image = base64_decode($image);
                $filename = time() . $this->user->id . '.png';
                if (file_put_contents('../public/assets/img/uploads/avatars/' . $filename, $image)) {

                    // mensagem
                    $img['avatar'] = $filename;

                    // apaga avatar anterior
                    if ($this->user->avatar && file_exists('../public/assets/img/uploads/avatars/' . $this->user->avatar)) {
                        unlink('../public/assets/img/uploads/avatars/' . $this->user->avatar);
                        $this->user->avatar = $filename;

                    }

                    // atualiza avatar em auth_users
                    if ($this->admin_model->update_avatar($this->user->id, $img)) {
                        $data['error'] = false;
                    } else {
                        //erro e mensagem
                    }
                } else {
                    //erro e mensagem
                }
            } else {


                if ($this->input->getPost('password')) {
                    $rules = [
                        'password' => 'required|min_length[6]',
                        'confirm' => 'required|matches[password]',
                        'celular' => 'permit_empty|regex_match[/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/]',
                        'telefone' => 'permit_empty|regex_match[/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/]'
                    ];
                } else {
                    $rules = [
                        'celular' => 'permit_empty|regex_match[/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/]',
                        'telefone' => 'permit_empty|regex_match[/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/]'
                    ];

                };



                $emails = null;
                if ($this->input->getPost('emails') != '') {
                    $emails = explode(',', $this->input->getPost('emails'));
                    $rules = [
                        'emails' => 'valid_email'
                    ];
                }


                if ($this->validate($rules) && !isset($data['email_form'])) {
                    // coleta os dados do post
                    $password = $this->input->getPost('password');
                    $telefone = $this->input->getPost('telefone');

                    $celular = $this->input->getPost('celular');



                    // atualiza dados
                    if (!$this->admin_model->update_user($user_id, $password, $telefone, $celular, $emails)) {
                        $data['error'] = true;

                        echo json_encode(array(
                            'status' => 'error',
                            'message' => 'Não foi possível atualizar os dados. Tente novamente em alguns minutos.'
                        ));
                    } else {
                        $data['error'] = false;
                        //mensagem
                        echo json_encode(array(
                            'status' => 'success',
                            'message' => 'Seus dados foram atualizados com sucesso.'
                        ));

                    }
                }
                return;
            }
        }
        $data['avatar'] = $this->user->avatar;
        echo $this->render('profile', $data);
    }

    public function grupos()
    {
        $this->render('grupos');
    }

    public function alertas()
    {
        $this->render('alerts');
    }

    
    // POST PART FILE

    public function get_entities()
    {
        // processa filtros externos ao DT
        $inactives = " AND esm_entidades.status = 'ativo'";
        if (filter_var($this->input->getGet('inactives'), FILTER_VALIDATE_BOOLEAN))
            $inactives = "";

        $mode = "";
        if ($this->input->getGet('mode') == 1)
            $mode = " AND esm_entidades.m_agua = 1";
        if ($this->input->getGet('mode') == 2)
            $mode = " AND esm_entidades.m_gas > 0";
        if ($this->input->getGet('mode') == 3)
            $mode = " AND esm_entidades.m_energia = 1";
        if ($this->input->getGet('mode') == 4)
            $mode = " AND esm_entidades.m_nivel = 1";

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
                esm_entidades.visibility = 'normal'" . $inactives . $mode
        );
        // inclui monitoramento
        $dt->add('monitoramento', function ($data) {
            $ret = '';
            if ($data['agua'] == 1)
                $ret .= '<i class="fas fa-tint text-primary" title="Água"></i> ';
            if ($data['gas'] == 1)
                $ret .= '<i class="fas fa-fire text-success" title="Gás"></i> ';
            if ($data['gas'] == 2)
                $ret .= '<i class="fas fa-fire text-success" title="Gás Mensal"></i> ';
            if ($data['energia'] == 1)
                $ret .= '<i class="fas fa-bolt text-warning" title="Energia Elétrica"></i>';
            if ($data['nivel'] == 1)
                $ret .= '<i class="fas fa-ruler-vertical text-info" title="Nível de Reservatório"></i>';

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
                    esm_agrupamentos.nome AS bloco, 
                    user.id AS prop_id, 
                    IFNULL(user.username, 'Não cadastrado') AS nome
            FROM esm_unidades
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            LEFT JOIN (
                    SELECT auth_user_relation.unidade_id, auth_users.id, auth_users.username
                    FROM auth_users
                    JOIN auth_user_relation ON auth_user_relation.user_id = auth_users.id 
            ) AS user ON user.unidade_id = esm_unidades.id
            WHERE esm_unidades.agrupamento_id = $bloco
            ORDER BY esm_agrupamentos.nome, esm_unidades.nome
        ");

        // inclui campo medidores
        $dt->add('medidores', function ($data) {
            $entradas = $this->admin_model->get_medidores_unidade($data['id']);
            $medidores = '';
            foreach ($entradas as $e) {
                if ($e->tipo == 'agua') {
                    if (is_null($e->central))
                        $medidores .= '<span class="cur-pointer badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '<br/>Não monitorado">&nbsp;</span>';
                    else {
                        $s = is_null($e->sensor_id) ? '' : '<b>Sensor:</b> ' . $e->sensor_id;
                        $medidores .= '<span class="cur-pointer badge badge-' . ($e->posicao == 0 ? 'secondary' : $e->tipo) . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '<br/><b>Central</b>: ' . $e->central . '<br/><b>Posição:</b> ' . $e->posicao . '<br/>' . $s . '">' . $e->nome . '</span>';
                    }
                } else if ($e->tipo == 'gas') {
                    if (is_null($e->central))
                        $medidores .= '<span class="cur-pointer badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '">' . $e->nome . '</span>';
                    else {
                        $s = is_null($e->sensor_id) ? '' : '<b>Sensor:</b> ' . $e->sensor_id;
                        $medidores .= '<span class="cur-pointer badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '<br/><b>Central</b>: ' . $e->central . '<br/><b>Posição:</b> ' . $e->posicao . '<br/>' . $s . '">' . $e->nome . '</span>';
                    }
                } else if ($e->tipo == 'energia') {
                    if (is_null($e->central))
                        $medidores .= '<span class="cur-pointer badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '">' . $e->nome . '</span>';
                    else {
                        $s = is_null($e->sensor_id) ? '' : '<b>Sensor:</b> ' . $e->sensor_id;
                        $medidores .= '<span class="cur-pointer badge badge-' . $e->tipo . ' action-medidor mr-3" data-id="' . $e->id . '" data-content="<b>Entrada:</b> ' . $e->entrada . '<br/><b>Central</b>: ' . $e->central . '<br/><b>Posição:</b> ' . $e->posicao . '<br/>' . $s . '">' . $e->nome . '</span>';
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

    public function suporte($id = false)
    {
        if ($id) {
            if ($this->input->getPost('fechar') == "fechar") {
                $data['chamado_close'] = $this->admin_model->chamado_close($id, $this->user->id);
            }
            if ($this->input->getPost('reply') == "reply") {
                $data['chamado_reply'] = $this->admin_model->new_reply($id, $this->input->getPost('message'), $this->user->id);
                //TODO: enviar email pro usuário
            }
            $data['chamado'] = $this->admin_model->get_chamado($id);
            if ($data['chamado']->status == 'aberto')
                $data['status'] = "<span class=\"badge badge-danger\">Aberto</span>";
            elseif ($data['chamado']->status == 'fechado')
                $data['status'] = "<span class=\"badge badge-success\">Fechado</span>";
            else
                $data['status'] = "<span class=\"badge badge-warning\">" . ucfirst($data['chamado']->status) . "</span>";

            $data['replys'] = $this->admin_model->get_chamado_reply($id);
            $this->render('suporte_reply', $data);
        } else {
            return $this->render('suporte');
        }
    }

    public function md_chamado()
    {
        echo view('Admin/modals/chamado');
    }

    public function md_new_chamado()
    {

        return view('modals/admin/md_chamado');
    }

    public function new_chamado()
    {
        $ass = $this->input->getPost('assunto');
        $msg = $this->input->getPost('message');
        $this->user->email = $this->admin_model->get_user_emails($this->user->id);

        $ret = $this->admin_model->new_chamado($this->user, $ass, $msg);

        if ($ret['status'] == 'success') {
            //envia email
            $email = \Config\Services::email();
            
            $data['cid'] = date('Y') . str_pad($ret['id'], 6, "0", STR_PAD_LEFT);
            $data['titulo'] = $ret['assunto'];
            $data['nome'] = $this->user->username;
            $data['msg'] = $msg;
            $data['prev'] = date('d/m/Y', strtotime("+2 days", time()));

            $email->setFrom('contato@easymeter.com.br', "Easymeter");
            $email->setTo('atendimento@unorobotica.com.br');
            $email->setReplyTo($this->user->email);
            $email->setSubject('Suporte Easymeter');
            $email->setMessage(view('admin/emails/aviso_chamado', $data));

            $email->send();

            
            $email->setFrom('contato@easymeter.com.br', "Easymeter");
            $email->setTo($this->user->email);
            $email->setReplyTo('contato@easymeter.com.br');
            $email->setSubject('Suporte Easymeter');
            $email->setMessage(view('admin/emails/suporte', $data));

            $email->send();
        }

        echo json_encode($ret);
    }

    public function get_chamados_novo()
    {
        // realiza a query via dt
        $dt = $this->datatables->query("  SELECT
        esm_tickets.id AS id, 
        esm_tickets.unidade_id AS Unidade_id, 
        esm_tickets.nome AS ticket, 
        esm_tickets.email AS email, 
        esm_tickets.mensagem AS mensagem, 
        esm_tickets.STATUS AS status, 
        DATE_FORMAT( esm_tickets.cadastro, '%d/%m/%Y' ) AS cadastro, 
        esm_departamentos.nome AS departamento, 
        esm_entidades.tabela AS entidade, 
        esm_entidades.nome AS agrupamento, 
        esm_entidades.classificacao AS classificacao
    FROM
        esm_tickets
        JOIN
        esm_departamentos
        ON 
            esm_tickets.departamento = esm_departamentos.id
        LEFT JOIN
        esm_unidades
        ON 
            esm_unidades.id = esm_tickets.unidade_id
        LEFT JOIN
        esm_agrupamentos
        ON 
            esm_agrupamentos.id = esm_unidades.agrupamento_id
        LEFT JOIN
        esm_entidades
        ON 
            esm_entidades.id = esm_agrupamentos.entidade_id
    GROUP BY
        esm_tickets.id
    ORDER BY
        COALESCE ( esm_tickets.cadastro ) DESC
            ");

        $dt->add('DT_RowId', function ($data) {
            return $data['id'];
        });

        $dt->edit('entidade', function ($data) {
            return ucfirst($data['entidade']);
        });

        $dt->edit('classificacao', function ($data) {
            return '<span class="badge badge-primary">' . ucfirst($data['classificacao']) . '</span>';
        });

        $dt->edit('status', function ($data) {
            if ($data['status'] == 'aberto')
                return "<span class=\"badge badge-danger\" style=\"width: 70px;\">Aberto</span>";
            elseif ($data['status'] == 'fechado')
                return "<span class=\"badge badge-success\" style=\"width: 70px;\">Fechado</span>";
            else
                return "<span class=\"badge badge-warning\" style=\"width: 70px;\">" . ucfirst($data['status']) . "</span>";
        });

        // gera resultados
        echo $dt->generate();
    }

    public function relatorios($entity = "")
    {
        if ($entity == 'viver') {
            $data['competencias'] = $this->admin_model->get_competencias(3, 3);
            $this->render('reports_viver', $data);
        } else if ($entity == 'baviera') {
            $data['competencias'] = $this->admin_model->get_competencias(3, 3);
            $this->render('reports_baviera', $data);
        } else {
            $this->render('reports');
        }
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

    public function get_ramais()
    {
        //realiza consulta
        $uriSegments = explode("/", parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
        $p = $this->admin_model->get_ramais($uriSegments[3]);
        // retorna dados formatados
        echo '{ "results" : ' . json_encode($p) . '}';
    }

    public function get_entity()
    {
        $q = $this->input->getGet('q');
        //realiza consulta
        $p = $this->admin_model->get_entity_for_select($q);

        // retorna dados formatados
        echo '{ "results" : ' . json_encode($p) . '}';
    }

    public function add_adm()
    {
        $dados['nome'] = $this->input->getPost('nome-adm') ?? '';
        $dados['cnpj'] = $this->input->getPost('cnpj-adm') ?? '';
        $dados['contato'] = $this->input->getPost('contato-adm') ?? '';
        $dados['email'] = $this->input->getPost('email-adm') ?? '';
        $dados['site'] = $this->input->getPost('site-adm') ?? '';
        $dados['cep'] = $this->input->getPost('cep-adm') ?? '';
        $dados['logradouro'] = $this->input->getPost('logradouro-adm') ?? '';
        $dados['numero'] = $this->input->getPost('numero-adm') ?? '';
        $dados['complemento'] = $this->input->getPost('complemento-adm') ?? '';
        $dados['bairro'] = $this->input->getPost('bairro-adm') ?? '';
        $dados['cidade'] = $this->input->getPost('cidade-adm') ?? '';
        $dados['tel_1'] = $this->input->getPost('telefone1-adm') ?? '';
        $dados['tel_2'] = $this->input->getPost('telefone2-adm') ?? '';

        echo $this->admin_model->add_adm($dados);
    }

    public function add_gestor()
    {
        $dados['nome'] = $this->input->getPost('nome-gestor') ?? '';
        $dados['cpf'] = $this->input->getPost('cpf-gestor') ?? '';
        $dados['aniversario'] = $this->input->getPost('nasc-gestor') ?? '';
        $dados['email'] = $this->input->getPost('email-gestor') ?? '';
        $dados['tipo'] = $this->input->getPost('tipo-gestor') ?? '';
        $dados['site'] = $this->input->getPost('site-gestor') ?? '';
        $dados['tel_1'] = $this->input->getPost('telefone1-gestor') ?? '';
        $dados['tel_2'] = $this->input->getPost('telefone2-gestor') ?? '';
        $dados['tel_3'] = $this->input->getPost('celular1-gestor') ?? '';
        $dados['tel_4'] = $this->input->getPost('celular2-gestor') ?? '';

        echo $this->admin_model->add_gestor($dados);
    }

    public function add_ramal()
    {

        $dados['nome']          = $this->input->getPost('nome-ramal') ?? '';
        $dados['tipo']          = $this->input->getPost('tipo-ramal') ?? '';
        $dados['entidade_id']   = $this->input->getPost('sel-entity') ?? '';

        echo $this->admin_model->add_ramal($dados);
    }

    public function add_entity()
    {
        $dados['classificacao'] = $this->input->getPost('classificacao-entity') ?? '';
        $dados['nome'] = $this->input->getPost('nome-entity') ?? '';
        $dados['cnpj'] = $this->input->getPost('cnpj-entity') ?? '';
        $dados['tipo'] = $this->input->getPost('tipo-entity') ?? '';
        $dados['cep'] = $this->input->getPost('cep-entity') ?? '';
        $dados['logradouro'] = $this->input->getPost('logradouro-entity') ?? '';
        $dados['numero'] = $this->input->getPost('numero-entity') ?? '';
        $dados['complemento'] = $this->input->getPost('complemento-entity') ?? '';
        $dados['bairro'] = $this->input->getPost('bairro-entity') ?? '';
        $dados['cidade'] = $this->input->getPost('cidade-entity') ?? '';
        $dados['uf'] = $this->input->getPost('estado-entity') ?? '';
        $dados['inicio'] = $this->input->getPost('inicio-entity') ?? '';
        $dados['fim'] = $this->input->getPost('fim-entity') ?? '';
        $dados['m_agua'] = $this->input->getPost('agua-entity') === 'on' ? 1 : 0;
        $dados['m_gas'] = $this->input->getPost('gas-entity') === 'on' ? 1 : 0;
        $dados['m_energia'] = $this->input->getPost('energia-entity') === 'on' ? 1 : 0;
        $dados['m_nivel'] = $this->input->getPost('nivel-entity') === 'on' ? 1 : 0;
        $dados['fracao_ideal'] = $this->input->getPost('fracao-entity') === 'on' ? 1 : 0;
        $dados['status'] = $this->input->getPost('switch') === 'on' ? 'ativo' : 'inativo';
        $dados['admin_id'] = $this->input->getPost('select-adm') ?? '';
        $dados['gestor_id'] = $this->input->getPost('select-gestor') ?? '';

        echo $this->admin_model->add_entity($dados);
    }

    public function edit_entity()
    {
        $dados['id'] = $this->input->getPost('id-entity');
        $dados['classificacao'] = $this->input->getPost('classificacao-entity') ?? '';
        $dados['nome'] = $this->input->getPost('nome-entity') ?? '';
        $dados['cnpj'] = $this->input->getPost('cnpj-entity') ?? '';
        $dados['tipo'] = $this->input->getPost('tipo-entity') ?? '';
        $dados['cep'] = $this->input->getPost('cep-entity') ?? '';
        $dados['logradouro'] = $this->input->getPost('logradouro-entity') ?? '';
        $dados['numero'] = $this->input->getPost('numero-entity') ?? '';
        $dados['complemento'] = $this->input->getPost('complemento-entity') ?? '';
        $dados['bairro'] = $this->input->getPost('bairro-entity') ?? '';
        $dados['cidade'] = $this->input->getPost('cidade-entity') ?? '';
        $dados['uf'] = $this->input->getPost('estado-entity') ?? '';
        $dados['inicio'] = $this->input->getPost('inicio-entity') ?? '';
        $dados['fim'] = $this->input->getPost('fim-entity') ?? '';
        $dados['m_agua'] = $this->input->getPost('agua-entity') === 'on' ? 1 : 0;
        $dados['m_gas'] = $this->input->getPost('gas-entity') === 'on' ? 1 : 0;
        $dados['m_energia'] = $this->input->getPost('energia-entity') === 'on' ? 1 : 0;
        $dados['m_nivel'] = $this->input->getPost('nivel-entity') === 'on' ? 1 : 0;
        $dados['fracao_ideal'] = $this->input->getPost('fracao-entity') === 'on' ? 1 : 0;
        $dados['status'] = $this->input->getPost('switch') === 'on' ? 'ativo' : 'inativo';
        $dados['admin_id'] = $this->input->getPost('select-adm') ?? '';
        $dados['gestor_id'] = $this->input->getPost('select-gestor');


        echo $this->admin_model->edit_entity($dados);
    }

    public function busca_endereco()
    {
        $cep = $this->input->getPost('cep');

        // executa o curl e retorna os dados
        echo $this->viacep->busca_cep($cep);
    }

    public function md_bloco()
    {
        $bid = $this->input->getPost('bid');
        $cid = $this->input->getPost('cid');

        if ($bid > 0) {
            $data['modal_title'] = "Editar Bloco";
            $data['bloco'] = $this->admin_model->get_bloco($bid);
        } else {
            $data['modal_title'] = "Incluir Bloco";
        }

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
        $data['entity'] = $this->admin_model->get_entity_config_by_bloco($bloco, ', esm_entidades.d_proprietarios, esm_entidades.tipo_unidades, esm_agrupamentos.nome as b_nome');
        // entradas do condominio
        $data['entradas'] = $this->admin_model->get_entradas($cid);
        // centrais do condominio
        $data['centrais'] = $this->admin_model->get_centrais($cid);
        // fraçoes já cadastradas no condominio
        $data['fracoes'] = $this->admin_model->get_fracoes_entidade($cid);
        // modo
        $data['modo'] = $modo;
        // edição ou inclusão?
        if ($uid > 0) {
            // dados da unidade e medidores
            $data['unidade'] = $this->admin_model->get_unidade($uid);
            $entradas = $this->admin_model->get_medidores_unidade($uid);
            if (count($entradas) > 0)
                $data['entradas'] = $entradas;

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
        $central = $this->input->getPost('id');
        $porta = $this->input->getPost('porta');

        $portas = $this->admin_model->get_portas($central);
        if (!$portas) {
            for ($i = 1; $i < 65; $i++)
                $out .= '<option value="' . $i . '">' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>';
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

            // atualiza bloco na tabela agrupamentos
            echo $this->admin_model->update_bloco($id, $nome, $agua, $agua_edit, $agua_delete, $ramal_id);
        } else {
            $unidade = array(
                'agrupamento_id' => $bid,
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

    public function delete_bloco()
    {
        $id = $this->input->getPost('id');

        echo $this->admin_model->delete_bloco($id);
    }

    public function edit_agrupamento()
    {

        $id         = $this->input->getPost('id');
        $cid        = $this->input->getPost('id-condo');
        $nome       = $this->input->getPost('id-bloco');
        $rid        = json_decode($this->input->getPost('sel-ramal'));
        $message    = [];


        if (is_array($rid)) {
            if ($id) {
                // atualiza bloco na tabela agrupamentos
                if (!$this->admin_model->update_agrupamento($id, $nome, 0)) {
                    return false;
                } else {
                    foreach ($rid as $r) {
                        $data['agrupamento_id'] = $id->data->value;
                        $data['ramal_id'] = $r->value;
                        $message = $this->admin_model->group_ramais($data);
                    }
                }
            } else {
                // insere bloco
                $return = json_decode($this->admin_model->add_agrupamento($cid, $nome, 0));
                foreach ($rid as $r) {
                    $data['agrupamento_id'] = $return->data->value;
                    $data['ramal_id'] = $r->value;
                    $message = $this->admin_model->group_ramais($data);
                }
            }
        } else {
            if ($id) {
                // atualiza bloco na tabela agrupamentos
                $message = $this->admin_model->update_agrupamento($id, $nome, $rid->value);
            } else {
                // insere bloco
                $message = $this->admin_model->add_agrupamento($cid, $nome, $rid->value);
            }
        }
        echo $message;
    }

    public function delete_unidade()
    {
        $id = $this->input->getPost('id');

        echo $this->admin_model->delete_unidade($id);
    }

    public function delete_user()
    {
        $id = $this->input->getPost('uid');

        $users = auth()->getProvider();

        $users->delete($id, true);
        return json_encode(
            array(
                'status' => 'success',
                'message' => 'Seus dados foram atualizados com sucesso.'
            )
        );
    }

    public function get_users()
    {
        if ($this->input->getGet("mode") == 0) $m = "";
        if ($this->input->getGet('mode') == 1) $m = " JOIN auth_groups_users ON auth_groups_users.user_id = auth_users.id AND auth_groups_users.group = 'agua'";
        if ($this->input->getGet('mode') == 2) $m = " JOIN auth_groups_users ON auth_groups_users.user_id = auth_users.id AND auth_groups_users.group = 'gas'";
        if ($this->input->getGet('mode') == 3) $m = " JOIN auth_groups_users ON auth_groups_users.user_id = auth_users.id AND auth_groups_users.group = 'energia'";
        if ($this->input->getGet('mode') == 4) $m = " JOIN auth_groups_users ON auth_groups_users.user_id = auth_users.id AND auth_groups_users.group = 'nivel'";

        $dt = $this->datatables->query(
            "
            SELECT 
                auth_users.id AS id,
                auth_users.avatar AS avatar,
                auth_users.username AS nome,
                auth_identities.secret AS email,
                auth_users.page AS page,
                auth_users.active AS status
            FROM auth_users
            JOIN auth_identities ON auth_identities.user_id = auth_users.id AND auth_identities.type = 'email_password'
           " . $m
        );

        $dt->edit("avatar", function ($data) {
            if ($data['avatar']) {
                return '<img alt src="' . base_url('assets/img/uploads/avatars/') . $data['avatar'] . ' " style="width: 32px" class="rounded-circle" />';
            }

            return '<img alt src="' . base_url('assets/img/user.png') . ' " style="width: 32px" class="rounded-circle" />';
        });

        $dt->edit("status", function ($data) {
            $checked = "";
            if ($data['status']) {
                $checked = "checked";
            }

            return '
                <div class="switch switch-sm switch-primary">
                    <input type="checkbox" class="switch-input" data-id="' . $data['id'] . '" name="switch" data-plugin-ios-switch ' . $checked . ' />
                </div>
            ';
        });

        $dt->add("monitora", function ($data) {
            $user = auth()->getProvider()->findById($data['id']);

            $res = "";

            if ($user->inGroup('agua')) {
                $res .= '<i class="fas fa-tint text-primary me-1"></i>';
            }
            if ($user->inGroup('energia')) {
                $res .= '<i class="fas fa-bolt text-warning me-1"></i>';
            }
            if ($user->inGroup('gas')) {
                $res .= '<i class="fas fa-fire text-success me-1"></i>';
            }
            if ($user->inGroup('nivel')) {
                $res .= '<i class="fas fa-database text-info me-1"></i>';
            }

            return $res;
        });

        $dt->add("groups", function ($data) {
            $groups = $this->admin_model->get_groups_by_user($data['id']);
            $res = "";

            foreach ($groups as $g) {
                $res .= '<span class="badge badge-info me-1 monitor">' . $g->group . '</span>';
            }

            return $res;
        });

        $dt->add('actions', function ($data) {
            return '
                <a href="' . site_url('admin/users/') . $data['id'] . '/editar" class="action-edit" data-id="' . $data['id'] . '"><i class="fas fa-pencil-alt text-primary" title="Editar"></i></a>
				<a href="#" class="action-delete-user" data-id="' . $data['id'] . '" data-toggle="confirmation" data-title="Certeza?"><i class="fas fa-trash text-danger" title="Excluir"></i></a>
            ';
        });

        echo $dt->generate();
    }

    public function add_user()
    {
        $users = auth()->getProvider();

        // Cria novo usuário
        $user = new User([
            'username' => $this->input->getPost('nome-user') ?? '',
            'email' => $this->input->getPost('email-user') ?? '',
            'password' => $this->input->getPost('senha-user') ?? '',
            'active' => $this->input->getPost('switch') === 'on' ? 1 : 0
        ]);

        $users->save($user);

        // Recebe dados para a inserção
        $dados['group'] = [];

        if ($this->input->getPost('user-agua') === 'on') {
            $dados['group']['agua'] = 'agua';
        }

        if ($this->input->getPost('user-gas') === 'on') {
            $dados['group']['gas'] = 'gas';
        }

        if ($this->input->getPost('user-energia') === 'on') {
            $dados['group']['energia'] = 'energia';
        }

        if ($this->input->getPost('user-nivel') === 'on') {
            $dados['group']['nivel'] = 'nivel';
        }

        $dados['user-id']               = $users->getInsertID();
        $dados['classificacao']         = $this->input->getPost('classificacao-user');
        $dados['page']                  = $this->input->getPost('page-user') ?? '';
        $dados['entity-user']           = $this->input->getPost('entity-user') ?? '';
        $dados['unity-user']            = $this->input->getPost('unity-user') ?? '';
        $dados['group-user']            = $this->input->getPost('group-user') ?? '';
        $dados['groups-user']           = array_map('trim', explode(",", $this->input->getPost('groups-user') ?? ''));


        //Chamada da função de inserção
        echo $this->admin_model->add_user($dados);
    }

    public function get_entity_for_select()
    {
        //realiza consulta
        $p = $this->admin_model->get_entities();

        $result = '';
        foreach ($p as $option) {
            $result .= "<option>$option</option>";
        }
        print_r($result);
        echo $result;
    }

    public function get_groups_for_select()
    {
        //realiza consulta
        $p = $this->admin_model->get_groups_for_select();

        $result = '';
        foreach ($p as $option) {
            $result .= "<option>$option</option>";
        }
        print_r($result);
        echo $result;

    }

    public function edit_active_stats()
    {
        if (!$this->admin_model->update_active($this->input->getPost('id'))) {
            return false;
        }
    }

    public function edit_user()
    {
        $users = auth()->getProvider();
        $dados['user_id'] = $this->input->getPost('id-user');

        $user = $users->findById($dados['user_id']);

        //Edita usuário
        $user->fill([
            'username' => $this->input->getPost('nome-user') ?? '',
            'email' => $this->input->getPost('email-user') ?? '',
            'password' => $this->input->getPost('senha-user') ?? '',
            'active' => $this->input->getPost('switch') === 'on' ? 1 : 0
        ]);

        $users->save($user);


        // Recebe dados para a edição

        if ($this->input->getPost('user-agua') === 'on') {
            $dados['group']['agua'] = 'agua';
        } else {
            $dados['group']['agua'] = '';
        }

        if ($this->input->getPost('user-gas') === 'on') {
            $dados['group']['gas'] = 'gas';
        } else {
            $dados['group']['gas'] = '';
        }


        if ($this->input->getPost('user-energia') === 'on') {
            $dados['group']['energia'] = 'energia';
        } else {
            $dados['group']['energia'] = '';
        }
        if ($this->input->getPost('user-nivel') === 'on') {
            $dados['group']['nivel'] = 'nivel';
        } else {
            $dados['group']['nivel'] = '';
        }


        $dados['page'] = $this->input->getPost('page-user') ?? '';
        $dados['groups-user'] = array_map('trim', explode(",", $this->input->getPost('groups-user') ?? ''));

        //Chamada da função de inserção
        echo $this->admin_model->edit_user($dados);
    }

    public function contatos()
    {
        $data['total'] = $this->admin_model->count_contato(0);
        return $this->render('contatos', $data);
    }

    public function get_contatos()
    {

        //Conecta ao banco principal
        $db = Database::connect('easy_com_br');

        // realiza a query via dt
        $builder = $db->table('esm_contatos');
        $builder->select("id, nome, email, telefone, condominio, unidades, cidade, estado, status, DATE_FORMAT(cadastro,'%d/%m/%Y') AS data");
        $builder->orderBy("cadastro DESC");

        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->db->db = $db;

        // using CI4 Builder
        $dt->query($builder);
        $dt->edit('cidade', function ($data) {
            return $data['cidade'] . '/' . $data['estado'];
        });

        $dt->edit('condominio', function ($data) {
            return $data['condominio'] . ' (' . $data['unidades'] . ')';
        });

        // inclui actions
        $dt->add('action', function ($data) {
            if ($data['status'] == 1)
                return '<a href="#" class="action-readed" data-id="' . $data['id'] . '"><i class="far fa-eye-slash" title="Marcar como não respondido"></i></a>';
            else
                return '<a href="#" class="action-readed" data-id="' . $data['id'] . '"><i class="fas fa-eye" title="Marcar como respondido"></i></a>';
        });

        $dt->edit('status', function ($data) {
            if ($data['status'] == 1)
                return "<span class=\"badge badge-success\">Respondido</span>";
            else
                return "<span class=\"badge badge-danger\">Responder</span>";
        });

        // gera resultados
        echo $dt->generate();
    }
    public function set_contact_state()
    {
        // pega id do post
        $id = $this->input->getPost('id');

        // altera status do log
        $return = $this->admin_model->change_contact_state($id);

        // retorna json
        echo json_encode($return);
    }
    public function get_centrais()
    {
        // realiza a query via dt
        $dt = $this->datatables->query("
        SELECT 
            esm_condominios_centrais.nome as DT_RowId, 
            esm_condominios_centrais.nome, 
            esm_condominios_centrais.modo, 
            esm_condominios.nome AS condo, 
            esm_condominios_centrais.simcard, 
            esm_condominios.tabela, 
            esm_condominios_centrais.auto_ok, 
            esm_central_data.hardware,
            esm_central_data.software,
            esm_central_data.fonte,
            esm_central_data.tensao,
            esm_central_data.fraude_hi,
            esm_central_data.fraude_low
        FROM esm_condominios_centrais 
        JOIN esm_condominios ON esm_condominios.id = esm_condominios_centrais.condo_id
        LEFT JOIN esm_central_data ON esm_central_data.nome = esm_condominios_centrais.nome AND esm_central_data.timestamp = esm_condominios_centrais.ultimo_envio
        ORDER BY esm_condominios_centrais.nome
    ");

        $dt->edit('modo', function ($data) {

            if ($data['modo'] == 'Master')
                return '<span class="badge badge-success">Master</span>';
            if ($data['modo'] == 'Slave')
                return '<span class="badge badge-warning">Slave</span>';
            if ($data['modo'] == 'Unica')
                return '<span class="badge badge-agua">Única</span>';

            return '';
        });

        $dt->add('alimentacao', function ($data) {
            if (is_null($data['fonte'])) {
                return '-';
            }
            return '<i class="mr-2 fas ' . ($data['fonte'] == "R" ? 'fa-bolt text-success' : 'fa-car-battery text-danger') . '"></i>' . number_format($data['tensao'] / 10, 1, ",", "");
        });

        $dt->add('fraude', function ($data) {
            if (is_null($data['fraude_hi'])) {
                return '-';
            }
            return '<i class="fas fa-user-secret ' . ($data['fraude_hi'] == "000.000.000.000" && $data['fraude_low'] == "000.000.000.000" ? 'text-muted' : 'text-danger') . '"></i>';
        });

        $dt->add('ultima', function ($data) {
            return $this->admin_model->get_ultima_leitura($data['nome'], $data['tabela']);
        });

        $dt->add('versao', function ($data) {
            if (is_null($data['hardware'])) {
                return '-/-';
            }
            return number_format($data['hardware'] / 100, 2) . '/' . number_format($data['software'] / 100, 2);
        });

        // inclui status
        $dt->add('status', function ($data) {
            if ($data['auto_ok'] > time()) {
                return '<i class="fas fa-tint-slash text-danger" title="Auto OK ativo"></i>';
            }

            $leitura = $this->admin_model->get_last_leitura($data['nome'], $data['tabela']);

            if ($leitura == 0)
                $status = 'text-muted';
            elseif ($leitura > time() - 3600)
                $status = 'text-success';
            elseif ($leitura > time() - 3600 * 2)
                $status = 'text-warning';
            else
                $status = 'text-danger';

            return '<i class="fas fa-circle ' . $status . '"></i>';
        });

        // inclui actions
        $dt->add('actions', function ($data) {
            return '<a class="dropdown-item action-view" href="' . site_url('/admin/centrais/' . $data['DT_RowId']) . '"><i class="fas fa-eye mr-2" title="Visualizar"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }
    public function get_postagens()
    {
        $db2 = Database::connect('easy_com_br');

        //Query builder
        $builder = $db2->table('post');
        $builder->select("id, HEX(LEFT(text, 4)) AS central, DATE_FORMAT(stamp, '%d/%m/%Y %H:%i:%s') AS data, CONCAT(FORMAT(LENGTH(text), 0, 'de_DE'), ' B') AS tamanho ");
        $builder->where('stamp > NOW() - INTERVAL 2 HOUR');
        $builder->orderBy('stamp DESC');

        // realiza a query via dt
        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->db->db = $db2;
        $dt->query($builder);
        
        // gera resultados
        echo $dt->generate();
    }
    public function get_central_detail($central)
    {
        if (substr($central, 0, 2) == "43" || substr($central, 0, 2) == "53" || substr($central, 0, 2) == "63") {
            $order = "esm_medidores.posicao";
        } else {
            $order = "esm_medidores.id, esm_medidores.posicao";
        }

        // // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT
                LPAD( esm_medidores.id, 6, '0' ) AS id,
            IF
                (
                    esm_medidores.posicao < 1276313600,
                    esm_medidores.posicao,
                HEX( esm_medidores.posicao )) AS posicao,
                IFNULL( esm_medidores.sensor_id, '-' ) AS sensor,
                esm_medidores.tipo,
                esm_medidores.fator,
                ROUND( esm_medidores.ultima_leitura ) AS leitura,
                esm_entradas.nome AS entrada,
                esm_unidades.nome AS unidade,
                esm_unidades.tipo AS unidade_tipo,
                esm_unidades.id AS u_id,
                esm_agrupamentos.nome AS agrupamentos,
                esm_entradas.entidade_id,
                esm_medidores.horas_consumo,
                ROUND( esm_medidores.consumo_horas, 0 ) AS consumo_horas,
                esm_central_data.fraude_hi,
                esm_central_data.fraude_low 
            FROM
                esm_medidores
                JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
                JOIN esm_condominios_centrais ON esm_condominios_centrais.nome = esm_medidores.central
                LEFT JOIN esm_central_data ON esm_central_data.nome = esm_medidores.central 
                AND esm_central_data.TIMESTAMP = esm_condominios_centrais.ultimo_envio 
            WHERE
                esm_medidores.central = '$central' 
                AND esm_medidores.posicao > 0 
            ORDER BY $order
        ");

        $dt->edit('unidade', function ($data) {
            if (is_null($data['agrupamentos']))
                return $data['unidade'];
            else
                return "{$data['agrupamentos']}/{$data['unidade']}";
        });

        $dt->edit('tipo', function ($data) {
            if ($data['tipo'] == 'agua')
                return '<span class="badge badge-agua">Água</span>';
            elseif ($data['tipo'] == 'gas')
                return '<span class="badge badge-gas">Gás</span>';
            elseif ($data['tipo'] == 'energia')
                return '<span class="badge badge-energia">Energia</span>';
            elseif ($data['tipo'] == 'nivel')
                return '<span class="badge badge-nivel" style="background: #5bc0de;color: #FFF;">Nível</span>';
            else
                return '';
        });

        $dt->edit('sensor', function ($data) use ($central) {
            if (substr($central, 0, 2) == "53" || substr($central, 0, 2) == "63") {
                $valores = $this->admin_model->get_bateria($data['id']);
                return "<span class='inlinebar'>$valores</span>";
            } else {
                return strtoupper(dechex(intval($data['sensor'])));
            }
        });

        $dt->add('consumo', function ($data) {
            return $data['horas_consumo'] . "h/" . $data['consumo_horas'] . " L";
        });

        $dt->add('fraude', function ($data) {
            if (is_null($data['fraude_low']) || $data['fraude_low'] == 0)
                return "-";

            if ($data['posicao'] < 32)
                $f = explode(".", $data['fraude_low']);
            else
                $f = explode(".", $data['fraude_hi']);

            $x = ($f[0] * 16777216) + ($f[1] * 65536) + ($f[2] * 256) + $f[3];

            if ($x & (1 << $data['posicao'] - 1))
                return '<i class="fas fa-user-secret text-danger"></i>';
            else
                return '<i class="fas fa-user-secret text-muted"></i>';
        });

        $dt->add('actions', function ($data) {

            return '<a href="' . site_url('/admin/unidades/' . $data['u_id'] . '/' . $data['entidade_id']) . '" target="_blank" class="" title="Visualizar Consumo"><i class="fas fa-tint"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }
    public function get_central_envios($central)
    {
        $db2 = Database::connect('easy_com_br');

       
        // realiza a query via dt
        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->db->db = $db2;
        
        if (substr($central, 0, 2) == "53" || substr($central, 0, 2) == "43" || substr($central, 0, 2) == "63") {
            // realiza a query via dt

            $builder = $db2->table('post');
            $builder->select("id, 
            id as DT_RowId, 
            DATE_FORMAT(stamp, '%d/%m/%Y %H:%i:%s') as data, 
            CONCAT(FORMAT(LENGTH(text), 0, 'de_DE'), ' B') AS tamanho,
            returned");
            $builder->where("LEFT(text, 4)  = UNHEX('$central')");
            $builder->orderBy('stamp DESC');

        } else {

            $builder = $db2->table('post_raw');
            $builder->select("id, id as DT_RowId, DATE_FORMAT(stamp, '%d/%m/%Y %H:%i:%s') as data, CONCAT(FORMAT(LENGTH(payload), 0, 'de_DE'), ' B') AS tamanho, answer AS returned");
            $builder->where("device = '$central' AND origin = 'data'");
            $builder->orderBy('stamp DESC');
        }

        $dt->query($builder);
        // gera resultados
        echo $dt->generate();
    
    }
    public function md_envio()
    {
        $id = $this->input->getPost('id');
        $central = $this->input->getPost('central');

        if (substr($central, 0, 2) == "53" || substr($central, 0, 2) == "43" || substr($central, 0, 2) == "63") {
            $post = $this->admin_model->get_post($id);
        } else {
            $post = $this->admin_model->get_data_raw($id);
        }

        $data['id'] = $id;
        $data['central'] = $central;
        $data['hex'] = $this->hex_dump($post->text, "<br/>");
        if (substr($central, 0, 2) == "53" || substr($central, 0, 2) == "43" || substr($central, 0, 2) == "63") {
            $data['dec'] = $this->post_dump($post->text, $central);
        } else {
            $data['dec'][0] = "TODO";
        }

        $data['hea'] = $post->header;

        return view('admin/modals/envio', $data);
    }    
    private function hex_dump($data, $newline = "\n")
    {
        static $from = '';
        static $to = '';
        static $width = 16;   // number of bytes per line
        static $pad = '.';  // padding for non-visible characters

        if ($from === '') {
            for ($i = 0; $i <= 0xFF; $i++) {
                $from .= chr($i);
                $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
            }
        }

        $hex = str_split(bin2hex($data), $width * 2);
        $chars = str_split(strtr($data, $from, $to), $width);
        $offset = 0;

        $ret = "";
        foreach ($hex as $i => $line) {
            $ret .= sprintf('%04X', $offset) . ' : ' . implode(' ', str_split($line, 2)) . $newline;
            $offset += $width;
        }

        return $ret;
    }

    private function post_dump($post, $central)
    {
        $ret = '';
        $count = "";

        if (substr($central, 0, 2) == "53" || substr($central, 0, 2) == "63") {

            $d = unpack("H8central/V1stamp/", $post);

            $ret = 'Timestamp Central: ' . $d['stamp'] . ' - ' . date('d/m/Y H:i:sP', $d['stamp']) . '</br>';

            $records = str_split(substr($post, 8), 18);
            $contador = [];

            // salva cada medidor de cada registro
            foreach ($records as $rec) {

                if (strlen($rec) == 18) {

                    // extrai timestamp e medidas
                    $medidas = unpack("h8medidor/v1battery/V1stamp/V1conta/V1contb/", $rec);

                    $ret .= '--------------------------------------------------------------<br/>';
                    $ret .= "<b>Medidor " . strtoupper(strrev($medidas['medidor'])) . '</b></br>';
                    $ret .= date('d/m/Y H:i:sP', $medidas['stamp']) . '</br>';
                    $ret .= "Bateria: " . number_format($medidas['battery'] * 4 / 1023, 2, ",", "") . 'v</br>';
                    $ret .= $medidas['conta'] . '<br>';

                    if (array_key_exists(strrev($medidas['medidor']), $contador))
                        $contador[strrev($medidas['medidor'])]++;
                    else
                        $contador[strrev($medidas['medidor'])] = 1;
                } else {
                    $ret .= "ERROR<br/>";
                }
            }

            $ret .= '--------------------------------------------------------------<br/>';

            ksort($contador);
            $count = '<div style="font-family: monospace;">';
            foreach ($contador as $key => $value) {
                $count .= strtoupper($key) . ': ' . $value . '</br>';
            }
            $count .= '</div>';
        } else {

            $central_count = count($this->admin_model->get_medidores_central($central));
            // central mandando 64 posições, mas menos medidores cadastrados...falta lucas configurar
            if (in_array(strtolower($central), array(
                '43000101', '43000102', '43000104', '43000105', '43000106', '43000107', '43000108', '43000109',
                '4300010a', '4300010b', '4300010d', '4300010e', '4300010f',
                '43000110', '43000111', '43000112', '43000113', '43000114', '43000115', '43000116', '43000117', '43000118', '43000119',
                '4300011a', '4300011b', '4300011c', '4300011d', '4300011e', '4300011f',
                '43000120', '43000121', '43000122', '43000123', '43000124', '43544c58', '43544c59', '43000301', '43001901'
            ))) {
                $central_count = 64;
            }


            $records = str_split(substr($post, 4), ($central_count + 1) * 4);

            $ret = 'Registros: ' . count($records) . '<br>';

            foreach ($records as $key => $value) {
                $rec = unpack("V1stamp/V*/", $value);

                $ret .= '<br><b>Registro ' . $key . '</b>:<br>';
                foreach ($rec as $key => $value) {
                    if ($key == 'stamp')
                        $ret .= 'Timestamp: ' . $value . ' - ' . date('d/m/Y H:i:sP', $value) . '</br>';
                    else
                        $ret .= str_pad($key, 2, "0", STR_PAD_LEFT) . ' - ' . $value . '<br>';
                }
            }
        }

        return array($ret, $count);
    }
}