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
    //         $data['blocos'] = $this->admin_model->get_groups($data['condo']->id);

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
    //                 $data['unidade_geral'] = $this->painel_model->get_unidade_medidor_principal($param1);
    //                 $data['central_geral'] = $this->painel_model->get_central_by_unidade($data['unidade_geral'])->central;
    //                 $data['primeira_leitura'] = date("d/m/Y", $this->painel_model->get_primeira_leitura($data['condo']->tabela, 'agua', $data['unidade_geral']));

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

            $data['usuario']           = $user;
            $data['id']             = $param1;
            $data['email']          = $groups['email'];
            $data['classificacao']  = $groups['classificacao'];
            $data['groups']         = $this->admin_model->get_groups_for_user($param1);



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
                    $celular  = $this->input->getPost('celular');


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
                esm_entidades.visibility = 'normal'" . $inactives . $mode
        );
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

            $data['replys']  = $this->admin_model->get_chamado_reply($id);
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
            $email->setTo('gabrieleduardowagener@gmail.com');
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

    public function add_ramal()
    {
        $dados['nome']          = $this->input->getPost('nome-ramal') ?? '';
        $dados['tipo']          = $this->input->getPost('tipo-ramal') ?? '';
        $dados['entidade_id']   = $this->input->getPost('sel-entity') ?? '';

        echo $this->admin_model->add_ramal($dados);
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

    public function edit_entity()
    {
        $dados['id']             = $this->input->getPost('id-entity');
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
        $dados['gestor_id']      = $this->input->getPost('select-gestor');


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
        $central = $this->input->getPost('id');
        $porta = $this->input->getPost('porta');

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
                // atualiza bloco na tabela blocos
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
                // atualiza bloco na tabela blocos
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
        return json_encode(array(
            'status' => 'success',
            'message' => 'Seus dados foram atualizados com sucesso.'
        ));
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
            'email'    => $this->input->getPost('email-user') ?? '',
            'password' => $this->input->getPost('senha-user') ?? '',
            'active'   => $this->input->getPost('switch') === 'on' ? 1 : 0
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
        $dados['user_id']  = $this->input->getPost('id-user');

        $user = $users->findById($dados['user_id']);

        //Edita usuário
        $user->fill([
            'username' => $this->input->getPost('nome-user') ?? '',
            'email'    => $this->input->getPost('email-user') ?? '',
            'password' => $this->input->getPost('senha-user') ?? '',
            'active'   => $this->input->getPost('switch') === 'on' ? 1 : 0
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
        $dados['groups-user'] =  array_map('trim', explode(",", $this->input->getPost('groups-user') ?? ''));

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
}
