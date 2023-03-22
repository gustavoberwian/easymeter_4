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

    public function alertas($group_id)
    {
        $data['user']       = $this->user;
        $data['group_id']   = $group_id;
        $data['group']      = $this->shopping_model->get_group_info($group_id);
        $data['unidades']   = $this->shopping_model->get_units($group_id);

        return $this->render('alertas', $data);
    }

    public function insights($group_id)
    {
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        return $this->render('insights', $data);
    }

    public function configuracoes($group_id)
    {
        $data['user'] = $this->user;
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);
        $data['unidades'] = $this->shopping_model->get_units($group_id);
        $data['client_config'] = $this->shopping_model->get_client_config($this->user->group);
        $data['alerts_config'] = $this->shopping_model->get_alert_config($group_id, true);
        $data['token'] = $this->shopping_model->getToken($group_id);

        foreach ($data['alerts_config'] as $c) {
            $data['alerts']['devices']['type-'.$c->type] = $this->shopping_model->get_devices($group_id, $c->type);
            $data['alerts']['config-type-'.$c->type] = $c;
        }

        return $this->render('configs', $data);
    }

    public function users($group_id, $op = null, $user_id = null)
    {
        $data['group_id'] = $group_id;
        $data['group'] = $this->shopping_model->get_group_info($group_id);

        $data['shoppings'] = $this->shopping_model->get_shoppings_by_user($this->user->id);

        $data['readonly'] = false;

        $data['user'] = $this->user;

        if (!is_null($user_id)) {
            $data['user_info'] = $this->shopping_model->get_user_info($user_id);
        }

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
                unc.luc as luc,
                IF(unc.type <= 1,(SELECT esm_client_config.area_comum FROM esm_client_config WHERE esm_client_config.group_id = ".$group_id."),'Unidades') as subtipo,
                unc.tipo as tipo,
                unc.identificador as identificador,
                unc.localizador as localizador,
                unc.disjuntor as disjuntor,
                unc.faturamento as faturamento
            FROM esm_medidores me
            JOIN esm_unidades un ON un.id = me.unidade_id
            JOIN esm_unidades_config unc ON unc.unidade_id = un.id
            WHERE un.bloco_id = $group_id AND me.tipo = '$type'
            GROUP BY id" ;

        $dt = $this->datatables->query($query);

        $dt->add('disjuntor', function ($data) {
            if (is_null($data['disjuntor']))
                return "";
            else
                return $data['disjuntor']." A";
        });

        $dt->add('luc', function ($data) {
            return $data['luc'];
        });

        $dt->add('subtipo', function ($data) {
            return $data['subtipo'];
        });

        $dt->add('tipo', function ($data) {
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

        $dt->add('faturamento', function ($data) {
            if ($data['faturamento'] === 'incluir') {
                return 'Incluir';
            } elseif ($data['faturamento'] === 'nao_incluir') {
                return 'Não Incluir';
            }
        });

        $dt->add('actions', function ($data) {
            if ($this->user->inGroup("admin", "shopping")){
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
        $monitoramento = $this->input->getPost('monitoramento');

        $user_id = auth()->user()->id;

        $m = "";
        $dvc = 'esm_alertas'.$m.'.medidor_id';
        $join = 'JOIN esm_medidores ON esm_medidores.id = ' . $dvc;
        if ($monitoramento === 'energia') {
            $m = '_' . $monitoramento;
            $dvc = 'esm_alertas'.$m.'.device';
            $join = 'JOIN esm_medidores ON esm_medidores.nome = ' . $dvc;
        }

        $dt = $this->datatables->query("
            SELECT 
                1 AS type, 
                esm_alertas".$m.".tipo, 
                $dvc,
                esm_unidades.nome, 
                esm_alertas".$m.".titulo, 
                esm_alertas".$m.".enviada, 
                0 as actions, 
                IF(ISNULL(esm_alertas".$m."_envios.lida), 'unread', '') as DT_RowClass,
                esm_alertas".$m."_envios.id AS DT_RowId
            FROM esm_alertas".$m."_envios 
            JOIN esm_alertas".$m." ON esm_alertas".$m.".id = esm_alertas".$m."_envios.alerta_id 
            " . $join . " 
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE
                esm_alertas".$m."_envios.user_id = $user_id AND 
                esm_alertas".$m.".visibility = 'normal' AND 
                esm_alertas".$m."_envios.visibility = 'normal' AND
                esm_alertas".$m.".enviada IS NOT NULL
            ORDER BY esm_alertas".$m.".enviada DESC
        ");

        $dt->edit('type', function ($data) {
            if ($this->input->getPost('monitoramento') === 'energia')
                return "<i class=\"fas fa-bolt text-warning\"></i>";
            elseif ($this->input->getPost('monitoramento') === 'agua')
                return "<i class=\"fas fa-tint text-warning\"></i>";
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
        //$this->setHistory("Requisição para buscar usuários shopping ".$this->input->post('group'), 'requisição');

        if ($this->user->inGroup("admin", "shopping")) {
            $groups = "(34, 35, 36)";
        } else {
            $groups = "(35, 36)";
        }
        $query = "
            SELECT
                users.id AS id,
                users.username AS name,
                auth_identities.secret AS email
            FROM
                users
            JOIN auth_identities ON auth_identities.user_id = users.id
            WHERE auth_identities.type = 'email_password'
        ";

        $dt = $this->datatables->query($query);

        $dt->add('actions', function ($data) {
            if ($this->user->inGroup("admin", "shopping")){
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
                    esm_unidades.bloco_id,
                    esm_device_groups.name AS name
            FROM 
                    esm_device_groups
            JOIN esm_medidores ON esm_medidores.entrada_id = esm_device_groups.entrada_id
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE esm_unidades.bloco_id = $group_id AND esm_medidores.tipo = '$type'
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
            if ($this->user->inGroup("admin", "shopping")){
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
                group_id,
                active as status,
                description as alerta,
                null as medidores,
                when_type as quando,
                notify_shopping as shopping,
                notify_unity as unidade,
                type as actions
            FROM esm_alertas_cfg
            WHERE group_id = $group_id AND subtipo = '$type'" ;

        $dt = $this->datatables->query($query);

        $dt->add('status', function ($data) {
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

        $dt->add('medidores', function ($data) {
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

        $dt->add('quando', function ($data) {
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

        $dt->add('unidade', function ($data) {
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

        $dt->add('shopping', function ($data) {
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

        $dt->add('actions', function ($data) {
            if ($this->user->inGroup("admin", "shopping")){
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
}