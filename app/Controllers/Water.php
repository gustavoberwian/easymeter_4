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

	public function GetLancamentosAgua()
	{
        $gid = $this->input->getGet('gid') ?? $this->input->getPost('gid');

		if (is_null($gid)) 
            $gid = $this->user->group;

		// realiza a query via dt
		$dt = $this->datatables->query("
            SELECT
                esm_fechamentos_agua.id,
                competencia,
                FROM_UNIXTIME(inicio, '%d/%m/%Y') AS inicio,
                FROM_UNIXTIME(fim, '%d/%m/%Y') AS fim,
                FORMAT(consumo_c + consumo_u, 1, 'de_DE') AS consumo,
                FORMAT(consumo_c_o + consumo_u_o, 1, 'de_DE') AS consumo_o,
                FORMAT(consumo_c_c + consumo_u_c, 1, 'de_DE') AS consumo_c,
                DATE_FORMAT(cadastro, '%d/%m/%Y') AS emissao
            FROM
                esm_fechamentos_agua
            JOIN 
                esm_blocos ON esm_blocos.id = esm_fechamentos_agua.group_id AND esm_blocos.id = $gid
            ORDER BY cadastro DESC
        ");

		$dt->edit('competencia', function ($data) {
			return competencia_nice($data['competencia']);
		});

		// inclui actions
		$dt->add('action', function ($data) {
			return '<a href="#" class="action-water-download text-primary me-2" data-id="' . $data['id'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
				<a href="#" class="action-water-delete text-danger" data-id="' . $data['id'] . '"><i class="fas fa-trash" title="Excluir"></i></a>';
		});

		// gera resultados
		echo $dt->generate();
	}

    public function DeleteLancamento()
	{
		$id = $this->input->getPost('id');

		echo $this->water_model->DeleteLancamento($id);
	}

    public function chart()
    {
        $field    = $this->input->getPost('field');
        $shopping_id    = $this->input->getPost('shopping_id');
        $divisor  = 1;
        $decimals = 0;
        $unidade  = "";
        $type     = "line";

        $device   = $this->input->getPost('device');
        $compare  = $this->input->getPost('compare');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');

        $period   = $this->water_model->GetConsumption($device, $start, $end);

        $period_o = $this->water_model->GetConsumption($device, $start, $end, array("opened", $this->user->config->open, $this->user->config->close), false)[0]->value;
        $period_c = $this->water_model->GetConsumption($device, $start, $end, array("closed", $this->user->config->open, $this->user->config->close), false)[0]->value;
        $main     = $this->water_model->GetDeviceLastRead($device, $shopping_id);
        $month_o  = $this->water_model->GetConsumption($device, date("Y-m-01"), date("Y-m-d"), array("opened", $this->user->config->open, $this->user->config->close), false)[0]->value;
        $month_c  = $this->water_model->GetConsumption($device, date("Y-m-01"), date("Y-m-d"), array("closed", $this->user->config->open, $this->user->config->close), false)[0]->value;

        $day_o  = $this->water_model->GetConsumption($device, date("Y-m-d", strtotime("-1 months")), date("Y-m-d"), array("opened", $this->user->config->open, $this->user->config->close), false)[0]->value;
        $day_c  = $this->water_model->GetConsumption($device, date("Y-m-d", strtotime("-1 months")), date("Y-m-d"), array("closed", $this->user->config->open, $this->user->config->close), false)[0]->value;

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
            $comp = $this->water_model->GetConsumption($compare, $start, $end);
            if ($comp) {
                foreach ($comp as $v) {
                    $values_c[] = $v->value;
                }
     
                $series[] = array(
                    "name"  => $this->shopping_model->GetUnidadeByDevice($compare)->nome,
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
        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades_config.luc AS luc, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                esm_medidores.ultima_leitura AS value_read,
                m.value AS value_month,
                h.value AS value_month_open,
                m.value - h.value AS value_month_closed,
                l.value AS value_last,
                m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())) AS value_future,
                c.value AS value_last_month
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            JOIN (  
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_ancar_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                WHERE timestamp > UNIX_TIMESTAMP() - 86400
                GROUP BY medidor_id
            ) l ON l.device = esm_medidores.nome
            JOIN (
                SELECT esm_medidores.nome as device, SUM(consumo) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_agua d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                JOIN esm_medidores ON esm_medidores.id = d.medidor_id
                WHERE 
                    esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                    esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                GROUP BY d.medidor_id
            ) m ON m.device = esm_medidores.nome
            JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_ancar_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                WHERE MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                GROUP BY medidor_id
            ) c ON c.device = esm_medidores.nome
            JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_ancar_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                WHERE 
                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND 
                    YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now()) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$this->user->config->open})) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$this->user->config->close}))
                GROUP BY medidor_id
            ) h ON h.device = esm_medidores.nome
            WHERE 
                entrada_id = 73
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
    
            $resume = $this->water_model->GetResume($this->user->config, $i + 1);

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
    
    public function lancamento()
    {
        $data = array(
            "group_id"    => $this->input->getPost('tar-water-group'),
            "entrada_id"  => 73,
            "competencia" => $this->input->getPost('tar-water-competencia'),
            "inicio"      => $this->input->getPost('tar-water-data-ini'),
            "fim"         => $this->input->getPost('tar-water-data-fim'),
            "mensagem"    => $this->input->getPost('tar-water-msg'),
        );

        if ($this->water_model->VerifyCompetencia($data["entrada_id"], $data["competencia"])) {
			echo '{ "status": "message", "field": "tar-water-competencia", "message" : "Competência já possui lançamento"}';
			return;
		}
        
        if (date_create_from_format('d/m/Y', $data["inicio"])->format('U') == date_create_from_format('d/m/Y', $data["fim"])->format('U')) {
			echo '{ "status": "message", "field": "tar-water-data-fim", "message" : "Data final igual a inicial"}';
			return;
		}

		if (date_create_from_format('d/m/Y', $data["inicio"])->format('U') > date_create_from_format('d/m/Y', $data["fim"])->format('U')) {
			echo '{ "status": "message", "field": "tar-water-data-fim", "message" : "Data final menor que a inicial"}';
			return;
		}

        echo $this->water_model->Calculate($data, $this->user->config);
    }

    public function GetLancamentoUnidades($type = 0)
    {
        $fid = $this->input->getPost('fid');

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
                consumo_o,
                consumo_c,
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
            return str_pad(round($data["leitura_anterior"]), 6 , '0' , STR_PAD_LEFT);
        });

        $dt->edit('leitura_atual', function ($data) {
            return str_pad(round($data["leitura_atual"]), 6 , '0' , STR_PAD_LEFT);
        });

        $dt->edit('consumo', function ($data) {
            return number_format($data['consumo'], 0, ",", ".");
        });

        $dt->edit('consumo_o', function ($data) {
            return number_format($data['consumo_o'], 0, ",", ".");
        });

        $dt->edit('consumo_c', function ($data) {
            return number_format($data['consumo_c'], 0, ",", ".");
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
        if(!$fechamento || is_null($fechamento->id)) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Lançamento não encontrado"));
            return;
        }

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Unidade', 'LUC', 'Leitura Anterior', 'Leitura Atual', 'Consumo - L' ]
		];

        $spreadsheet->getProperties()
			->setCreator('Easymeter')
			->setLastModifiedBy('Easymeter')
			->setTitle('Relatório de Consumo')
			->setSubject(competencia_nice($fechamento->competencia))
			->setDescription('Relatório de Consumo - '.$fechamento->nome.' - '.$fechamento->competencia)
			->setKeywords($fechamento->nome.' '.competencia_nice($fechamento->competencia))
			->setCategory('Relatório')->setCompany('Easymeter');

        $split = 0;

        for ($i = 0; $i <= $split; $i++) {

            $spreadsheet->setActiveSheetIndex($i);

            $spreadsheet->getActiveSheet()->getStyle('A1:E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($fechamento->nome));
            $spreadsheet->getActiveSheet()->mergeCells('A1:E1');
            $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Consumo de Água - '.date("d/m/Y", $fechamento->inicio).' a '.date("d/m/Y", $fechamento->fim));
            $spreadsheet->getActiveSheet()->mergeCells('A2:E2');

            $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'A4');

            $spreadsheet->getActiveSheet()->getStyle('A1:E4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


            $linhas = $this->water_model->GetLancamentoUnidades($fechamento_id, $this->user->config, $i + 1);

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
 
        $filename = $fechamento->nome.' Água - '.competencia_nice($fechamento->competencia, ' ');

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

    public function DownloadLancamentos()
    {
        $group_id = $this->input->getPost('id');

        // busca fechamento
        $fechamentos = $this->water_model->GetLancamentos($group_id);
        $group       = $this->shopping_model->get_group_info($group_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if(!$fechamentos) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Nenhum lançamento não encontrado"));
            return;
        }

        foreach($fechamentos as &$f) {
            $f['competencia'] = competencia_nice($f['competencia']);
        }

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Total', 'Aberto', 'Fechado' ]
		];

        $spreadsheet->getProperties()
			->setCreator('Easymeter')
			->setLastModifiedBy('Easymeter')
			->setTitle('Relatório de Lançamentos - Água')
			->setSubject($group->group_name)
			->setDescription('Relatório de Lançamentos - Água - '.$group->group_name)
			->setKeywords($group->group_name.' Lançamentos Água')
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

        $spreadsheet->getActiveSheet()->getStyle('A6:G'.(count($fechamentos) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A'.(count($fechamentos) + 7), 'Gerado em '.date("d/m/Y H:i"));

		$spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);
 
        $filename = "Lançamentos Água ".$group->group_name;

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