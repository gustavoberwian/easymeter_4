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

        $dt->add('ultima_competencia', function ($data) {
            if ($this->consigaz_model->get_last_fechamento($data['id'])->competencia) {
                return strftime('%b/%Y', strtotime($this->consigaz_model->get_last_fechamento($data['id'])->competencia));
            } else {
                return '';
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

        $dt->add("amarelo", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');

            $total = 0;
            foreach ($medidores as $medidor) {
                $total += $this->consigaz_model->get_valvulas($medidor->id, 'amarelo', 'count');
            }

            return number_format($total, 0, '', '.');
        });

        $dt->add("verde", function ($data) {
            $medidores = $this->consigaz_model->get_medidores_by_entidade($data['id'], 'gas');

            $total = 0;
            foreach ($medidores as $medidor) {
                $total += $this->consigaz_model->get_valvulas($medidor->id, 'verde', 'count');
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

            return number_format($total, 0, '', '.') . ' <small>m³</small>';
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

            return number_format($total, 0, '', '.') . ' <small>m³</small>';
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

            return number_format($total, 0, '', '.') . ' <small>m³</small>';
        });

        $dt->add("actions", function ($data) {
            return '
                <a class="text-success me-1" data-id="' . $data['id'] . '"><i class="fas fa-eye" title="Ver"></i></a>
                <a class="action-inclui-fechamento text-primary" data-id="' . $data['id'] . '"><i class="fas fa-file-import" title="Faturar Individual"></i></a>
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
        $builder->select('esm_medidores.id as m_id, esm_unidades.id as u_id, esm_medidores.nome AS device, esm_medidores.device AS medidor, esm_unidades.nome as unidade, esm_agrupamentos.nome as bloco, esm_valves_stats.state, esm_valves_stats.status');
        $builder->where('esm_agrupamentos.entidade_id', $entidade);

        // Datatables Php Library
        $dt = new Datatables(new Codeigniter4Adapter);

        // using CI4 Builder
        $dt->query($builder);

        $dt->add("ultimo_mes", function ($data) {
            $consumo = $this->gas_model->GetConsumption($data['m_id'], date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);
            $total = 0;

            foreach ($consumo as $c) {
                $total += $c->value;
            }

            return number_format($total, 0, '', '.') . ' <small>m³</small>';
        });

        $dt->add("mes_atual", function ($data) {
            $consumo = $this->gas_model->GetConsumption($data['m_id'], date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
            $total = 0;

            foreach ($consumo as $c) {
                $total += $c->value;
            }

            return number_format($total, 0, '', '.') . ' <small>m³</small>';
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

            return number_format($total, 0, '', '.') . ' <small>m³</small>';
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

    public function get_unidades_config()
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

        $dt->add("actions", function ($data) {
            return '
                <a class="text-primary reload-table-modal cur-pointer me-1"><i class="fas fa-rotate" title="Atualizar"></i>
                <a href="' . base_url($this->url . '/unidade/' . $data['u_id'] . '/consumo') . '" class="text-primary me-1"><i class="fas fa-eye" title="Consumo"></i></a>
                <a class="text-primary sync-leitura-modal cur-pointer" data-mid="' . $data['m_id'] . '"><i class="fas fa-gear" title="Sincronizar"></i>
            ';
        });

        echo $dt->generate();
    }

    public function download_clientes()
    {
        $resume = $this->consigaz_model->download_clientes($this->user->id);

        $spreadsheet = new Spreadsheet();

        $titulos = [
            ['Nome', 'Leitura', 'Válvulas', 'Consumo']
        ];

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório Clientes')
            ->setSubject(MonthName(date("m"))."/".date("Y"))
            ->setDescription('Relatório Clientes - '.date("01/m/Y").' - '.date("d/m/Y"))
            ->setKeywords('Relatório Clientes')
            ->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório Resumo - '. date("01/m/Y").' a '.date("d/m/Y"));
        $spreadsheet->getActiveSheet()->mergeCells('A2:H2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Nome')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Abertas')->mergeCells('B4:B5');
        $spreadsheet->getActiveSheet()->setCellValue('C4', 'Fechadas')->mergeCells('C4:C5');
        $spreadsheet->getActiveSheet()->setCellValue('D4', 'Erros')->mergeCells('D4:D5');
        $spreadsheet->getActiveSheet()->setCellValue('E4', 'Alertas')->mergeCells('E4:E5');
        $spreadsheet->getActiveSheet()->setCellValue('F4', 'Corretas')->mergeCells('F4:F5');
        $spreadsheet->getActiveSheet()->setCellValue('G4', 'Último Mês')->mergeCells('G4:G5');
        $spreadsheet->getActiveSheet()->setCellValue('H4', 'Mês Atual')->mergeCells('H4:H5');
        $spreadsheet->getActiveSheet()->setCellValue('I4', 'Previsão')->mergeCells('I4:I5');

        $spreadsheet->getActiveSheet()->getStyle('A1:J5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'D5');

        $spreadsheet->getActiveSheet()->fromArray($resume, NULL, 'A6');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);

        $spreadsheet->getActiveSheet()->getStyle('A6:A'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('B6:B'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('D6:J'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

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
}