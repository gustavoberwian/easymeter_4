<?php

namespace App\Controllers;

use Config\Database;
use App\Models\Consigaz_model;
use App\Models\Gas_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PragmaRX\Google2FA\Google2FA;

class Consigaz extends UNO_Controller
{
    protected $input;
    protected Datatables $datatables;

    /**
     * @var Consigaz_model
     */
    private Consigaz_model $consigaz_model;

    /**
     * @var Gas_model
     */
    private Gas_model $gas_model;

    public $url;

    public function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        // load requests
        $this->input = \Config\Services::request();

        // load models
        $this->consigaz_model = new Consigaz_model();
        $this->gas_model = new Gas_model();

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

        $total_mes_atual = 0;
        $total_mes_anterior = 0;

        $medidores = $this->consigaz_model->get_medidores_by_user($this->user->id);

        foreach ($medidores as $medidor) {
            $consumo_mes_atual = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
            foreach ($consumo_mes_atual as $c) {
                $total_mes_atual += $c->value;
            }

            $consumo_mes_anterior = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);
            foreach ($consumo_mes_anterior as $c) {
                $total_mes_anterior += $c->value;
            }
        }

        $data['consumo']['mes_atual'] = number_format($total_mes_atual, 0, '', '.') . ' <small>m³</small>';
        $data['consumo']['ultimo_mes'] = number_format($total_mes_anterior, 0, '', '.') . ' <small>m³</small>';

        $data['valvulas']['abertas'] = $this->consigaz_model->get_valvulas(null, 'open', 'count');
        $data['valvulas']['fechadas'] = $this->consigaz_model->get_valvulas(null, 'close', 'count');
        $data['valvulas']['erros'] = $this->consigaz_model->get_valvulas(null, 'vermelho', 'count');

        $data['alertas']['vazamentos'] = $this->gas_model->get_alertas_by_user($this->user->id, 'vazamento', 'count');
        $data['alertas']['outros'] = $this->gas_model->get_alertas_by_user($this->user->id, null, 'count');

        return $this->render("index", $data);
    }

    public function unidades()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['clientes'] = $this->consigaz_model->get_clientes_by_user($this->user->id);

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

    public function fechamentos($fechamento = null, $entidade = null)
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['clientes'] = $this->consigaz_model->get_clientes_by_user($this->user->id);

        if (!is_null($fechamento) && !is_null($entidade)) {
            $data['fechamento'] = $this->consigaz_model->get_fechamento($fechamento);
            $data['entidade'] = $this->consigaz_model->get_entidade(40);
            $data['ramal'] = $this->consigaz_model->get_ramal(40, 'gas');
            $data['entidade_id'] = $data['entidade']->id;
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

    public function alertas()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        return $this->render("alertas", $data);
    }

    public function configuracoes()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['monitoria'] = $this->monitoria;

        $data['secret'] = $this->consigaz_model->get_secret_key($this->user->id);

        return $this->render("configuracoes", $data);
    }

    // Requests

    public function get_entidades()
    {
        $db = Database::connect('easy_com_br');

        $builder = $db->table('auth_user_relation');
        $builder->join('esm_entidades', 'esm_entidades.id = auth_user_relation.entidade_id');
        $builder->select('esm_entidades.id, esm_entidades.nome,
            CONCAT(esm_entidades.logradouro, ", ", esm_entidades.numero, " - ", esm_entidades.bairro, ", ", esm_entidades.cidade, " - ", esm_entidades.uf, ", ", esm_entidades.cep) as endereco');
        $builder->where('auth_user_relation.user_id', $this->user->id);

        // Datatables Php Library
        $dt = new Datatables(new Codeigniter4Adapter);

        // using CI4 Builder
        $dt->query($builder);

        $data = $dt->generate()->getData();

        $totalConsumoAtual = 0;
        $totalConsumoAnterior = 0;
        $totalAbertas = 0;
        $totalFechadas = 0;
        $totalErros = 0;
        $totalAlertas = 0;
        $totalVazamentos = 0;

        foreach ($data as $d) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($d['id']);

            foreach ($medidores as $medidor) {
                $consumoAnterior = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);
                foreach ($consumoAnterior as $c) {
                    $totalConsumoAnterior += $c->value;
                }

                $consumoAtual = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
                foreach ($consumoAtual as $c) {
                    $totalConsumoAtual += $c->value;
                }

                $totalAbertas += $this->consigaz_model->get_valvulas($medidor->id, 'open', 'count');
                $totalFechadas += $this->consigaz_model->get_valvulas($medidor->id, 'close', 'count');
                $totalErros += $this->consigaz_model->get_valvulas($medidor->id, 'vermelho', 'count');
                $totalAlertas += $this->consigaz_model->get_valvulas($medidor->id, 'amarelo', 'count');
            }
        }

        $dt->setDistinctResponse(array(
            'atual' => number_format($totalConsumoAtual, 2, ',', '.') . ' <small>m³</small>',
            'anterior' => number_format($totalConsumoAnterior, 2, ',', '.') . ' <small>m³</small>',
            'abertas' => $totalAbertas,
            'fechadas' => $totalFechadas,
            'erros' => $totalErros,
            'alertas' => $totalAlertas,
            'vazamentos' => $totalVazamentos,
        ));

        $dt->add('ultima_competencia', function ($data) {
            if ($this->consigaz_model->get_last_fechamento($data['id'])) {
                return strftime('%b/%Y', strtotime($this->consigaz_model->get_last_fechamento($data['id'])->competencia));
            } else {
                return 'Nenhum fechamento encontrado';
            }
        });

        $dt->add("opened", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');

            $total = 0;
            foreach ($medidores as $medidor) {
                $total += $this->consigaz_model->get_valvulas($medidor->id, 'open', 'count');
            }

            return number_format($total, 0, '', '.');
        });

        $dt->add("closed", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');

            $total = 0;
            foreach ($medidores as $medidor) {
                $total += $this->consigaz_model->get_valvulas($medidor->id, 'close', 'count');
            }

            return number_format($total, 0, '', '.');
        });

        $dt->add("vermelho", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');

            $total = 0;
            foreach ($medidores as $medidor) {
                $total += $this->consigaz_model->get_valvulas($medidor->id, 'vermelho', 'count');
            }

            return number_format($total, 0, '', '.');
        });

        $dt->add("vazamentos", function ($data) {
            return number_format(0, 0, '', '.');
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');

            $total = 0;
            foreach ($medidores as $medidor) {
                $total += $this->consigaz_model->get_valvulas($medidor->id, 'verde', 'count');
            }

            return number_format($total, 0, '', '.');
        });

        $dt->add("amarelo", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');

            $total = 0;
            foreach ($medidores as $medidor) {
                $total += $this->consigaz_model->get_valvulas($medidor->id, 'amarelo', 'count');
            }

            return number_format($total, 0, '', '.');
        });

        $dt->add("ultimo_mes", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');
            $total = 0;

            foreach ($medidores as $medidor) {
                $consumo = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);
                foreach ($consumo as $c) {
                    $total += $c->value;
                }
            }

            return number_format($total, 2, ',', '.') . ' <small>m³</small>';
        });

        $dt->add("mes_atual", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');
            $total = 0;

            foreach ($medidores as $medidor) {
                $consumo = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
                foreach ($consumo as $c) {
                    $total += $c->value;
                }
            }

            return number_format($total, 2, ',', '.') . ' <small>m³</small>';
        });

        $dt->add("previsao", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');
            $total = 0;

            foreach ($medidores as $medidor) {
                $consumo = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
                foreach ($consumo as $c) {
                    $total += $c->value;
                }
            }

            $days = (strtotime(date('Y-m-d')) - strtotime(date('Y-m-01'))) / 86400 + 1;
            $days_month = date('t', strtotime('this month'));

            $total = $total / $days * $days_month;

            return number_format($total, 2, ',', '.') . ' <small>m³</small>';
        });

        $dt->add("actions", function ($data) {
            return '
                <div class="dropdown">
                    <a class="" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars" title="Ações"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item action-view" data-id="' . $data['id'] . '"><i class="fas fa-eye me-1" title="Ver"></i> Detalhes</a>
                        <a class="dropdown-item action-inclui-fechamento" data-id="' . $data['id'] . '"><i class="fas fa-file-import me-1" title="Faturar Individual"></i> Faturar</a>
                        <a class="dropdown-item action-edit" data-id="' . $data['id'] . '" href="#"><i class="fas fa-pencil-alt me-1"></i> Editar</a>
                    </div>
                </div>';
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
        $builder->select('esm_medidores.id as m_id, esm_unidades.id as u_id, esm_medidores.nome AS device, esm_medidores.device AS medidor, esm_unidades.nome as unidade, esm_agrupamentos.nome as bloco, esm_valves_stats.state, esm_valves_stats.status');
        $builder->where('esm_agrupamentos.entidade_id', $entidade);

        // Datatables Php Library
        $dt = new Datatables(new Codeigniter4Adapter);

        // using CI4 Builder
        $dt->query($builder);

        $data = $dt->generate()->getData();

        $totalConsumoAtual = 0;
        $totalConsumoAnterior = 0;
        $totalAbertas = 0;
        $totalFechadas = 0;
        $totalErros = 0;

        foreach ($data as $d) {
            $consumoAnterior = $this->gas_model->GetConsumption($d['m_id'], date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);
            foreach ($consumoAnterior as $c) {
                $totalConsumoAnterior += $c->value;
            }

            $consumoAtual = $this->gas_model->GetConsumption($d['m_id'], date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
            foreach ($consumoAtual as $c) {
                $totalConsumoAtual += $c->value;
            }

            if ($d['state']) {
                $totalAbertas ++;
            } else {
                $totalFechadas ++;
            }

            if ($d['status'] === 'vermelho') {
                $totalErros ++;
            }
        }

        $dt->setDistinctResponse(array(
            'atual' => number_format($totalConsumoAtual, 2, ',', '.') . ' <small>m³</small>',
            'anterior' => number_format($totalConsumoAnterior, 2, ',', '.') . ' <small>m³</small>',
            'abertas' => $totalAbertas,
            'fechadas' => $totalFechadas,
            'erros' => $totalErros,
        ));

        $dt->add("ultimo_mes", function ($data) {
            $consumo = $this->gas_model->GetConsumption($data['m_id'], date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);
            $total = 0;

            foreach ($consumo as $c) {
                $total += $c->value;
            }

            return number_format($total, 2, ',', '.') . ' <small>m³</small>';
        });

        $dt->add("mes_atual", function ($data) {
            $consumo = $this->gas_model->GetConsumption($data['m_id'], date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
            $total = 0;

            foreach ($consumo as $c) {
                $total += $c->value;
            }

            return number_format($total, 2, ',', '.') . ' <small>m³</small>';
        });

        $dt->add("previsao", function ($data) {
            $consumo = $this->gas_model->GetConsumption($data['m_id'], date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
            $total = 0;

            foreach ($consumo as $c) {
                $total += $c->value;
            }

            $days = (strtotime(date('Y-m-d')) - strtotime(date('Y-m-01'))) / 86400 + 1;
            $days_month = date('t', strtotime('this month'));

            $total = $total / $days * $days_month;

            return number_format($total, 2, ',', '.') . ' <small>m³</small>';
        });

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
            }

            return '<form><input type="hidden" value="' . $data['m_id'] . '" name="m_id">
                <div class="switch switch-sm switch-white ' . $disabled . ' ' . $color . '">
                    <input type="checkbox" class="switch-input" name="state" data-plugin-ios-switch ' . $checked . '>
                </div>
            </form>';
        });

        $dt->add("actions", function ($data) {
            return '
                <div class="dropdown">
                    <a class="" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-bars" title="Ações"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item reload-table-modal me-1" href="#"><i class="fas fa-rotate" title="Atualizar"></i> Atualizar</a>
                        <a class="dropdown-item me-1" target="_blank" href="' . base_url($this->url . '/unidade/' . $data['u_id'] . '/consumo') . '"><i class="fas fa-eye" title="Consumo"></i> Consumo</a>
                        <a class="dropdown-item sync-leitura-modal me-1" data-mid="' . $data['m_id'] . '" href="#"><i class="fas fa-gear" title="Sincronizar"></i> Sincronizar</a>
                        <a class="dropdown-item action-edit me-1" data-mid="' . $data['m_id'] . '" href="#"><i class="fas fa-pencil-alt me-1"></i> Editar</a>
                    </div>
                </div>';
        });

        echo $dt->generate();
    }

    public function edit_valve_stats()
    {
        $state = $this->input->getPost("state");
        $medidor_id = $this->input->getPost("mid");
        $code = $this->input->getPost("code");

        $secret_key = $this->consigaz_model->get_secret_key($this->user->id);

        if (is_null($secret_key)) {
            echo json_encode(array("status" => "error", "message" => "QR Code não gerado!"));
            return;
        }

        if (!$this->check_code($code, $secret_key)) {
            echo json_encode(array("status" => "error", "message" => "Código inválido!"));
            return;
        }

        echo $this->consigaz_model->edit_valve_stats($medidor_id, $state);
    }

    public function edit_valve_leitura()
    {
        $leitura = $this->input->getPost("leitura");
        $medidor = $this->input->getPost("mid");

        return $this->consigaz_model->edit_valve_leitura($medidor, $leitura);
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

    public function get_unidades_config()
    {
        $entidade = $this->input->getPost("entidade");

        $db = \Config\Database::connect('easy_com_br');

        $builder = $db->table('esm_medidores');
        $builder->join('esm_unidades', 'esm_unidades.id = esm_medidores.unidade_id');
        $builder->join('esm_agrupamentos', 'esm_agrupamentos.id = esm_unidades.agrupamento_id');
        $builder->join('esm_valves_stats', 'esm_valves_stats.medidor_id = esm_medidores.id');
        $builder->select('esm_medidores.id as m_id, esm_unidades.id as u_id, esm_medidores.nome AS device, esm_medidores.device AS medidor, esm_unidades.nome as unidade, esm_agrupamentos.nome as bloco, esm_valves_stats.state, esm_valves_stats.status');
        $builder->where('esm_agrupamentos.entidade_id', $entidade);

        // Datatables Php Library
        $dt = new Datatables(new Codeigniter4Adapter);

        // using CI4 Builder
        $dt->query($builder);

        $dt->add("actions", function ($data) {
            return '
                <a class="text-primary me-1" href="' . base_url($this->url . '/unidade/' . $data['u_id'] . '/consumo') . '"><i class="fas fa-eye" title="Consumo"></i></a>
                <a class="text-muted sync-leitura-modal" data-mid="' . $data['m_id'] . '"><i class="fas fa-gear" title="Sincronizar"></i></a>
            ';
        });

        echo $dt->generate();
    }

    public function download_clientes()
    {
        $resume = $this->consigaz_model->download_clientes($this->user->id);

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório Clientes')
            ->setSubject(MonthName(date("m"))."/".date("Y"))
            ->setDescription('Relatório Clientes - '.date("01/m/Y").' - '.date("d/m/Y"))
            ->setKeywords('Relatório Clientes')
            ->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'Easymeter');
        $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório Resumo - '. date("01/m/Y").' a '.date("d/m/Y"));
        $spreadsheet->getActiveSheet()->mergeCells('A2:I2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Nome')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Abertas')->mergeCells('B4:B5');
        $spreadsheet->getActiveSheet()->setCellValue('C4', 'Fechadas')->mergeCells('C4:C5');
        $spreadsheet->getActiveSheet()->setCellValue('D4', 'Erros')->mergeCells('D4:D5');
        $spreadsheet->getActiveSheet()->setCellValue('E4', 'Alertas')->mergeCells('E4:E5');
        $spreadsheet->getActiveSheet()->setCellValue('F4', 'Corretas')->mergeCells('F4:F5');
        $spreadsheet->getActiveSheet()->setCellValue('G4', 'Último Mês - m³')->mergeCells('G4:G5');
        $spreadsheet->getActiveSheet()->setCellValue('H4', 'Mês Atual - m³')->mergeCells('H4:H5');
        $spreadsheet->getActiveSheet()->setCellValue('I4', 'Previsão - m³')->mergeCells('I4:I5');

        $spreadsheet->getActiveSheet()->getStyle('A1:I5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($resume, NULL, 'A6');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);

        $spreadsheet->getActiveSheet()->getStyle('A6:A'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('B6:B'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('D6:I'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('A6:G'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A'.(count($resume) + 7), 'Gerado em '.date("d/m/Y H:i"));

        $spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = "Resumo Clientes";

        ob_start();
        $writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'status' => "success",
            'name'   => $filename,
            'file'   => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function md_fechamento_inclui()
    {
        $entidade_id = $this->input->getPost('entidade');

        $data['entidade'] = (object) null;
        $data['ramal'] = (object) null;

        $data['entidade']->id = null;
        $data['ramal']->id = null;

        if ($entidade_id) {
            $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
            $data['ramal'] = $this->consigaz_model->get_ramal($entidade_id, 'gas');
        }

        echo view('Consigaz/modals/md_incluir_fechamento', $data);
    }

    public function md_generate_code()
    {
        $google2fa = new Google2FA();

        $secret_key = $this->consigaz_model->get_secret_key($this->user->id);

        if (!$secret_key) {
            $secret_key = $google2fa->generateSecretKey();

            if (!$this->consigaz_model->save_secret_key($secret_key, $this->user->id)) {
                header('HTTP/1.1 500 Internal Server Booboo');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => 'Não foi possível salvar a chave')));
            }
        }

        $holder = ' ' . $this->user->username;
        $data['holder'] = ' ' . $this->user->username;

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'Consigaz',
            $holder,
            $secret_key
        );

        $google2fa_url = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl='.$qrCodeUrl;

        $data['google2fa_url'] = $google2fa_url;

        echo view('Consigaz/modals/md_generate_code', $data);
    }

    public function md_check_code()
    {
        $data['mid'] = $this->input->getPost('m_id');
        $data['state'] = $this->input->getPost("state") ? 1 : 0;

        echo view('Consigaz/modals/md_check_code', $data);
    }

    public function check_code($user_provided_code, $secret_key)
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA();

        return $google2fa->verifyKey($secret_key, $user_provided_code);
    }

    public function download_unidades()
    {
        if (!$this->input->getPost('entidade')) {
            echo json_encode(array("status" => "error", "message" => "Nenhum cliente selecionado"));
            return;
        }

        $resume = $this->consigaz_model->download_unidades($this->input->getPost('entidade'));

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório Unidades')
            ->setSubject(MonthName(date("m"))."/".date("Y"))
            ->setDescription('Relatório Unidades - '.date("01/m/Y").' - '.date("d/m/Y"))
            ->setKeywords('Relatório Unidades')
            ->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'Easymeter');
        $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório Unidades - '. date("01/m/Y").' a '.date("d/m/Y"));
        $spreadsheet->getActiveSheet()->mergeCells('A2:H2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Medidor')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Dispositivo')->mergeCells('B4:B5');
        $spreadsheet->getActiveSheet()->setCellValue('C4', 'Bloco')->mergeCells('C4:C5');
        $spreadsheet->getActiveSheet()->setCellValue('D4', 'Apto')->mergeCells('D4:D5');
        $spreadsheet->getActiveSheet()->setCellValue('E4', 'Último Mês - m³')->mergeCells('E4:E5');
        $spreadsheet->getActiveSheet()->setCellValue('F4', 'Mês Atual - m³')->mergeCells('F4:F5');
        $spreadsheet->getActiveSheet()->setCellValue('G4', 'Previsão - m³')->mergeCells('G4:G5');
        $spreadsheet->getActiveSheet()->setCellValue('H4', 'Válvula')->mergeCells('H4:H5');

        $spreadsheet->getActiveSheet()->getStyle('A1:J5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($resume, NULL, 'A6');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);

        $spreadsheet->getActiveSheet()->getStyle('A6:A'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('B6:B'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('D6:H'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('A6:G'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A'.(count($resume) + 7), 'Gerado em '.date("d/m/Y H:i"));

        $spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = "Resumo Unidades";

        ob_start();
        $writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'status' => "success",
            'name'   => $filename,
            'file'   => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function md_edit_cliente()
    {
        $entidade_id = $this->input->getPost('entidade');

        $data['entidade'] = (object) null;

        $data['entidade']->id = null;

        if ($entidade_id) {
            $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
        }

        echo view('Consigaz/modals/md_edit_cliente', $data);
    }

    public function edit_cliente()
    {
        $entidade = $this->input->getPost("entidade");
        $data = array(
            "nome" => $this->input->getPost("nome")
        );

        echo $this->consigaz_model->edit_cliente($entidade, $data);
    }

    public function md_edit_medidor()
    {
        $medidor_id = $this->input->getPost('medidor');

        $data['medidor'] = (object) null;

        $data['medidor']->id = null;

        if ($medidor_id) {
            $data['medidor'] = $this->consigaz_model->get_medidor($medidor_id);
        }

        echo view('Consigaz/modals/md_edit_medidor', $data);
    }

    public function edit_medidor()
    {
        $medidor = $this->input->getPost("medidor");
        $data = array(
            "medidor" => $this->input->getPost("device")
        );

        echo $this->consigaz_model->edit_medidor($medidor, $data);
    }

    public function md_view_cliente()
    {
        $entidade_id = $this->input->getPost('entidade');

        $data['entidade'] = (object) null;

        $data['entidade']->id = null;

        if ($entidade_id) {
            $data['entidade'] = $this->consigaz_model->get_entidade($entidade_id);
        }

        echo view('Consigaz/modals/md_view_cliente', $data);
    }
}