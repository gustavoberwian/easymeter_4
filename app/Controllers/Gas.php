<?php

/**
 * @property $energy_model
 */

namespace App\Controllers;

use App\Models\Gas_model;
use App\Models\Shopping_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Gas extends UNO_Controller
{
    protected $input;
    protected Datatables $datatables;

    private Shopping_model $shopping_model;
    private Gas_model $gas_model;

    public function __construct()
    {
        parent::__construct();

        // load models
        $this->shopping_model = new Shopping_model();
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
//				"intersect" => false,
//				"shared"    => true,
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
                    "decimals" => 0,
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

    public function chart()
    {
        $field    = $this->input->getPost('field');
        $shopping_id    = $this->input->getPost('shopping_id');

        $this->user->config = $this->shopping_model->get_client_config($shopping_id);

        if (!$this->user->config) {
            return json_encode(array(
                "status" => "error",
                "message" => "Dados não foram carregados corretamente. Configurações gerais não fornecidas."
            ));
        }

        $divisor  = 1;
        $decimals = 0;
        $unidade  = "";
        $type     = "line";

        $device   = $this->input->getPost('device');
        $compare  = $this->input->getPost('compare');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');

        $period   = $this->gas_model->GetConsumption($device, $shopping_id, $start, $end, array(), true, null, $this->user->demo);

        $period_o = $this->gas_model->GetConsumption($device, $shopping_id, $start, $end, array("opened", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $period_c = $this->gas_model->GetConsumption($device, $shopping_id, $start, $end, array("closed", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $main     = $this->gas_model->GetDeviceLastRead($device, $shopping_id);
        $month_o  = $this->gas_model->GetConsumption($device, $shopping_id, date("Y-m-01"), date("Y-m-d"), array("opened", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $month_c  = $this->gas_model->GetConsumption($device, $shopping_id, date("Y-m-01"), date("Y-m-d"), array("closed", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;

        $day_o  = $this->gas_model->GetConsumption($device, $shopping_id, date("Y-m-d", strtotime("-1 months")), date("Y-m-d"), array("opened", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $day_c  = $this->gas_model->GetConsumption($device, $shopping_id, date("Y-m-d", strtotime("-1 months")), date("Y-m-d"), array("closed", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;

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
            $comp = $this->gas_model->GetConsumption($compare, $shopping_id, $start, $end, array(), false,null, $this->user->demo);
            if ($comp) {
                foreach ($comp as $v) {
                    $values_c[] = $v->value;
                }

                $series[] = array(
                    "name"  => "Comparado",//$this->shopping_model->GetUnidadeByDevice($compare)->nome,
                    "data"  => $values_c,
                    "color" => "#87c1de",
                );
            }
        }

        $dias   = ((strtotime(date("Y-m-d")) - strtotime(date("Y-m-01"))) / 86400) + 1;
        $dias_t = date('d', mktime(0, 0, 0, date("m") + 1, 0, date("Y")));
        $dias_m = (strtotime(date("Y-m-d")) - strtotime("-1 months")) / 86400;

        $extra = array(
            "main"        => ($main ? str_pad(round($main), 6 , '0' , STR_PAD_LEFT) : "- - - - - -"). " <span style='font-size:12px;'>L</span>",
            "period"      => number_format(round($period_o + $period_c, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "period_o"    => number_format(round($period_o, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "period_c"    => number_format(round($period_c, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "month"       => number_format(round($month_o + $month_c, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "month_o"     => number_format(round($month_o, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "month_c"     => number_format(round($month_c, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "prevision"   => number_format(round(($month_o + $month_c) / $dias * $dias_t, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "prevision_o" => number_format(round($month_o / $dias * $dias_t, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "prevision_c" => number_format(round($month_c / $dias * $dias_t, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "day"         => number_format(round(($day_o + $day_c) / $dias_m, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "day_o"       => number_format(round($day_o / $dias_m, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
            "day_c"       => number_format(round($day_c / $dias_m, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>L</span>",
        );

        $data = array(
            array("Máximo", ($max == -1) ? "-" : number_format(round($max), 0, ",", ".") . " <span style='font-size:12px;'>L</span>", "#268ec3"),
            array("Mínimo", ($min == 999999999) ? "-" : number_format(round($min), 0, ",", ".") . " <span style='font-size:12px;'>L</span>", "#268ec3"),
            array("Médio",  ($min == 999999999) ? "-" : number_format(round(($period_o + $period_c) / count($period)), 0, ",", ".") . " <span style='font-size:12px;'>L</span>", "#268ec3"),
        );

        $footer = $this->chartFooter($data);

        $config = $this->chartConfig("bar", false, $series, $titles, $labels, "L", 0, $extra, $footer, $dates);

        echo json_encode($config);
    }

    public function resume()
    {
        $this->user->config = $this->shopping_model->get_client_config($this->input->getPost('group'));

        $entity = $this->shopping_model->get_entity_by_group($this->input->getPost('group'));

        // retorna erro caso agrupamento não configurado corretamente
        if (!$this->user->config) {
            return json_encode(array(
                "status" => "error",
                "message" => "Dados não foram carregados corretamente. Configurações gerais do shopping não fornecidas."
            ));
        }

        $tabela = "esm_leituras_".$entity->tabela."_gas";

        $select = "esm_medidores.nome AS device, 
                esm_unidades_config.luc AS luc, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                esm_medidores.ultima_leitura AS value_read,
                m.value AS value_month,
                h.value AS value_month_open,
                m.value - h.value AS value_month_closed,
                l.value AS value_last,
                m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())) AS value_future,
                c.value AS value_last_month";

        if ($this->user->demo) {

            $tabela = "esm_leituras_".$entity->tabela."_gas_demo";

            $select = "esm_medidores.nome AS device, 
                esm_unidades_config.luc AS luc, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                RAND() * 10000 AS value_read,
                RAND() * 10000 AS value_month,
                RAND() * 10000 AS value_month_open,
                RAND() * 10000 AS value_month_closed,
                RAND() * 10000 AS value_last,
                RAND() * 10000 AS value_future,
                RAND() * 10000 AS value_last_month";
        }

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT $select
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            LEFT JOIN (  
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM $tabela
                JOIN esm_medidores ON esm_medidores.id = $tabela.medidor_id
                WHERE timestamp > UNIX_TIMESTAMP() - 86400
                GROUP BY medidor_id
            ) l ON l.device = esm_medidores.nome
            LEFT JOIN (
                SELECT esm_medidores.nome as device, SUM(consumo) AS value
                FROM esm_calendar
                LEFT JOIN $tabela d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                JOIN esm_medidores ON esm_medidores.id = d.medidor_id
                WHERE 
                    esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                    esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                GROUP BY d.medidor_id
            ) m ON m.device = esm_medidores.nome
            LEFT JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM $tabela
                JOIN esm_medidores ON esm_medidores.id = $tabela.medidor_id
                WHERE MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                GROUP BY medidor_id
            ) c ON c.device = esm_medidores.nome
            LEFT JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM $tabela
                JOIN esm_medidores ON esm_medidores.id = $tabela.medidor_id
                WHERE 
                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND 
                    YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now()) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$this->user->config->open})) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$this->user->config->close}))
                GROUP BY medidor_id
            ) h ON h.device = esm_medidores.nome
            WHERE 
                esm_unidades.agrupamento_id = ".$this->input->getPost("group")." AND
                esm_medidores.tipo = 'gas'
            ORDER BY 
            esm_unidades_config.type, esm_unidades.nome
        ");

        $dt->edit('type', function ($data) {
            if ($data["type"] == 1) {
                return "<span class=\"badge badge-warning\">".$this->user->config->area_comum."</span>";
            } else if ($data["type"] == 2) {
                return "<span class=\"badge badge-info\">Unidades</span>";
            }
        });

        $dt->edit('value_read', function ($data) {
            return str_pad(round($data["value_read"]), 6 , '0' , STR_PAD_LEFT);
        });

        $dt->edit('value_last', function ($data) {
            return number_format($data["value_last"], 0, ",", ".");
        });

        $dt->edit('value_month', function ($data) {
            return number_format($data["value_month"], 0, ",", ".");
        });

        $dt->edit('value_month_open', function ($data) {
            return number_format($data["value_month_open"], 0, ",", ".");
        });

        $dt->edit('value_month_closed', function ($data) {
            return number_format($data["value_month_closed"], 0, ",", ".");
        });

        $dt->edit('value_future', function ($data) {
            $icon = "";
            if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-up-alt text-danger ms-2\"></i>";
            else if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-down-alt text-success ms-2\"></i>";

            return number_format($data["value_future"], 0, ",", ".").$icon;
        });

        // gera resultados
        echo $dt->generate();
    }

    public function downloadResume()
    {
        $group_id = $this->input->getPost('id');

        // busca fechamento
        $group  = $this->shopping_model->get_group_info($group_id);

        $this->user->config = $this->shopping_model->get_client_config($group_id);

        if (!$this->user->config) {
            return json_encode(array(
                "status" => "error",
                "message" => "Dados não foram carregados corretamente. Configurações gerais não fornecidas."
            ));
        }

        $spreadsheet = new Spreadsheet();

        $titulos = [
            ['Mês', 'Aberto', 'Fechado', 'Últimas 24h', "Previsão Mês" ]
        ];

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório Resumo')
            ->setSubject(MonthName(date("m"))."/".date("Y"))
            ->setDescription('Relatório Resumo - '.date("01/m/Y").' - '.date("d/m/Y"))
            ->setKeywords($group->group_name.' Resumo '.MonthName(date("m"))."/".date("Y"))
            ->setCategory('Relatório')->setCompany('Easymeter');


        $spreadsheet->getActiveSheet()->setTitle($this->user->config->area_comum);

        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Unidades');
        $spreadsheet->addSheet($myWorkSheet, 1);

        for ($i = 0; $i < 2; $i++) {

            $spreadsheet->setActiveSheetIndex($i);

            $resume = $this->gas_model->GetResume($group_id, $this->user->config, $i + 1, $this->user->demo);

            $spreadsheet->getActiveSheet()->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($group->group_name));
            $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
            $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório Resumo - '. date("01/m/Y").' a '.date("d/m/Y"));
            $spreadsheet->getActiveSheet()->mergeCells('A2:H2');

            $spreadsheet->getActiveSheet()->setCellValue('A4', 'Medidor')->mergeCells('A4:A5');
            $spreadsheet->getActiveSheet()->setCellValue('B4', 'LUC')->mergeCells('B4:B5');
            $spreadsheet->getActiveSheet()->setCellValue('C4', 'Nome')->mergeCells('B4:B5');
            $spreadsheet->getActiveSheet()->setCellValue('D4', 'Leitura')->mergeCells('C4:C5');
            $spreadsheet->getActiveSheet()->setCellValue('E4', 'Consumo - L')->mergeCells('D4:H4');

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

            $spreadsheet->getActiveSheet()->setCellValue('A'.(count($resume) + 7), 'Gerado em '.date("d/m/Y H:i"));

            $spreadsheet->getActiveSheet()->setSelectedCell('A1');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = "Resumo Água ".$group->group_name;

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
}