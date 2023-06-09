<?php
/**
 * @property $water_controller
 */

namespace App\Controllers;

use App\Models\Water_model;
use App\Models\Shopping_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Water extends UNO_Controller
{
    protected $input;
    protected Datatables $datatables;

    private Shopping_model $shopping_model;
    private Water_model $water_model;

    public function __construct()
    {
        parent::__construct();

        // load models
        $this->shopping_model = new Shopping_model();
        $this->water_model = new Water_model();

        // load requests
        $this->input = \Config\Services::request();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);
    }

    private function chartConfig($type, $stacked, $series, $titles, $labels, $unit, $decimals, $extra = array(), $footer = "", $dates = array(), $decimals_chart = 0)
    {
        $config = array(
            "chart" => array(
                "type" => $type,
                "width" => "100%",
                "height" => 380,
                "foreColor" => "#777",
                "stacked" => $stacked,
                "toolbar" => array(
                    "show" => false
                ),
                "zoom" => array(
                    "enabled" => false
                ),
                "events" => array(
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
                "position" => "bottom"
            ),
            "tooltip" => array(
                "enabled" => true,
                //				"intersect" => false,
//				"shared"    => true,
                "x" => array(
                    "formatter" => "function",
                    "show" => true
                ),
                "y" => array(
                    "formatter" => "function",
                    "show" => true
                )
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $titles,
                    "decimals" => $decimals_chart,
                ),
                "unit" => $unit,
                "decimals" => $decimals,
                "custom" => $extra,
                "footer" => $footer,
                "dates" => $dates
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
        $html = '<div class="card-footer card-footer-chart total text-right d-none d-sm-block" data-loading-overlay="" data-loading-overlay-options="{ \"css\": { \"backgroundColor\": \"#00000080\" } }" style="">
                    <div class="row">';

        foreach ($data as $d) {

            $html .= '<div class="col text-center">
                        <div class="row">
                            <div class="col-6 col-lg-12">
                                <p class="text-3 mb-0" style="color: ' . $d[2] . ';">' . $d[0] . '</p>
                            </div>
                            <div class="col-6 col-lg-12">
                                <p class="text-3 mb-0" style="color: ' . $d[2] . ';">' . $d[1] . '</p>
                            </div>
                        </div>
                    </div>';
        }

        $html .= '</div></div>';

        return $html;
    }

    public function GetLancamentosAgua()
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        $gid = $this->input->getGet('gid') ?? $this->input->getPost('gid');
        if (is_null($gid))
            $gid = $this->user->group;

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT
                esm_fechamentos_agua.id,
                competencia AS competencia,
                DATE_FORMAT(inicio, '%d/%m/%Y') AS inicio,
                DATE_FORMAT(fim, '%d/%m/%Y') AS fim,
                FORMAT(consumo_c + consumo_u, 1, 'de_DE') AS consumo,
                FORMAT(consumo_c_o + consumo_u_o, 1, 'de_DE') AS consumo_o,
                FORMAT(consumo_c_c + consumo_u_c, 1, 'de_DE') AS consumo_c,
                DATE_FORMAT(cadastro, '%d/%m/%Y') AS emissao
            FROM
                esm_fechamentos_agua
            JOIN 
                esm_agrupamentos ON esm_agrupamentos.id = esm_fechamentos_agua.agrupamento_id AND esm_agrupamentos.id = $gid
            ORDER BY cadastro DESC
        ");
        $dt->edit('competencia', function ($data) {
            return strftime('%b/%Y', strtotime($data['competencia']));
        });

        // inclui actions
        $dt->add('action', function ($data) {
            return '<a href="#" class="action-water-download text-primary me-2" data-id="' . $data['id'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
				<a href="#" class="action-water-delete text-danger" data-id="' . $data['id'] . '"><i class="fas fa-trash" title="Excluir"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function get_lancamento_unity($entity_id)
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        $dt = $this->datatables->query("
        SELECT
            competencia AS competencia, 
            esm_fechamentos_agua.id AS fid, 
            esm_unidades.nome, 
            FORMAT(esm_fechamentos_agua.consumo_c + consumo_u, 1, 'de_DE') AS consumo, 
            FORMAT(esm_fechamentos_agua.consumo_c_o + consumo_u_o, 1, 'de_DE') AS consumo_o, 
            FORMAT(esm_fechamentos_agua.consumo_c_c + consumo_u_c, 1, 'de_DE') AS consumo_c, 
            DATE_FORMAT(cadastro, '%d/%m/%Y') AS emissao, 
            esm_fechamentos_agua_entradas.id AS entrada_id, 
            esm_unidades.id AS uid
        FROM
                esm_fechamentos_agua_entradas
        JOIN
                esm_medidores ON esm_medidores.nome = esm_fechamentos_agua_entradas.device
        JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
        JOIN
                esm_fechamentos_agua ON esm_fechamentos_agua.id = esm_fechamentos_agua_entradas.fechamento_id
        LEFT JOIN 
                esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
        WHERE
                esm_unidades.id = $entity_id
        ");

        $dt->edit('competencia', function ($data) {
            return strftime('%b/%Y', strtotime($data['competencia']));
        });

        // inclui actions
        $dt->add('action', function ($data) {
            return '<a href="#" class="action-download-unity text-primary me-2" data-fid="' . $data['fid'] . '" data-uid="' . $data['uid'] . '" data-eid="' . $data['entrada_id'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>';
        });

        echo $dt->generate();
    }

    public function DeleteLancamento()
    {
        $id = $this->input->getPost('id');

        echo $this->water_model->DeleteLancamento($id);
    }

    public function chart()
    {
        $field = $this->input->getPost('field');
        $shopping_id = $this->input->getPost('shopping_id');

        $this->user->config = $this->shopping_model->get_client_config($shopping_id);

        if (!$this->user->config) {
            return json_encode(
                array(
                    "status" => "error",
                    "message" => "Dados não foram carregados corretamente. Configurações gerais não fornecidas."
                )
            );
        }

        $divisor = 1;
        $decimals = 3;
        $unidade = "";
        $type = "line";

        $device = $this->input->getPost('device');
        $compare = $this->input->getPost('compare');
        $start = $this->input->getPost('start');
        $end = $this->input->getPost('end');

        $period = $this->water_model->GetConsumption($device, $shopping_id, $start, $end, array(), true, null, $this->user->demo);

        $period_o = $this->water_model->GetConsumption($device, $shopping_id, $start, $end, array("opened", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $period_c = $this->water_model->GetConsumption($device, $shopping_id, $start, $end, array("closed", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $main = $this->water_model->GetDeviceLastRead($device, $shopping_id);
        $month_o = $this->water_model->GetConsumption($device, $shopping_id, date("Y-m-01"), date("Y-m-d"), array("opened", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $month_c = $this->water_model->GetConsumption($device, $shopping_id, date("Y-m-01"), date("Y-m-d"), array("closed", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;

        $day_o = $this->water_model->GetConsumption($device, $shopping_id, date("Y-m-d", strtotime("-1 months")), date("Y-m-d"), array("opened", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;
        $day_c = $this->water_model->GetConsumption($device, $shopping_id, date("Y-m-d", strtotime("-1 months")), date("Y-m-d"), array("closed", $this->user->config->open, $this->user->config->close), false, null, $this->user->demo)[0]->value;

        $values = array();
        $labels = array();
        $titles = array();
        $dates = array();

        $max = -1;
        $min = 999999999;
        $unidade_medida = 'm³';
        $divisor = 1000;

        $series = array();

        if ($period) {
            foreach ($period as $v) {
                if ($max < floatval($v->value) && !is_null($v->value))
                    $max = floatval($v->value);
                if ($min > floatval($v->value) && !is_null($v->value))
                    $min = floatval($v->value);
            }

            foreach ($period as $v) {
                if (is_null($v->value)) {
                    $values[] = 0;
                } else {
                    if ($v->value == 0) {
                        $values[] = $max <= $v->value ? 0.05 : ($max / $divisor) * 0.005;
                    } else {
                        $values[] = $v->value / $divisor;
                    }
                }
                $labels[] = $v->label;

                if ($start == $end) {
                    $titles[] = $v->label . " - " . $v->next;
                } else {
                    $titles[] = $v->label . " - " . weekDayName($v->dw);
                    $dates[] = $v->date;
                }
            }

            $series[] = array(
                "name" => "Consumo",
                "data" => $values,
                "color" => "#007AB8",
            );
        }

        $max_c = -1;
        $min_c = 999999999;
        $colors = ['#734ba9', '#87a84b', '#a86f4b'];
        if ($compare != "") {
            foreach ($compare as $key => $c) {
                $values_c   = array();
                $comp = $this->water_model->GetConsumption($c, $shopping_id, $start, $end, array(), true, null, $this->user->demo);
                if ($comp) {

                    foreach ($comp as $v) {
                        if ($max_c < floatval($v->value) && !is_null($v->value))
                            $max_c = floatval($v->value);
                        if ($min_c > floatval($v->value) && !is_null($v->value))
                            $min_c = floatval($v->value);
                    }

                    foreach ($comp as $v) {
                        if (is_null($v->value)) {
                            $values_c[] = 0;
                        } else {
                            if ($v->value == 0) {
                                $values_c[] = $max_c <= $v->value ? 0.05 : ($max_c / $divisor) * 0.005;
                            } else {
                                $values_c[] = $v->value / $divisor;
                            }
                        }
                    }

                    $series[] = array(
                        "name" => ucfirst(mb_strtolower($this->shopping_model->GetUnidadeByDevice($c)->nome)),
                        "data" => $values_c,
                        "color" => $colors[array_rand($colors)],
                    );
                }
            }
        }

        $dias = ((strtotime(date("Y-m-d")) - strtotime(date("Y-m-01"))) / 86400) + 1;
        $dias_t = date('d', mktime(0, 0, 0, date("m") + 1, 0, date("Y")));
        $dias_m = (strtotime(date("Y-m-d")) - strtotime("-1 months")) / 86400;

        $extra = array(
            "main" => ($main ? str_pad(round($main), 6, '0', STR_PAD_LEFT) : "- - - - - -") . " <span style='font-size:12px;'>L</span>",
            "period" => number_format(round(($period_o + $period_c) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "period_o" => number_format(round($period_o / $divisor , $decimals ), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "period_c" => number_format(round($period_c / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "month" => number_format(round(($month_o + $month_c) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "month_o" => number_format(round($month_o / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "month_c" => number_format(round($month_c / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "prevision" => number_format(round((($month_o + $month_c) / $dias * $dias_t) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "prevision_o" => number_format(round(($month_o / $dias * $dias_t) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "prevision_c" => number_format(round(($month_c / $dias * $dias_t) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "day" => number_format(round((($day_o + $day_c) / $dias_m) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "day_o" => number_format(round(($day_o / $dias_m) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "day_c" => number_format(round(($day_c / $dias_m) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "max" => $max / $divisor,
            "max_c" => $max_c / $divisor
        );

        $data = array(
            array("Máximo", ($max == -1) ? "-" : number_format(round($max / $divisor), 0, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#268ec3"),
            array("Mínimo", ($min == 999999999) ? "-" : number_format(round($min / $divisor), 0, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#268ec3"),
            array("Médio", ($min == 999999999) ? "-" : number_format(round((($period_o + $period_c) / $divisor) / count($period)), 0, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#268ec3"),
        );

        $footer = $this->chartFooter($data);

        $config = $this->chartConfig("bar", false, $series, $titles, $labels, $unidade_medida, $decimals, $extra, $footer, $dates, $decimals);

        if ($max == 0) {
            $config["yaxis"] = array("labels" => array("formatter" => "function"), "tickAmount" => 5,"min" => 0,"max" => 10);
        }

        echo json_encode($config);
    }

    public function resume()
    {
        $this->user->config = $this->shopping_model->get_client_config($this->input->getPost('group'));

        if (!$this->user->config) {
            return json_encode(
                array(
                    "status" => "error",
                    "message" => "Dados não foram carregados corretamente. Configurações gerais do shopping não fornecidas."
                )
            );
        }

        $entity = $this->shopping_model->get_entity_by_group($this->input->getPost('group'));

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                esm_medidores.ultima_leitura AS value_read,
                m.value AS value_month,
                l.value AS value_last,
                m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())) AS value_future,
                c.value AS value_last_month
            FROM esm_medidores
            LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            LEFT JOIN (  
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_" . $entity->tabela . "_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_agua.medidor_id
                WHERE timestamp > UNIX_TIMESTAMP() - 86400
                GROUP BY medidor_id
            ) l ON l.device = esm_medidores.nome
            LEFT JOIN (
                SELECT esm_medidores.nome as device, SUM(consumo) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_" . $entity->tabela . "_agua d ON 
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
                FROM esm_leituras_" . $entity->tabela . "_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_agua.medidor_id
                WHERE MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                GROUP BY medidor_id
            ) c ON c.device = esm_medidores.nome
            LEFT JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_" . $entity->tabela . "_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_agua.medidor_id
                WHERE 
                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND 
                    YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now()) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$this->user->config->open})) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$this->user->config->close}))
                GROUP BY medidor_id
            ) h ON h.device = esm_medidores.nome
            WHERE 
                esm_unidades.agrupamento_id = " . $this->input->getPost("group") . " AND
                esm_medidores.tipo = 'agua'
            ORDER BY 
            esm_unidades_config.type, esm_unidades.nome
        ");

        $dt->edit('type', function ($data) {
            if ($data["type"] == 1) {
                return "<span class=\"badge badge-warning\">" . $this->user->config->area_comum . "</span>";
            } else if ($data["type"] == 2) {
                return "<span class=\"badge badge-info\">Unidades</span>";
            }
        });

        $dt->edit('value_read', function ($data) {
            return str_pad(round($data["value_read"] ), 6, '0', STR_PAD_LEFT);
        });

        $dt->edit('value_last', function ($data) {
            return number_format(($data["value_last"] / 1000), 3, ",", ".");
        });

        $dt->edit('value_month', function ($data) {
            return number_format(($data["value_month"] / 1000), 3, ",", ".");
        });

        $dt->edit('value_last_month', function ($data) {
            return number_format(($data["value_last_month"] / 1000), 3, ",", ".");
        });

        $dt->edit('value_future', function ($data) {
            $icon = "";
            if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-up-alt text-danger ms-2\"></i>";
            else if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-down-alt text-success ms-2\"></i>";

            return number_format(($data["value_future"] / 1000), 3, ",", ".") . $icon;
        });

        // gera resultados
        echo $dt->generate();
    }

    public function downloadResume()
    {
        $group_id = $this->input->getPost('id');

        // busca fechamento
        $group = $this->shopping_model->get_group_info($group_id);

        $this->user->config = $this->shopping_model->get_client_config($group_id);

        if (!$this->user->config) {
            return json_encode(
                array(
                    "status" => "error",
                    "message" => "Dados não foram carregados corretamente. Configurações gerais não fornecidas."
                )
            );
        }

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        /*
                if(!$resume) {
                    // mostra erro
                    echo json_encode(array("status"  => "error", "message" => "Resumo não encontrado"));
                    return;
                }
        */
        $spreadsheet = new Spreadsheet();

        $titulos = [
            ['Mês Atual', 'Últimas 24h', 'Mês Anterior', "Previsão Mês" ]
        ];

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório Resumo')
            ->setSubject(MonthName(date("m")) . "/" . date("Y"))
            ->setDescription('Relatório Resumo - ' . date("01/m/Y") . ' - ' . date("d/m/Y"))
            ->setKeywords($group->group_name . ' Resumo ' . MonthName(date("m")) . "/" . date("Y"))
            ->setCategory('Relatório')->setCompany('Easymeter');


        $spreadsheet->getActiveSheet()->setTitle($this->user->config->area_comum);

        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Unidades');
        $spreadsheet->addSheet($myWorkSheet, 1);

        for ($i = 0; $i < 2; $i++) {

            $spreadsheet->setActiveSheetIndex($i);

            $resume = $this->water_model->GetResume($group_id, $this->user->config, $i + 1, $this->user->demo);

            $spreadsheet->getActiveSheet()->getStyle('A1:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($group->group_name));
            $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
            $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório Resumo - ' . date("01/m/Y") . ' a ' . date("d/m/Y"));
            $spreadsheet->getActiveSheet()->mergeCells('A2:G2');

            $spreadsheet->getActiveSheet()->setCellValue('A4', 'Medidor')->mergeCells('A4:A5');
            $spreadsheet->getActiveSheet()->setCellValue('B4', 'Nome')->mergeCells('B4:B5');
            $spreadsheet->getActiveSheet()->setCellValue('C4', 'Leitura - m³')->mergeCells('C4:C5');
            $spreadsheet->getActiveSheet()->setCellValue('D4', 'Consumo - m³')->mergeCells('D4:G4');

            $spreadsheet->getActiveSheet()->getStyle('A1:J5')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A4:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
            $spreadsheet->getActiveSheet()->getStyle('C6:J'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $spreadsheet->getActiveSheet()->setCellValue('A' . (count($resume) + 7), 'Gerado em ' . date("d/m/Y H:i"));

            $spreadsheet->getActiveSheet()->setSelectedCell('A1');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = "Resumo Água " . $group->group_name;

        ob_start();
        $writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response = array(
            'status' => "success",
            'name' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function generateResume()
    {
        $group_id = $this->input->getPost('group');
        $start = $this->input->getPost('dados[start]');
        $end = $this->input->getPost('dados[end]');
        $device = $this->input->getPost('dados[device]');
        $compare = $this->input->getPost('dados[compare]');

        // busca fechamento
        $group = $this->shopping_model->get_group_info($group_id);

        $this->user->config = $this->shopping_model->get_client_config($group_id);

        if (!$this->user->config) {
            return json_encode(
                array(
                    "status" => "error",
                    "message" => "Dados não foram carregados corretamente. Configurações gerais não fornecidas."
                )
            );
        }

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        /*
                if(!$resume) {
                    // mostra erro
                    echo json_encode(array("status"  => "error", "message" => "Resumo não encontrado"));
                    return;
                }
        */
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório Resumo')
            ->setSubject(MonthName(date("m")) . "/" . date("Y"))
            ->setDescription('Relatório Resumo - ' . date($start) . ' - ' . date($end))
            ->setKeywords($group->group_name . ' Resumo ' . MonthName(date("m")) . "/" . date("Y"))
            ->setCategory('Relatório')->setCompany('Easymeter');


        $spreadsheet->getActiveSheet()->setTitle('Medidores');

        //if ($start === $end) {
        if ($device === "T") {
            $medidores = $this->shopping_model->get_medidores_by_group($group_id, 'agua');

            foreach ($medidores as $i => $medidor) {
                $hours = $this->water_model->GetConsumption($medidor->nome, $group_id, $start, $end);

                $resume[$i] = [$medidor->nome, $medidor->unidade];

                foreach ($hours as $h) {
                    $resume[$i][] = $h->value / 1000;
                }
            }
        } else {
            if (empty($compare)) {
                $medidor = $this->shopping_model->get_medidor($device);
                $hours = $this->water_model->GetConsumption($medidor->nome, $group_id, $start, $end);

                $resume = [$medidor->nome, $medidor->unidade];

                foreach ($hours as $h) {
                    $resume[] = $h->value / 1000;
                }
            } else {
                $medidores = array();

                $medidores[] = $this->shopping_model->get_medidor($device);

                foreach ($compare as $c) {
                    $medidores[] = $this->shopping_model->get_medidor($c);
                }

                foreach ($medidores as $i => $medidor) {
                    $hours = $this->water_model->GetConsumption($medidor->nome, $group_id, $start, $end);

                    $resume[$i] = [$medidor->nome, $medidor->unidade];

                    foreach ($hours as $h) {
                        $resume[$i][] = $h->value / 1000;
                    }
                }
            }
        }

        if ($start === $end) {
            $startColumn = 'C';
            $endColumn = chr(ord($startColumn) + 23);
            $startRow = 5;
            $spreadsheet->getActiveSheet()->setCellValue($startColumn . '4', 'Consumo - m³')->mergeCells($startColumn . '4:' . $endColumn . '4');
            for ($hour = 0; $hour < 24; $hour++) {
                $column = $startColumn;
                $row = $startRow;

                $hourLabel = $hour . 'h';

                $spreadsheet->getActiveSheet()->setCellValue($column . $row, $hourLabel);

                $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(11);

                $spreadsheet->getActiveSheet()->getStyle($column . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $spreadsheet->getActiveSheet()->getStyle($column . $row)->getFont()->setBold(true);

                $startColumn++;
            }
        } else {

            $data1 = strtotime($start);
            $data2 = strtotime($end);

            $dias = abs($data2 - $data1) / (60 * 60 * 24);

            $endColumn = num2alpha($dias);

            $startColumn = 'C';
            $startRow = 5;
            $spreadsheet->getActiveSheet()->setCellValue($startColumn . '4', 'Consumo - m³')->mergeCells($startColumn . '4:' . num2alpha($dias + 3) . '4');
            for ($i = 0; $i <= $dias; $i++) {
                $column = $startColumn;
                $row = $startRow;

                $label = $hours[$i]->label;

                $spreadsheet->getActiveSheet()->setCellValue($column . $row, $label);

                $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(11);

                $spreadsheet->getActiveSheet()->getStyle($column . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $spreadsheet->getActiveSheet()->getStyle($column . $row)->getFont()->setBold(true);

                $startColumn++;
            }
        }

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Medidor')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Nome')->mergeCells('B4:B5');

        $spreadsheet->getActiveSheet()->getStyle('A1:C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($group->group_name));
        $spreadsheet->getActiveSheet()->mergeCells('A1:C1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório Resumo - ' . date('d/m/Y', strtotime($start) ) . ' a ' . date('d/m/Y', strtotime($end)));
        $spreadsheet->getActiveSheet()->mergeCells('A2:C2');

        $spreadsheet->getActiveSheet()->getStyle('A1:C5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($resume, NULL, 'A6');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);

        $spreadsheet->getActiveSheet()->getStyle('A6:A'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('B6:B'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C6:C'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $spreadsheet->getActiveSheet()->setCellValue('A' . (count($resume) + 7), 'Gerado em ' . date("d/m/Y H:i"));

        $spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = "Resumo Água " . $group->group_name;

        ob_start();
        $writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response = array(
            'status' => "success",
            'name' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function lancamento()
    {
        $comp = str_replace('/', '-', $this->input->getPost('tar-water-competencia'));
        $ini = str_replace('/', '-', $this->input->getPost('tar-water-data-ini'));
        $end = str_replace('/', '-', $this->input->getPost('tar-water-data-fim'));

        $data = array(
            "agrupamento_id"    => $this->input->getPost('tar-water-group'),
            "competencia" => date('Y-m-d', strtotime('01-' . $comp)),
            "inicio" => date('Y-m-d', strtotime($ini)),
            "fim" => date('Y-m-d', strtotime($end)),
            "mensagem" => $this->input->getPost('tar-water-msg'),
        );

        $this->user->config = $this->shopping_model->get_client_config($data['agrupamento_id']);

        if (!$this->user->config) {
            return json_encode(
                array(
                    "status" => "error",
                    "message" => "Dados não foram carregados corretamente. Configurações gerais do shopping não fornecidas."
                )
            );
        }

        if ($this->water_model->VerifyCompetencia($data["agrupamento_id"], $data["competencia"])) {
            echo '{ "status": "message", "field": "tar-water-competencia", "message" : "Competência já possui lançamento"}';
            return;
        }

        if ($data["inicio"] == $data["fim"]) {
            echo '{ "status": "message", "field": "tar-water-data-fim", "message" : "Data final igual a inicial"}';
            return;
        }

        if ($data["inicio"] > $data["fim"]) {
            echo '{ "status": "message", "field": "tar-water-data-fim", "message" : "Data final menor que a inicial"}';
            return;
        }

        echo $this->water_model->Calculate($data, $this->user->config, $data['agrupamento_id']);
    }

    public function GetLancamentoUnidades($type = 0)
    {
        $fid = $this->input->getPost('fid');
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');


        $where = "";
        if ($type)
            $where = "AND esm_fechamentos_agua_entradas.type = $type";

        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.nome,
                esm_unidades_config.luc as luc,
                leitura_anterior,
                leitura_atual,
                consumo,
                esm_fechamentos_agua_entradas.id AS DT_RowId
            FROM 
                esm_fechamentos_agua_entradas
            JOIN 
                esm_medidores ON esm_medidores.nome = esm_fechamentos_agua_entradas.device
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN
                esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            WHERE 
                esm_fechamentos_agua_entradas.fechamento_id = $fid
                $where
        ");

        $dt->edit('leitura_anterior', function ($data) {
            return str_pad(round($data["leitura_anterior"]), 6, '0', STR_PAD_LEFT);
        });

        $dt->edit('leitura_atual', function ($data) {
            return str_pad(round($data["leitura_atual"]), 6, '0', STR_PAD_LEFT);
        });



        $dt->edit('consumo', function ($data) {
            return number_format($data['consumo'], 0, ",", ".");
        });


        echo $dt->generate();
    }

    public function download()
    {
        $fechamento_id = $this->input->getPost('id');

        // busca fechamento
        $fechamento = $this->water_model->GetLancamento($fechamento_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if (!$fechamento || is_null($fechamento->id)) {
            // mostra erro
            echo json_encode(array("status" => "error", "message" => "Lançamento não encontrado"));
            return;
        }

        $spreadsheet = new Spreadsheet();

        $titulos = [
            ['Unidade', 'Leitura Anterior', 'Leitura Atual', 'Consumo - L' ]
        ];

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório de Consumo')
            ->setSubject(strftime('%B/%Y', strtotime($fechamento->competencia)))
            ->setDescription('Relatório de Consumo - '.$fechamento->nome.' - '.$fechamento->competencia)
            ->setKeywords($fechamento->nome.' '.strftime('%B/%Y', strtotime($fechamento->competencia)))
            ->setCategory('Relatório')->setCompany('Easymeter');

        $split = 0;

        for ($i = 0; $i <= $split; $i++) {

            $spreadsheet->setActiveSheetIndex($i);

            $spreadsheet->getActiveSheet()->getStyle('A1:D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($fechamento->nome));
            $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
            $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Consumo de Água - '.date("d/m/Y", strtotime($fechamento->inicio)).' a '.date("d/m/Y", strtotime($fechamento->fim)));
            $spreadsheet->getActiveSheet()->mergeCells('A2:D2');

            $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'A4');

            $spreadsheet->getActiveSheet()->getStyle('A1:D4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $linhas = $this->water_model->GetLancamentoUnidades($fechamento_id, $this->user->config, $i + 1);
            $spreadsheet->getActiveSheet()->fromArray($linhas, NULL, 'A5');

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);

            $spreadsheet->getActiveSheet()->getStyle('B5:D'.(count($linhas) + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $spreadsheet->getActiveSheet()->setCellValue('A' . (count($linhas) + 7), 'Gerado em ' . date("d/m/Y H:i"));

            $spreadsheet->getActiveSheet()->setSelectedCell('A1');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = $fechamento->nome.' Água - '. strftime('%B/%Y', strtotime($fechamento->competencia));

        ob_start();
        $writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response = array(
            'status' => "success",
            'name' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function DownloadLancamentos()
    {
        $group_id = $this->input->getPost('id');

        // busca fechamento
        $fechamentos = $this->water_model->GetLancamentos($group_id);
        $group = $this->shopping_model->get_group_info($group_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if (!$fechamentos) {
            // mostra erro
            echo json_encode(array("status" => "error", "message" => "Nenhum lançamento encontrado"));
            return;
        }
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        foreach($fechamentos as &$f) {
            $f['competencia'] = strftime('%B/%Y', strtotime($f['competencia']));
        }

        $spreadsheet = new Spreadsheet();

        $titulos = [
            ['Total', 'Aberto', 'Fechado']
        ];

        $spreadsheet->getProperties()
            ->setCreator('Easymeter')
            ->setLastModifiedBy('Easymeter')
            ->setTitle('Relatório de Lançamentos - Água')
            ->setSubject($group->group_name)
            ->setDescription('Relatório de Lançamentos - Água - ' . $group->group_name)
            ->setKeywords($group->group_name . ' Lançamentos Água')
            ->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($group->group_name));
        $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Lançamentos - Água');
        $spreadsheet->getActiveSheet()->mergeCells('A2:G2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Competência')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Data Inicial')->mergeCells('B4:B5');
        $spreadsheet->getActiveSheet()->setCellValue('C4', 'Data Final')->mergeCells('C4:C5');
        $spreadsheet->getActiveSheet()->setCellValue('D4', 'Consumo - L')->mergeCells('D4:F4');
        $spreadsheet->getActiveSheet()->setCellValue('G4', 'Emissão')->mergeCells('G4:G5');

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

        $spreadsheet->getActiveSheet()->getStyle('A6:G' . (count($fechamentos) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A' . (count($fechamentos) + 7), 'Gerado em ' . date("d/m/Y H:i"));

        $spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);

        $filename = "Lançamentos Água " . $group->group_name;

        ob_start();
        $writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response = array(
            'status' => "success",
            'name' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );

        echo json_encode($response);
    }

    public function resume_demo()
    {
        $this->user->config = $this->shopping_model->get_client_config($this->input->getPost('group'));

        if (!$this->user->config) {
            return json_encode(
                array(
                    "status" => "error",
                    "message" => "Dados não foram carregados corretamente. Configurações gerais do shopping não fornecidas."
                )
            );
        }

        $entity = $this->shopping_model->get_entity_by_group($this->input->getPost('group'));

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
            SELECT 
                $select
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            LEFT JOIN (  
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_" . $entity->tabela . "_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_agua.medidor_id
                WHERE timestamp > UNIX_TIMESTAMP() - 86400
                GROUP BY medidor_id
            ) l ON l.device = esm_medidores.nome
            LEFT JOIN (
                SELECT esm_medidores.nome as device, SUM(consumo) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_" . $entity->tabela . "_agua d ON 
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
                FROM esm_leituras_" . $entity->tabela . "_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_agua.medidor_id
                WHERE MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                GROUP BY medidor_id
            ) c ON c.device = esm_medidores.nome
            LEFT JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_" . $entity->tabela . "_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_agua.medidor_id
                WHERE 
                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND 
                    YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now()) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$this->user->config->open})) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$this->user->config->close}))
                GROUP BY medidor_id
            ) h ON h.device = esm_medidores.nome
            WHERE 
                esm_unidades.agrupamento_id = " . $this->input->getPost("group") . " AND
                esm_medidores.tipo = 'agua'
            ORDER BY 
            esm_unidades_config.type, esm_unidades.nome
        ");

        $dt->edit('type', function ($data) {
            if ($data["type"] == 1) {
                return "<span class=\"badge badge-warning\">" . $this->user->config->area_comum . "</span>";
            } else if ($data["type"] == 2) {
                return "<span class=\"badge badge-info\">Unidades</span>";
            }
        });

        $dt->edit('value_read', function ($data) {
            return str_pad(round($data["value_read"]), 6, '0', STR_PAD_LEFT);
        });

        $dt->edit('value_last', function ($data) {
            return number_format($data["value_last"], 0, ",", ".");
        });

        $dt->edit('value_month', function ($data) {
            return number_format($data["value_month"], 0, ",", ".");
        });

        $dt->edit('value_last_month', function ($data) {
            return number_format($data["value_last_month"], 0, ",", ".");
        });

        $dt->edit('value_future', function ($data) {
            $icon = "";
            if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-up-alt text-danger ms-2\"></i>";
            else if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-down-alt text-success ms-2\"></i>";

            return number_format($data["value_future"], 0, ",", ".") . $icon;
        });

        // gera resultados
        echo $dt->generate();
    }

    private function GetVazamentoInsights($group)
    {
        $entity = $this->shopping_model->get_entity_by_group($group);

        $total = $this->water_model->GetMonthByStationWaterAlert($group, $this->user->demo);
        $factor = 1;

        $value = "SUM(esm_alertas.consumo_horas) AS value";
        if ($this->user->demo) {
            $value = "RAND() * 1 AS value";
            $group = 113;
        }

        // realiza a query via dt
        $dt = $this->datatables->query("
        SELECT 	
            esm_unidades.nome AS name,
            $value 	
        from esm_alertas
            JOIN esm_medidores 	ON esm_medidores.id = esm_alertas.medidor_id
            JOIN esm_unidades 	ON esm_unidades.id = esm_alertas.unidade_id
        where esm_alertas.tipo = 'vazamento' and esm_unidades.agrupamento_id = $group
        GROUP BY esm_alertas.medidor_id, esm_alertas.unidade_id
        ORDER BY value DESC 
        ");

        $dt->add('id', function ($data) {
            return "-";
        });

        $dt->edit('value', function ($data) use ($factor) {
            if ($data['value'] > 999) {
                $divisor = 1000;
                $uni_med = ' m³';
            } else
            {
                $divisor = 1;
                $uni_med = ' L';
            }
            return number_format(($data["value"] / $divisor) * $factor, 3, ",", ".") . $uni_med;
        });

        $dt->add('percentage', function ($data) use ($total, $factor) {
            $v = round(($data['value'] * $factor) / $total * 100);
            return "<div class=\"progress progress-sm progress-half-rounded m-0 mt-1 light\">
                <div class=\"progress-bar progress-bar-primary t-$total\" role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: $v%;\">
                </div>
            </div>";
        });

        $dt->add('participation', function ($data) use ($total, $factor) {
            return number_format(round(($data['value'] * $factor) / $total * 100, 1), 1, ",", ".") . "%";
        });

        echo $dt->generate();

    }

    private function GetFactorInsightWater($group)
    {
        $entity = $this->shopping_model->get_entity_by_group($group);

        $value = "p.value AS value";
        $tabela = "esm_leituras_" . $entity->tabela . "_agua";
        if ($this->user->demo) {
            $value = "RAND() * 1 AS value";
            $tabela = "esm_leituras_" . $entity->tabela . "_agua_demo";
            $group = 113;
        }

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.nome AS name, 
                $value
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN (
                SELECT 
                    d.medidor_id,
                    IFNULL(SUM(consumo),1) AS value
                FROM esm_calendar
                LEFT JOIN $tabela d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                WHERE 
                    esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                    esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                GROUP BY d.medidor_id
            ) p ON p.medidor_id = esm_medidores.id
            WHERE 
               esm_unidades.agrupamento_id = $group
            ORDER BY 
                value
            LIMIT 10
        ");

        $dt->add('id', function ($data) {
            return "-";
        });

        $dt->edit('value', function ($data) {
            return number_format($data["value"], 3, ",", ".");
        });

        return $dt->generate();
    }

    public function insights($iud)
    {
        $group = $this->input->getPost("group");

        $entity = $this->shopping_model->get_entity_by_group($group);

        $this->user->config = $this->shopping_model->get_client_config($group);

        if (!$this->user->config) {
            return json_encode(
                array(
                    "status" => "error",
                    "message" => "Dados não foram carregados corretamente. Configurações gerais do shopping não fornecidas."
                )
            );
        }

        // Query que calcula os valores totais e faz a participação em porcentagem

        $st = "";
        $total = false;
        $factor = 1;
        if ($iud == 1) {
            $st = "consumo";
            $total = $this->water_model->GetMonthByStationWater(array($st, $this->user->config->ponta_start, $this->user->config->ponta_end), $group, $this->user->demo);
        } else if ($iud == 2) {
            $st = "vazamento";
            $total = $this->water_model->GetMonthByStationWater(array($st, $this->user->config->ponta_start, $this->user->config->ponta_end), $group, $this->user->demo);

            echo $this->GetVazamentoInsights($group);
            return;
        }

        $value = "p.value AS value";
        $tabela = "esm_leituras_" . $entity->tabela . "_agua";
        if ($this->user->demo) {
            $value = "RAND() * 1000 AS value";
            $tabela = "esm_leituras_" . $entity->tabela . "_agua_demo";
            $group = 113;
        }

        // realiza a query medidor e consumo via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.nome AS name, 
                $value
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN (
                    SELECT 
                        d.medidor_id,
                        SUM(consumo) AS value
                    FROM esm_calendar
                    LEFT JOIN $tabela d ON 
                        (d.timestamp) > (esm_calendar.ts_start) AND 
                        (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    WHERE 
                        esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                        esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.medidor_id
                ) p ON p.medidor_id = esm_medidores.id
            WHERE 
                esm_unidades.agrupamento_id = $group
            ORDER BY value DESC LIMIT 10");

        $dt->add('id', function ($data) {
            return "-";
        });

        $dt->edit('value', function ($data) use ($factor) {
            if ($data["value"] > 999) {
                $divisor = 1000;
            } else {
                $divisor = 1;
            }
            return number_format(($data["value"] / $divisor) * $factor, 3, ",", ".") . ($divisor == 10000 ? " m³" : " L");

        });

        $dt->add('percentage', function ($data) use ($total, $factor) {
            $v = round(($data['value'] * $factor) / ($total) * 100);
            return "<div class=\"progress progress-sm progress-half-rounded m-0 mt-1 light\">
                <div class=\"progress-bar progress-bar-primary t-$total\" role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: $v%;\">
                </div>
            </div>";
        });

        $dt->add('participation', function ($data) use ($total, $factor) {
            return number_format(round(($data['value'] * $factor) / $total * 100, 1), 1, ",", ".") . "%";
        });

        echo $dt->generate();
    }
}