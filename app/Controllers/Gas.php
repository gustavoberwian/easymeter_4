<?php

/**
 * @property $energy_model
 */

namespace App\Controllers;

use App\Models\Gas_model;
use App\Models\Consigaz_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;
use CodeIgniter\Database\RawSql;

class Gas extends UNO_Controller
{
    protected $input;
    protected Datatables $datatables;

    private Consigaz_model $consigaz_model;
    private Gas_model $gas_model;

    public function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        // load models
        $this->consigaz_model = new Consigaz_model();
        $this->gas_model = new Gas_model();

        // load requests
        $this->input = \Config\Services::request();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);
    }

    private function chartConfig($type, $stacked, $series, $titles, $labels, $unit, $decimals, $extra = array(), $footer = "", $dates = array())
    {
        $config = array(
            "chart" => array(
                "type"      => $type,
                "width"     => "100%",
                "height"    => 380,
                "foreColor" => "#777",
                "stacked"   => $stacked,
                "toolbar"   => array(
                    "show" => false
                ),
                "zoom"      => array(
                    "enabled" => false
                ),
                "events"    => array(
                    "click" => true
                )
            ),
            "series" => $series,
            "dataLabels" => array(
                "enabled" => false,
            ),
            "xaxis" => array(
                "categories" => $labels,
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                ),
            ),
            "legend" => array(
                "showForSingleSeries" => true,
                "position"            => "bottom"
            ),
            "tooltip" => array(
                "enabled"   => true,
                "x" => array(
                    "formatter" => "function",
                    "show"      => true
                ),
                "y" => array(
                    "formatter" => "function",
                    "show"      => true
                )
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $titles,
                    "decimals" => $decimals,
                ),
                "unit"     => $unit,
                "decimals" => $decimals,
                "custom"   => $extra,
                "footer"   => $footer,
                "dates"    => $dates
            ),
        );

        if ($type == "area" || $type == "line") {
            $config["stroke"] = array(
                "curve" => "smooth",
                "width" => 2
            );
        }

        return $config;
    }

    private function chartFooter($data, $colorize = false)
    {
        $html = '<div class="card-footer total text-right d-none d-sm-block" data-loading-overlay="" data-loading-overlay-options="{ \"css\": { \"backgroundColor\": \"#00000080\" } }" style="">
                    <div class="row">';

        foreach ($data as $d) {

            $html .= '<div class="col text-center">
                        <div class="row">
                            <div class="col-6 col-lg-12">
                                <p class="text-3 mb-0" style="color: '.$d[2].';">'.$d[0].'</p>
                            </div>
                            <div class="col-6 col-lg-12">
                                <p class="text-3 mb-0" style="color: '.$d[2].';">'.$d[1].'</p>
                            </div>
                        </div>
                    </div>';
        }

        $html .= '</div></div>';

        return $html;
    }

    public function chart_battery($device, $compare, $start, $end)
    {
        $period = $this->gas_model->get_battery_consumption($device, $start, $end, true);

        $bateria1  = array();
        $bateria2  = array();
        $labels  = array();
        $titles  = array();
        $dates   = array();

        $series = array();

        if ($period) {
            foreach ($period as $v) {
                $bateria1[] = ($v->bateria1 / 1545.66) + 0.3;
                $bateria2[] = ($v->bateria2 / 1545.66) + 0.2;
                $labels[] = $v->label;

                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }

            $series[] = array(
                "name"  => "Bateria 1",
                "data"  => $bateria1,
                "color" => "#007AB8",
            );
            $series[] = array(
                "name"  => "Bateria 2",
                "data"  => $bateria2,
                "color" => "#734BA9",
            );
        }

        $extra = array();

        $data = array();

        $footer = $this->chartFooter($data);

        $config = $this->chartConfig("line", false, $series, $titles, $labels, "V", 1, $extra, $footer, $dates);

        echo json_encode($config);
    }

    public function chart_sensor($device, $compare, $start, $end)
    {
        $period = $this->gas_model->get_sensor_consumption($device, $start, $end, true);

        $values  = array();
        $labels  = array();
        $titles  = array();
        $dates   = array();

        $series = array();

        if ($period) {
            foreach ($period as $v) {
                $values[] = $v->value == 65535 ? 0 : $v->value;
                $labels[] = $v->label;

                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }

            $series[] = array(
                "name"  => "Sensor",
                "data"  => $values,
                "color" => "#007AB8",
            );
        }

        $extra = array();

        $data = array();

        $footer = $this->chartFooter($data);

        $config = $this->chartConfig("bar", false, $series, $titles, $labels, "PPM", 1, $extra, $footer, $dates);

        echo json_encode($config);
    }

    public function chart()
    {
        $field    = $this->input->getPost('field');

        $divisor  = 1;
        $decimals = 0;
        $unidade  = "";
        $type     = "line";

        $device   = $this->input->getPost('device');
        $compare  = $this->input->getPost('compare');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');

        if ($field === "battery") {
            $this->chart_battery($device, $compare, $start, $end);
            return;
        } elseif ($field === "sensor") {
            $this->chart_sensor($device, $compare, $start, $end);
            return;
        }

        $period   = $this->gas_model->GetConsumption($device, $start, $end, array(), true, null);
        $period_s = $this->gas_model->GetConsumption($device, $start, $end, array(), false, null)[0]->value;
        $month    = $this->gas_model->GetConsumption($device, date("Y-m-01"), date("Y-m-d"), array(), false, null)[0]->value;
        $day      = $this->gas_model->GetConsumption($device, date("Y-m-d", strtotime("-1 months")), date("Y-m-d"), array(), false, null)[0]->value;

        $values  = array();
        $labels  = array();
        $titles  = array();
        $dates   = array();

        $max = -1;
        $min = 999999999;

        $series = array();

        if ($period) {
            foreach ($period as $v) {
                $values[] = $v->value;
                $labels[] = $v->label;

                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
                if ($max < floatval($v->value) && !is_null($v->value)) $max = floatval($v->value);
                if ($min > floatval($v->value) && !is_null($v->value)) $min = floatval($v->value);
            }

            $series[] = array(
                "name"  => "Consumo",
                "data"  => $values,
                "color" => "#007AB8",
            );
        }

        if ($compare != "") {
            $values_c  = array();
            $comp = $this->gas_model->GetConsumption($compare, $start, $end, array(), false,null);
            if ($comp) {
                foreach ($comp as $v) {
                    $values_c[] = $v->value;
                }

                $series[] = array(
                    "name"  => "Comparado",
                    "data"  => $values_c,
                    "color" => "#87c1de",
                );
            }
        }

        $dias   = ((strtotime(date("Y-m-d")) - strtotime(date("Y-m-01"))) / 86400) + 1;
        $dias_t = date('d', mktime(0, 0, 0, date("m") + 1, 0, date("Y")));
        $dias_m = (strtotime(date("Y-m-d")) - strtotime("-1 months")) / 86400;

        $extra = array(
            "period"      => number_format(round($period_s, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>m³</span>",
            "month"       => number_format(round($month, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>m³</span>",
            "prevision"   => number_format(round(($month) / $dias * $dias_t, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>m³</span>",
            "day"         => number_format(round(($day) / $dias_m, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>m³</span>",
        );

        $data = array(
            array("Máximo", ($max == -1) ? "-" : number_format(round($max), 0, ",", ".") . " <span style='font-size:12px;'>m³</span>", "#268ec3"),
            array("Mínimo", ($min == 999999999) ? "-" : number_format(round($min), 0, ",", ".") . " <span style='font-size:12px;'>m³</span>", "#268ec3"),
            array("Médio",  ($min == 999999999) ? "-" : number_format(round(($period_s) / count($period)), 0, ",", ".") . " <span style='font-size:12px;'>m³</span>", "#268ec3"),
        );

        $footer = $this->chartFooter($data);

        $config = $this->chartConfig("bar", false, $series, $titles, $labels, "m³", 2, $extra, $footer, $dates);

        echo json_encode($config);
    }

    public function get_fechamentos_gas()
    {
        $entidade = empty($this->input->getPost('entidade')) ? 0 : $this->input->getPost('entidade');

        // realiza a query via dt
        $dt = $this->datatables->query("SELECT
                esm_fechamentos_gas.id,
                esm_fechamentos_gas.competencia,
                FROM_UNIXTIME(esm_fechamentos_gas.inicio, '%d/%m/%Y') AS inicio,
                FROM_UNIXTIME(esm_fechamentos_gas.fim, '%d/%m/%Y') AS fim,
                FORMAT(esm_fechamentos_gas.leitura_atual - esm_fechamentos_gas.leitura_anterior, 1, 'de_DE') AS consumo,
                DATE_FORMAT(esm_fechamentos_gas.cadastro, '%d/%m/%Y') AS emissao
            FROM esm_fechamentos_gas
            JOIN esm_entidades ON esm_entidades.id = esm_fechamentos_gas.entidade_id AND esm_entidades.id = $entidade
            ORDER BY competencia DESC");

        $dt->edit('competencia', function ($data) {
            return strftime('%b/%Y', strtotime($data['competencia']));
        });

        // inclui actions
        $dt->add('action', function ($data) {
            return '<a href="#" class="action-gas-download text-primary me-2" data-id="' . $data['id'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
				<a href="#" class="action-gas-delete text-danger" data-id="' . $data['id'] . '"><i class="fas fa-trash" title="Excluir"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function delete_fechamento()
    {
        $id = $this->input->getPost('id');

        echo $this->gas_model->delete_fechamento($id);
    }

    public function download()
    {
        $fechamento_id = $this->input->getPost('id');

        // busca fechamento
        $fechamento = $this->gas_model->get_fechamento($fechamento_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if(!$fechamento || is_null($fechamento->id)) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Lançamento não encontrado"));
            return;
        }

        $spreadsheet = new Spreadsheet();

        $titulos = [
            ['Unidade', 'Medidor', 'Leitura Anterior', 'Leitura Atual', 'Consumo - L' ]
        ];

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório de Consumo')
            ->setSubject(strftime('%b/%Y', strtotime($fechamento->competencia)))
            ->setDescription('Relatório de Consumo - '.$fechamento->nome.' - '.$fechamento->competencia)
            ->setKeywords($fechamento->nome.' '.strftime('%b/%Y', strtotime($fechamento->competencia)))
            ->setCategory('Relatório')->setCompany('Easymeter');

        $split = 0;

        for ($i = 0; $i <= $split; $i++) {

            $spreadsheet->setActiveSheetIndex($i);

            $spreadsheet->getActiveSheet()->getStyle('A1:E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($fechamento->nome));
            $spreadsheet->getActiveSheet()->mergeCells('A1:E1');
            $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Consumo de Gás - '.date("d/m/Y", $fechamento->inicio).' a '.date("d/m/Y", $fechamento->fim));
            $spreadsheet->getActiveSheet()->mergeCells('A2:E2');

            $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'A4');

            $spreadsheet->getActiveSheet()->getStyle('A1:E4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $linhas = $this->gas_model->get_fechamento_unidades($fechamento_id, $i + 1);

            $spreadsheet->getActiveSheet()->fromArray($linhas, NULL, 'A5');

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);

            $spreadsheet->getActiveSheet()->getStyle('B5:E'.(count($linhas) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


            $spreadsheet->getActiveSheet()->setCellValue('A'.(count($linhas) + 7), 'Gerado em '.date("d/m/Y H:i"));


            $spreadsheet->getActiveSheet()->setSelectedCell('A1');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = $fechamento->nome.' Gás - '. strftime('%b/%Y', strtotime($fechamento->competencia));

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

    public function download_fechamentos()
    {
        $entidade_id = $this->input->getPost('id');

        // verifica retorno
        if(!$entidade_id) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Nenhum cliente selecionado"));
            return;
        }
        
        // busca fechamento
        $fechamentos = $this->gas_model->get_fechamentos($entidade_id);
        $entidade    = $this->consigaz_model->get_entidade($entidade_id);

        //TODO verificar se usuário tem acesso a esse fechamento
        
        // verifica retorno
        if(!$fechamentos) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Nenhum lançamento encontrado"));
            return;
        }

        foreach($fechamentos as &$f) {
            $f['competencia'] = strftime('%b/%Y', strtotime($f['competencia']));
        }

        $spreadsheet = new Spreadsheet();

        $titulos = [
            ['Total']
        ];

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório de Lançamentos - Gás')
            ->setSubject($entidade->nome)
            ->setDescription('Relatório de Lançamentos - Gás - '.$entidade->nome)
            ->setKeywords($entidade->nome.' Lançamentos Água')
            ->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($entidade->nome));
        $spreadsheet->getActiveSheet()->mergeCells('A1:E1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Lançamentos - Gás');
        $spreadsheet->getActiveSheet()->mergeCells('A2:E2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Competência')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Data Inicial')->mergeCells('B4:B5');
        $spreadsheet->getActiveSheet()->setCellValue('C4', 'Data Final')->mergeCells('C4:C5');
        $spreadsheet->getActiveSheet()->setCellValue('D4', 'Consumo - L')->mergeCells('D4:D5');
        $spreadsheet->getActiveSheet()->setCellValue('E4', 'Emissão')->mergeCells('E4:E5');

        $spreadsheet->getActiveSheet()->getStyle('A1:G5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'D5');

        $spreadsheet->getActiveSheet()->fromArray($fechamentos, NULL, 'A6');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);

        $spreadsheet->getActiveSheet()->getStyle('A6:G'.(count($fechamentos) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A'.(count($fechamentos) + 7), 'Gerado em '.date("d/m/Y H:i"));

        $spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);

        $filename = "Lançamentos Gás ".$entidade->nome;

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

    public function add_fechamento_geral($data)
    {
        $entidades = $this->consigaz_model->get_entidades($this->user->id);
        if (!$entidades) {
            return '{ "status": "message", "message" : "Não há clientes cadastrados para esse usuário"}';
        }

        foreach ($entidades as $entidade) {
            $entidade->ramal = $this->consigaz_model->get_ramal($entidade->id, 'gas');

            if (!$entidade->ramal) {
                return '{ "status": "message", "message" : "Não há ramal cadastrado para o cliente ' . $entidade->nome . '"}';
            }

            if ($this->gas_model->verify_competencia($entidade->id, $entidade->ramal->id, $data["competencia"])) {
                return '{ "status": "message", "field": "tar-gas-competencia", "message" : "Cliente ' . $entidade->nome . ' já possui lançamento nessa competência"}';
            }

            if (date_create_from_format('d/m/Y', $data["inicio"])->format('U') == date_create_from_format('d/m/Y', $data["fim"])->format('U')) {
                return '{ "status": "message", "field": "tar-gas-data-fim", "message" : "Data final igual a inicial"}';
            }

            if (date_create_from_format('d/m/Y', $data["inicio"])->format('U') > date_create_from_format('d/m/Y', $data["fim"])->format('U')) {
                return '{ "status": "message", "field": "tar-gas-data-fim", "message" : "Data final menor que a inicial"}';
            }
        }

        return $this->gas_model->calculateGeral($data, $entidades);
    }

    public function add_fechamento()
    {
        $competencia = date("Y-m-d", strtotime('01-' . str_replace('/', '-', $this->input->getPost('tar-gas-competencia'))));

        if (!$this->input->getPost('tar-gas-entidade') || $this->input->getPost('tar-gas-entidade') === 'ALL') {
            echo $this->add_fechamento_geral(array(
                "competencia" => $competencia,
                "inicio"      => $this->input->getPost('tar-gas-data-ini'),
                "fim"         => $this->input->getPost('tar-gas-data-fim'),
                "mensagem"    => $this->input->getPost('tar-gas-msg')
            ));
            return;
        }

        if (!$this->consigaz_model->get_ramal($this->input->getPost('tar-gas-entidade'), 'gas')) {
            sleep(2);
            echo '{ "status": "message", "field": "", "message" : "Ramal não cadastrado, contate seu administrador"}';
            return;
        }

        $data = array(
            "entidade_id" => $this->input->getPost('tar-gas-entidade'),
            "ramal_id"    => $this->consigaz_model->get_ramal($this->input->getPost('tar-gas-entidade'), 'gas')->id,
            "competencia" => $competencia,
            "inicio"      => $this->input->getPost('tar-gas-data-ini'),
            "fim"         => $this->input->getPost('tar-gas-data-fim'),
            "mensagem"    => $this->input->getPost('tar-gas-msg'),
        );

        if ($this->gas_model->verify_competencia($data["entidade_id"], $data["ramal_id"], $data["competencia"])) {
            echo '{ "status": "message", "field": "tar-gas-competencia", "message" : "Competência já possui lançamento"}';
            return;
        }

        if (date_create_from_format('d/m/Y', $data["inicio"])->format('U') == date_create_from_format('d/m/Y', $data["fim"])->format('U')) {
            echo '{ "status": "message", "field": "tar-gas-data-fim", "message" : "Data final igual a inicial"}';
            return;
        }

        if (date_create_from_format('d/m/Y', $data["inicio"])->format('U') > date_create_from_format('d/m/Y', $data["fim"])->format('U')) {
            echo '{ "status": "message", "field": "tar-gas-data-fim", "message" : "Data final menor que a inicial"}';
            return;
        }

        echo $this->gas_model->calculate($data, $data['entidade_id']);
    }

    public function get_fechamentos_unidades()
    {
        $fid = $this->input->getPost('fid');

        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.nome AS medidor,
                leitura_anterior,
                leitura_atual,
                leitura_atual - leitura_anterior AS consumo,
                esm_fechamentos_gas_entradas.id AS DT_RowId
            FROM 
                esm_fechamentos_gas_entradas
            JOIN 
                esm_medidores ON esm_medidores.id = esm_fechamentos_gas_entradas.medidor_id
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE 
                esm_fechamentos_gas_entradas.fechamento_id = $fid
        ");

        $dt->edit('leitura_anterior', function ($data) {
            return str_pad(round($data["leitura_anterior"]), 6 , '0' , STR_PAD_LEFT);
        });

        $dt->edit('leitura_atual', function ($data) {
            return str_pad(round($data["leitura_atual"]), 6 , '0' , STR_PAD_LEFT);
        });

        $dt->edit('consumo', function ($data) {
            return number_format($data['consumo'], 0, ",", ".");
        });

        echo $dt->generate();
    }
}