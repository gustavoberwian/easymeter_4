<?php

namespace App\Controllers;

use App\Models\Shopping_model;
use App\Models\Energy_model;
use App\Models\Water_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Shopping extends UNO_Controller
{
    protected $input;
    protected Datatables $datatables;

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

    public $url;

    public function __construct()
    {
        parent::__construct();

        // load requests
        $this->input = \Config\Services::request();
       

        // load models
        $this->shopping_model = new Shopping_model();
        $this->energy_model = new Energy_model();
        $this->water_model = new Water_model();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);

        // set variables
        $this->url = service('uri')->getSegment(1);
        

        if ($this->user->inGroup('superadmin')) {
            $this->user->entity = (object)[];
            $this->user->entity->classificacao = $this->user->page;
        } else if ($this->user->inGroup('admin')) {
            $this->user->entity = $this->shopping_model->get_condo($this->user->type->entity_id);
        } else if ($this->user->inGroup('group')) {
            $this->user->entity = $this->shopping_model->get_condo_by_group($this->user->type->group_id);
        } else if ($this->user->inGroup('unity')) {
            $this->user->entity = $this->shopping_model->get_condo_by_unity($this->user->type->unity_id);
        }
        if(!$this->user->inGroup('superadmin'))
        {
          if ($this->user->inGroup('energia'))
            $this->user->monitoria = 'energy';
        elseif ($this->user->inGroup('agua'))
            $this->user->monitoria = 'water';
        elseif ($this->user->inGroup('gas'))
            $this->user->monitoria = 'gas';
        elseif ($this->user->inGroup('nivel'))
            $this->user->monitoria = 'nivel';  
        }
        
    }

    public function index()
    {
        $this->setHistory('Acesso à página inicial', 'acesso');

        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->user->monitoria;

        if ($this->user->inGroup('shopping', 'admin')) {

            $data['entity_id'] = $this->user->type->entity_id;
            $data['groups'] = $this->shopping_model->get_groups_by_entity($this->user->type->entity_id);

            foreach ($data['groups'] as $grp) {
                if ($this->user->inGroup('energia')) {
                    $data['overall_c'][] = $this->energy_model->GetOverallConsumption(1, $grp->agrupamento_id);
                    $data['overall_l'][] = $this->energy_model->GetOverallConsumption(2, $grp->agrupamento_id);
                } else if ($this->user->inGroup('agua')) {
                    $data['overall_c'][] = $this->water_model->GetOverallConsumption(1, $grp->agrupamento_id);
                    $data['overall_l'][] = $this->water_model->GetOverallConsumption(2, $grp->agrupamento_id);
                } else {
                    $data['overall_c'][] = 0;
                    $data['overall_l'][] = 0;
                }

                $data['area_comum'][] = $this->shopping_model->get_client_config($grp->agrupamento_id)->area_comum;
            }

            // echo "<pre>"; print_r($data); echo "</pre>"; return;

            return $this->render("index", $data);

        } else if ($this->user->inGroup('group', 'shopping')) {

            $group = $this->shopping_model->get_group_by_user($this->user->id);

            return redirect()->to('/shopping/energy/' . $group->agrupamento_id);

        } else if ($this->user->inGroup('unity', 'shopping')) {

            $unidade = $this->shopping_model->get_unity_by_user($this->user->id);
            $group = $this->shopping_model->get_group_by_unity($unidade->id);

            return redirect()->to('shopping/unidade/' . $group->agrupamento_id . '/' . $unidade->id);

        }
    }

    
    public function profile()
    {
        $data['validation'] = \Config\Services::validation();
        $data['session'] = \Config\Services::session();
        $data['set'] = false;
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['emails'] = $this->shopping_model->get_user_emails($this->user->id);
        helper('form');
        $user_id = $this->user->id;
        
        if ($this->user->inGroup('shopping', 'admin'))
        {
            $data['condo'] = $this->shopping_model->get_condo($this->user->type->entity_id);
        } elseif ($this->user->inGroup('group')) {
            $data['condo'] = $this->shopping_model->get_condo_by_group($this->user->type->group_id);
        } elseif ($this->user->inGroup('unity')) {
            $data['condo'] = $this->shopping_model->get_condo_by_unity($this->user->type->unity_id);
        } else {
            $data['condo'] = '';
        }

        



        if ($this->input->getPost()) {
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
                    if ($this->shopping_model->update_avatar($this->user->id, $img)) {
                        $data['error'] = false;
                        }
                         else {
                        //erro e mensagem
                    }
                } else {
                    //erro e mensagem
                }
            } else {


                if($this->input->getPost('password'))
                {
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
                    if (!$this->shopping_model->update_user($user_id, $password, $telefone, $celular, $emails)) {
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

    // TODO: Finalizar página da unidade

    public function unidade($group_id, $unidade_id, $alerta = null)
    {
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['user'] = $this->user;
        $data['url'] = $this->url;

        $data['unidade'] = $this->shopping_model->get_unidade($unidade_id);
        $data['device_groups'] = $this->shopping_model->get_device_groups($group_id, 'energia');

        $data['alerta'] = false;
        $data['faturamento'] = false;
        $data['unidade_id'] = $unidade_id;
        $data['area_comum'] = $this->user->config->area_comum;

        $data['permission'] = $this->get_user_permission($this->user->id);

        if (!is_null($alerta)) {
            if ($alerta === 'faturamentos') {
                $data['faturamento'] = true;
                $data['group_id'] = $group_id;
                $data['group'] = $this->shopping_model->get_group_info($group_id);
                $data['unidades'] = $this->shopping_model->get_unidades($group_id);
                $data['area_comum'] = $this->user->config->area_comum;

                $this->setHistory("Acesso aos faturamentos da unidade $unidade_id do shopping $group_id", 'acesso');

                echo $this->render('faturamentos_unidade', $data);
                return;
            }

            $data['alerta'] = true;

            $this->setHistory("Acesso aos alertas da unidade $unidade_id do shopping $group_id", 'acesso');

            echo $this->render('alertas_unidade', $data);
            return;
        }

        $this->setHistory("Acesso ao consumo da unidade $unidade_id do shopping $group_id", 'acesso');

        echo $this->render('unidade', $data);
    }

    public function energy($group_id = null)
    {
        $data['permission'] = $this->shopping_model->get_user_permission($this->user->id);

        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['area_comum'] = $this->user->config->area_comum;

        $data['unidades'] = $this->shopping_model->get_units($group_id, "energia");
        $data['device_groups'] = $this->shopping_model->get_device_groups($group_id, "energia");

        //echo "<pre>"; print_r($data); echo "</pre>";

        return $this->render('energy', $data);
    }

    public function water($group_id)
    {
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['url'] = $this->url;
        $data['user'] = $this->user;

        $data['unidades'] = $this->shopping_model->get_units($group_id, "agua");
        $data['device_groups'] = $this->shopping_model->get_device_groups($group_id, "agua");

        $data['area_comum'] = $this->user->config->area_comum;

        return $this->render('water', $data);
    }

    public function level($group_id = null)
    {
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        //echo "<pre>"; print_r($data); echo "</pre>";

        return $this->render('level', $data);
    }

    public function gas($group_id = null)
    {
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['unidades'] = $this->shopping_model->get_units($group_id, "gas");
        $data['device_groups'] = $this->shopping_model->get_device_groups($group_id, "gas");

        $data['area_comum'] = $this->user->config->area_comum;

        //echo "<pre>"; print_r($data); echo "</pre>";

        return $this->render('gas', $data);
    }

    public function faturamentos($group_id)
    {
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['monitoria'] = $this->user->monitoria;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);
        $data['unidades'] = $this->shopping_model->get_unidades($group_id);
        $data['area_comum'] = $this->user->config->area_comum;

        return $this->render('faturamentos', $data);
    }

    public function lancamento($type, $group_id, $id)
    {
        $data['url'] = $this->url;
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        if ($type == "energia") {

            $data['group_id'] = $group_id;
            $data['group'] = $this->shopping_model->get_group_info($group_id);
            $data['fechamento'] = $this->energy_model->GetLancamento($id);
            $data['area_comum'] = $this->shopping_model->get_client_config($group_id)->area_comum;

            return $this->render('lancamento_energy', $data);

        } else if ($type == "agua") {

            $data['group_id'] = $group_id;
            $data['group'] = $this->shopping_model->get_group_info($group_id);
            $data['fechamento'] = $this->water_model->GetLancamento($id);
            $data['area_comum'] = $this->shopping_model->get_client_config($group_id)->area_comum;

            return $this->render('lancamento_water', $data);
        }
    }

    public function relatorio($type, $group_id, $fechamento_id, $relatorio_id)
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        $data['url'] = $this->url;
        $data['group_id'] = $group_id;
        $data['shopping'] = $this->shopping_model->GetGroup($group_id);
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        if ($type == "energia") {

            $data['unidade'] = $this->shopping_model->GetFechamentoUnidade("energia", $relatorio_id);
            $data['unidade_id'] =  $this->shopping_model->get_unidade_id($data['unidade']->nome);
            $data['fechamento'] = $this->shopping_model->GetFechamento("energia", $fechamento_id);
            $data['historico'] = $this->shopping_model->GetFechamentoHistoricoUnidade("energia", $data['unidade']->device, $data['fechamento']->cadastro);
            $data['permission'] = $this->user;

            return $this->render('relatorio_energy', $data);

        } else if ($type == "agua") {

            $data['unidade'] = $this->shopping_model->GetFechamentoUnidade("agua", $relatorio_id);
            $data['fechamento'] = $this->shopping_model->GetFechamento("agua", $fechamento_id);
            $data['historico'] = $this->shopping_model->GetFechamentoHistoricoUnidade("agua", $data['unidade']->device, $data['fechamento']->cadastro);

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

    public function alertas($group_id)
    {
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['monitoria'] = $this->user->monitoria;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);
        $data['unidades'] = $this->shopping_model->get_units($group_id);

        return $this->render('alertas', $data);
    }

    public function insights($group_id)
    {
        $data['url'] = $this->url;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);
        $data['monitoria'] = $this->user->monitoria;

        return $this->render('insights', $data);
    }

    public function configuracoes($group_id)
    {
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        $data['monitoria'] = $this->user->monitoria;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);
        $data['unidades'] = $this->shopping_model->get_units($group_id);
        $data['client_config'] = $this->shopping_model->get_client_config($group_id);
        $data['alerts_config'] = $this->shopping_model->get_alert_config($group_id, true);
        $data['token'] = $this->shopping_model->getToken($group_id);

        foreach ($data['alerts_config'] as $c) {
            $data['alerts']['devices']['type-' . $c->type] = $this->shopping_model->get_devices($group_id, $c->type);
            $data['alerts']['config-type-' . $c->type] = $c;
        }

        return $this->render('configs', $data);
    }

    public function users($group_id, $op = null, $user_id = null)
    {
        $data['url'] = $this->url;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['shoppings'] = $this->shopping_model->get_shoppings_by_user($this->user->id);

        $data['readonly'] = false;

        $data['user_info'] = $this->user;

        /*if (!is_null($user_id)) {
            $data['user_info'] = $this->shopping_model->get_user_info($user_id);
        }*/

        // echo "<pre>"; print_r($data); echo "</pre>"; return;

        if ($op === 'edit') {
            return $this->render('user_edit', $data);
        } elseif ($op === 'create') {
            return $this->render('user_add', $data);
        } elseif ($op === 'view') {
            $data['readonly'] = true;
            return $this->render('user_edit', $data);
        } else {
            redirect('shopping/configuracoes/' . $group_id, 'refresh');
        }
    }

    // FUNCTIONS BELOW

    public function get_unidades()
    {
        $group_id = $this->input->getPost("group");
        $type = $this->input->getPost("tipo");

        $query = "SELECT 
                un.id as id,
                un.nome as unidade,
                me.nome as luc,
                IF(unc.type <= 1,(SELECT esm_client_config.area_comum FROM esm_client_config WHERE esm_client_config.agrupamento_id = " . $group_id . "),'Unidades') as subtipo,
                unc.tipo as tipo,
                unc.identificador as identificador,
                unc.localizador as localizador,
                unc.disjuntor as disjuntor,
                unc.faturamento as faturamento
            FROM esm_medidores me
            JOIN esm_unidades un ON un.id = me.unidade_id
            LEFT JOIN esm_unidades_config unc ON unc.unidade_id = un.id
            WHERE un.agrupamento_id = $group_id AND me.tipo = '$type'
            GROUP BY id";

        $dt = $this->datatables->query($query);

        $dt->edit('disjuntor', function ($data) {
            if (is_null($data['disjuntor']))
                return "";
            else
                return $data['disjuntor'] . " A";
        });

        $dt->edit('luc', function ($data) {
            return $data['luc'];
        });

        $dt->edit('subtipo', function ($data) {
            return $data['subtipo'];
        });

        $dt->edit('tipo', function ($data) {
            if ($data['tipo'] === 'iluminacao') {
                return 'Iluminação';
            } elseif ($data['tipo'] === 'ar_condicionado') {
                return 'Ar Condicionado';
            } elseif ($data['tipo'] === 'loja') {
                return 'Loja';
            } elseif ($data['tipo'] === 'quiosque') {
                return 'Quiosque';
            }
        });

        if ($type === 'agua') {
            $dt->hide('tipo');
        }

        $dt->edit('faturamento', function ($data) {
            if ($data['faturamento'] === 'incluir') {
                return 'Incluir';
            } elseif ($data['faturamento'] === 'nao_incluir') {
                return 'Não Incluir';
            }
        });

        $dt->add('actions', function ($data) {
            if ($this->user->inGroup("admin", "shopping") && !$this->user->demo) {
                return '
                    <a href="#" class="hidden on-editing btn-save save-row text-success"><i class="fas fa-save"></i></a>
                                        <a href="#" class="hidden on-editing btn-save cancel-row text-danger"><i
                                                    class="fas fa-times"></i></a>
                                        <a href="#" class="on-default edit-row text-primary"><i class="fas fa-pen"></i></a>
                ';
            } else {
                return '<a href="' . site_url('/shopping/unidades/' . $this->input->getPost('group') . '/view/' . $data['id']) . '" class="action-visualiza text-center text-success"><i class="fas fa-eye me-2"></i></a>';
            }
        });

        // gera resultados
        echo $dt->generate();
    }

    public function GetAlerts()
    {
        $m = $this->input->getPost('monitoramento');
        $group = $this->input->getPost('group');

        $user_id = auth()->user()->id;

        $dvc = 'esm_alertas_' . $m . '.device';
        $join = 'JOIN esm_medidores ON esm_medidores.nome = ' . $dvc;

        $dt = $this->datatables->query("
            SELECT 
                1 AS type, 
                esm_alertas_" . $m . ".tipo, 
                $dvc,
                esm_unidades.nome, 
                esm_alertas_" . $m . ".titulo, 
                esm_alertas_" . $m . ".enviada, 
                0 as actions, 
                IF(ISNULL(esm_alertas_" . $m . "_envios.lida), 'unread', '') as DT_RowClass,
                esm_alertas_" . $m . "_envios.id AS DT_RowId
            FROM esm_alertas_" . $m . "_envios 
            JOIN esm_alertas_" . $m . " ON esm_alertas_" . $m . ".id = esm_alertas_" . $m . "_envios.alerta_id 
            " . $join . " 
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id AND esm_unidades.agrupamento_id = $group
            WHERE
                esm_alertas_" . $m . "_envios.user_id = $user_id AND 
                esm_alertas_" . $m . ".visibility = 'normal' AND 
                esm_alertas_" . $m . "_envios.visibility = 'normal' AND
                esm_alertas_" . $m . ".enviada IS NOT NULL
            ORDER BY esm_alertas_" . $m . ".enviada DESC
        ");

        $dt->edit('type', function ($data) {
            if ($this->input->getPost('monitoramento') === 'energia')
                return "<i class=\"fas fa-bolt text-warning\"></i>";
            elseif ($this->input->getPost('monitoramento') === 'agua')
                return "<i class=\"fas fa-tint text-primary\"></i>";
            elseif ($this->input->getPost('monitoramento') === 'gas')
                return "<i class=\"fas fa-fire text-success\"></i>";
        });

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

    public function ShowAlert()
    {
        // pega o id do post
        $id = $this->input->getPost('id');
        $m = $this->input->getPost('monitoramento');

        // busca o alerta
        $data['alerta'] = $this->shopping_model->GetUserAlert($id, $m, true);

        $data['alerta']->enviada = time_ago($data['alerta']->enviada);

        // verifica e informa erros
        if (!$data['alerta']) {
            return view('modals/erro', array('message' => 'Alerta não encontrado!'));
        }

        // carrega a modal
        return view('modals/alert', $data);
    }

    public function DeleteAlert()
    {
        $id = $this->input->getPost('id');
        $m = $this->input->getPost('monitoramento');

        echo $this->shopping_model->DeleteAlert($id, $m);
    }

    public function ReadAllAlert()
    {
        echo $this->shopping_model->ReadAllAlert(auth()->user()->id, $this->input->getPost('monitoramento'));
    }

    public function get_users()
    {
        $group = $this->input->getPost("group") ?? 0;
        $unity = $this->input->getPost("unity") ?? 0;

        $groupFilter = "";

        if ($this->user->inGroup("shopping")) {
            if ($this->user->inGroup("superadmin")) {
                $groupFilter = '"shopping"';
            } elseif ($this->user->inGroup("admin")) {
                $groupFilter = '"group", "unity"';
            } elseif ($this->user->inGroup("group")) {
                $groupFilter = '"unity"';
            }
        }

        $query = "
            SELECT
                auth_users.id AS id,
                auth_users.username AS name,
                auth_identities.secret AS email
            FROM
                auth_users
            JOIN auth_identities ON auth_identities.user_id = auth_users.id
            JOIN auth_groups_users ON auth_groups_users.user_id = auth_users.id
            JOIN auth_user_relation ON auth_user_relation.user_id = auth_users.id
            WHERE
                auth_identities.type = 'email_password' AND
                auth_groups_users.group IN (" . $groupFilter . ") AND
                auth_user_relation.agrupamento_id = " . $group . " OR !ISNULL(auth_user_relation.unidade_id)
            GROUP BY auth_users.id
        ";

        $dt = $this->datatables->query($query);

        $dt->add('actions', function ($data) {
            if ($this->user->inGroup("admin", "shopping") && !$this->user->demo) {
                return '
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Ver" href="' . site_url('/shopping/users/' . $this->input->getPost('group') . '/view/' . $data['id']) . '" class="action-visualiza text-success"><i class="fas fa-eye me-2"></i></a>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" href="' . site_url('/shopping/users/' . $this->input->getPost('group') . '/edit/' . $data['id']) . '" class="action-access text-primary"><i class="fas fa-pen me-2"></i></a>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excluir" href="#" class="action-delete-user text-danger" data-id="' . $data['id'] . '"><i class="fas fa-trash me-2"></i></a>
                ';
            } else {
                return '<a href="' . site_url('/shopping/users/' . $this->input->getPost('group') . '/view/' . $data['id']) . '" class="action-visualiza text-center text-success"><i class="fas fa-eye me-2"></i></a>';
            }
        });

        // gera resultados
        echo $dt->generate();
    }

    public function get_agrupamentos()
    {
        $group_id = $this->input->getPost('group');
        $type = $this->input->getPost('tipo');

        $query = "
            SELECT
                    esm_device_groups.id as id,
                    esm_unidades.agrupamento_id,
                    esm_device_groups.name AS name
            FROM 
                    esm_device_groups
            JOIN esm_medidores ON esm_medidores.entrada_id = esm_device_groups.entrada_id
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE esm_unidades.agrupamento_id = $group_id AND esm_medidores.tipo = '$type'
            GROUP BY esm_device_groups.name";

        $dt = $this->datatables->query($query);

        $dt->add('unidades', function ($data) {
            $unidades = $this->shopping_model->get_units($this->input->getPost("group"), $this->input->getPost("tipo"));
            $medidores = $this->shopping_model->get_devices_agrupamento($data['id']);

            $return = '<select class="form-control select-medidores" multiple="multiple" id="medidores-agrupamento" name="medidores_agrupamento[]" data-plugin-multiselect data-plugin-options=\'{ "buttonClass": "multiselect dropdown-toggle form-select text-center form-control", "maxHeight": 200, "buttonWidth": "100%", "numberDisplayed": 1, "includeSelectAllOption": true}\' disabled>';

            foreach ($unidades as $u) {
                if ($medidores) {
                    foreach ($medidores as $j => $m) {
                        if ($u['medidor_id'] === $m->dvc) {
                            $return .= '<option selected value="' . $u['medidor_id'] . '">' . $u['unidade_nome'] . '</option>';
                            continue 2;
                        } elseif ($j == array_key_last($medidores)) {
                            $return .= '<option value="' . $u['medidor_id'] . '">' . $u['unidade_nome'] . '</option>';
                        }
                    }
                } else {
                    $return .= '<option value="' . $u['medidor_id'] . '">' . $u['unidade_nome'] . '</option>';
                }
            }

            return $return;
        });

        $dt->add('actions', function ($data) {
            if ($this->user->inGroup("admin", "shopping") && !$this->user->demo) {
                return '
                    <a href="#" class="hidden on-editing btn-save save-row text-success"><i class="fas fa-save"></i></a>
                                        <a href="#" class="hidden on-editing btn-save cancel-row text-danger"><i
                                                    class="fas fa-times"></i></a>
                                        <a href="#" class="on-default edit-row text-primary"><i class="fas fa-pen"></i></a>
                                        <a href="#" class="on-default delete-row text-danger"><i class="fas fa-trash"></i></a>
                ';
            } else {
                return '<a href="' . site_url('/shopping/unidades/' . $this->input->getPost('group') . '/view/' . $data['id']) . '" class="action-visualiza text-center text-success"><i class="fas fa-eye me-2"></i></a>';
            }
        });

        // gera resultados
        echo $dt->generate();
    }

    public function get_alertas_conf()
    {
        $group_id = $this->input->getPost("group");
        $type = $this->input->getPost("tipo");

        $query = "SELECT 
                id,
                agrupamento_id,
                active as status,
                description as alerta,
                null as medidores,
                when_type as quando,
                notify_shopping as shopping,
                notify_unity as unidade,
                type as actions
            FROM esm_alertas_cfg
            WHERE agrupamento_id = $group_id AND subtipo = '$type'";

        $dt = $this->datatables->query($query);

        $dt->edit('status', function ($data) {
            if ($data['status']) {
                return '
                    <div class="switch switch-sm switch-primary disabled">
                        <input type="checkbox" checked class="switch-input" name="active" data-plugin-ios-switch>
                    </div>
                ';
            } else {
                return '
                    <div class="switch switch-sm switch-primary disabled">
                        <input type="checkbox" class="switch-input" name="active" data-plugin-ios-switch>
                    </div>
                ';
            }
        });

        $dt->edit('medidores', function ($data) {
            $medidores = $this->shopping_model->get_devices_alert($this->input->getPost("group"), $data['id']);
            $unidades = $this->shopping_model->get_units($this->input->getPost("group"));

            $return = '<select class="form-control select-medidores" multiple="multiple" id="medidores-type" name="medidores_type[]" data-plugin-multiselect data-plugin-options=\'{ "buttonClass": "multiselect dropdown-toggle form-select text-center form-control", "maxHeight": 200, "buttonWidth": "100%", "numberDisplayed": 1, "includeSelectAllOption": true}\' disabled>';

            foreach ($unidades as $u) {
                if ($medidores) {
                    foreach ($medidores as $j => $m) {
                        if ($u['medidor_id'] === $m->dvc) {
                            $return .= '<option selected value="' . $u['medidor_id'] . '">' . $u['unidade_nome'] . '</option>';
                            continue 2;
                        } elseif ($j == array_key_last($medidores)) {
                            $return .= '<option value="' . $u['medidor_id'] . '">' . $u['unidade_nome'] . '</option>';
                        }
                    }
                } else {
                    $return .= '<option value="' . $u['medidor_id'] . '">' . $u['unidade_nome'] . '</option>';
                }
            }

            $return .= '</select>';

            return $return;
        });

        $dt->edit('quando', function ($data) {
            $return = '<select class="form-control period" id="when-type" name="when_type" disabled>';

            if ($data['quando'] === 'day') {
                if ($data['actions'] != 3) {
                    $return .= '<option selected value="day">No Dia</option>';
                } else {
                    $return .= '
                        <option value="">Selecione</option>
                        <option selected value="day">No Dia</option>
                        <option value="hour">Na Hora</option>
                        <option value="instant">No Instante</option>
                    ';
                }
            } elseif ($data['quando'] === 'hour') {
                if ($data['actions'] != 3) {
                    $return .= '<option selected value="hour">Na Hora</option>';
                } else {
                    $return .= '
                        <option value="">Selecione</option>
                        <option value="day">No Dia</option>
                        <option selected value="hour">Na Hora</option>
                        <option value="instant">No Instante</option>
                    ';
                }
            } elseif ($data['quando'] === 'instant') {
                if ($data['actions'] != 3) {
                    $return .= '<option selected value="instant">No Instante</option>';
                } else {
                    $return .= '
                        <option value="">Selecione</option>
                        <option value="day">No Dia</option>
                        <option value="hour">Na Hora</option>
                        <option selected value="instant">No Instante</option>
                    ';
                }
            } else {
                $return .= '
                    <option value="">Selecione</option>
                    <option value="day">No Dia</option>
                    <option value="hour">Na Hora</option>
                    <option value="instant">No Instante</option>
                ';
            }


            $return .= '</select>';

            return $return;
        });

        $dt->edit('unidade', function ($data) {
            if ($data['unidade']) {
                return '
                    <div class="switch switch-sm switch-primary disabled">
                        <input type="checkbox" class="switch-input" checked name="notify_unity" data-plugin-ios-switch>
                    </div>
                ';
            } else {
                return '
                    <div class="switch switch-sm switch-primary disabled">
                        <input type="checkbox" class="switch-input" name="notify_unity" data-plugin-ios-switch>
                    </div>
                ';
            }
        });

        $dt->edit('shopping', function ($data) {
            if ($data['shopping']) {
                return '
                    <div class="switch switch-sm switch-primary disabled">
                        <input type="checkbox" class="switch-input" checked name="notify_shopping" data-plugin-ios-switch>
                    </div>
                ';
            } else {
                return '
                    <div class="switch switch-sm switch-primary disabled">
                        <input type="checkbox" class="switch-input" name="notify_shopping" data-plugin-ios-switch>
                    </div>
                ';
            }
        });

        $dt->edit('actions', function ($data) {
            if ($this->user->inGroup("admin", "shopping") && !$this->user->demo) {
                return '
                    <a href="#" class="hidden on-editing btn-save save-row text-success"><i class="fas fa-save"></i></a>
                                        <a href="#" class="hidden on-editing btn-save cancel-row text-danger"><i
                                                    class="fas fa-times"></i></a>
                                        <a href="#" class="on-default edit-row text-primary"><i class="fas fa-pen"></i></a>
                ';
            } else {
                return '<a href="' . site_url('/shopping/unidades/' . $this->input->getPost('group') . '/view/' . $data['id']) . '" class="action-visualiza text-center text-success"><i class="fas fa-eye me-2"></i></a>';
            }
        });

        // gera resultados
        echo $dt->generate();
    }

    public function edit_client_conf()
    {
        foreach ($this->input->getPost() as $i => $post) {
            if ($post) {
                if ($i === 'group_id') {
                    $dados[$i] = $post;
                    $this->setHistory("Configuração do shopping $post alterada", 'ação');
                } elseif ($i === 'area_comum') {
                    $dados['tabela']['esm_client_config'][$i] = $post;
                } elseif ($i === 'split_report') {
                    $dados['tabela']['esm_client_config'][$i] = 1;
                } else {
                    $dados['tabela']['esm_client_config'][$i] = strtotime('01-01-1970 ' . $post);
                }
            }
        }

        if (!$this->input->getPost('split_report')) {
            $dados['tabela']['esm_client_config']['split_report'] = 0;
        }

        if ($this->shopping_model->edit_client_conf($dados)) {
            echo json_encode(array('status' => 'success', 'message' => 'Configurações gerais alteradas com sucesso'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
        }
    }

    public function generateToken()
    {
        $group_id = $this->input->getPost("group_id");

        $token = md5(strtotime(date("Y-m-d H:i:s")) . $group_id);

        if ($this->shopping_model->insertToken($token, $group_id)) {
            echo json_encode(array('status' => 'success', 'message' => 'Operação finalizada com sucesso', 'token' => $token));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
        }
    }

    public function get_unidades_select()
    {
        $group_id = $this->input->getPost('group');
        $type = $this->input->getPost('tipo');

        //$this->setHistory("Requisição para buscar unidades de $type do shopping $group_id", 'requisição');

        $unidades = $this->shopping_model->get_units($group_id, $type);

        $data = array();

        foreach ($unidades as $unidade) {
            $data['options'][] = $unidade['medidor_id'];
            $data['_options'][] = $unidade['unidade_nome'];
        }

        echo json_encode($data);
    }

    public function edit_agrupamentos()
    {
        $dados = array();

        if ($this->input->getPost("group_name")) {
            $dados['name'] = $this->input->getPost("group_name");
        }
        foreach ($this->input->getPost("select_unidades") as $m) {
            $dados['devices'][] = $m;
        }

        if ($this->input->getPost("entrada_id")) {
            $dados['entrada_id'] = $this->input->getPost("entrada_id") === 'energia' ? 72 : 73;
        }

        if ($this->input->getPost("id")) {
            $dados['id'] = $this->input->getPost("id");

            if ($this->shopping_model->edit_agrupamento($dados)) {
                echo json_encode(array('status' => 'success', 'message' => 'Agrupamento alterado com sucesso'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
            }
        } else {
            $this->setHistory("Agrupamento criado", 'ação');

            if ($this->shopping_model->add_agrupamento($dados)) {
                echo json_encode(array('status' => 'success', 'message' => 'Agrupamento criado com sucesso'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
            }
        }
    }

    public function delete_agrupamento()
    {
        if ($this->shopping_model->delete_agrupamento($this->input->getPost("id"))) {
            echo json_encode(array('status' => 'success', 'message' => 'Agrupamento excluído com sucesso'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
        }
    }

    public function get_subtipo_cliente_config()
    {
        echo $this->shopping_model->get_subtipo_cliente_config($this->input->getPost("group"), $this->input->getPost("uid"));
    }

    public function edit_unidade()
    {
        $dados = array();

        foreach ($this->input->getPost() as $index => $post) {

            if ($index === "id") {
                $dados['unidade_id'] = $post;
            } elseif ($index === "entrada_id") {
                $dados['tipo'] = $post;
            } elseif ($index === "group_id") {
                $dados['tabela']['esm_unidades']['nome'] = $post;
            } elseif ($index === "luc") {
                $dados['tabela']['esm_medidores']['luc'] = $post;
            } elseif ($index === "subtipo") {
                $dados['tabela']['esm_unidades_config']['type'] = $post;
            } elseif ($index === "tipo") {
                $dados['tabela']['esm_unidades_config']['tipo'] = $post;
            } elseif ($index === "identificador") {
                $dados['tabela']['esm_unidades_config']['identificador'] = $post;
            } elseif ($index === "localizador") {
                $dados['tabela']['esm_unidades_config']['localizador'] = $post;
            } elseif ($index === "capacidade") {
                $dados['tabela']['esm_unidades_config']['disjuntor'] = intval($post);
            } elseif ($index === "faturamento") {
                $dados['tabela']['esm_unidades_config']['faturamento'] = $post;
            }

        }

        if ($this->shopping_model->edit_unidade($dados)) {
            echo json_encode(array('status' => 'success', 'message' => 'Unidade alterada com sucesso'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
        }
    }

    public function edit_alert_conf()
    {
        $dados = array();

        if ($this->input->getPost('config_id')) {
            $dados['config_id'] = $this->input->getPost('config_id');
        }
        if ($this->input->getPost('group_id')) {
            $dados['group_id'] = $this->input->getPost('group_id');
        }
        if ($this->input->getPost('when_type')) {
            $dados['esm_alertas_cfg']['when_type'] = $this->input->getPost('when_type');
        }
        if ($this->input->getPost('active')) {
            $dados['esm_alertas_cfg']['active'] = $this->input->getPost('active');
        } else {
            $dados['esm_alertas_cfg']['active'] = 0;
        }
        if ($this->input->getPost('notify_shopping')) {
            $dados['esm_alertas_cfg']['notify_shopping'] = $this->input->getPost('notify_shopping');
        } else {
            $dados['esm_alertas_cfg']['notify_shopping'] = 0;
        }
        if ($this->input->getPost('notify_unity')) {
            $dados['esm_alertas_cfg']['notify_unity'] = $this->input->getPost('notify_unity');
        } else {
            $dados['esm_alertas_cfg']['notify_unity'] = 0;
        }
        $dados['esm_alertas_cfg_devices'] = false;
        if ($this->input->getPost('medidores_type')) {
            foreach ($this->input->getPost('medidores_type') as $m) {
                $dados['esm_alertas_cfg_devices'][] = $m;
            }
        }

        if ($this->shopping_model->edit_alert_conf($dados)) {
            echo json_encode(array('status' => 'success', 'message' => 'Configurações dos alertas alteradas com sucesso'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
        }
    }

    public function delete_user()
    {
        $user = $this->input->getPost('user');

        if ($this->shopping_model->delete_user($user)) {
            echo json_encode(array('status' => 'success', 'message' => 'Usuário removido com sucesso'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Falha na operação, tente novamente em instantes'));
        }
    }

    public function get_lojas()
    {
        $group_id = $this->input->getPost('shopping_id');

        $dados = $this->shopping_model->get_lojas_by_shopping($group_id);

        echo json_encode($dados);
    }

    public function get_user_permission($uid)
    {
        return $this->shopping_model->get_user_permission($uid);
    }

    public function md_profile_image_edit()
    {

        return view('shopping/modals/md_profile_image_edit');
    }
}