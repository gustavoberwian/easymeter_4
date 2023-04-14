<?php

namespace App\Controllers;

use App\Models\Industria_model;
use App\Models\Energy_model;
use App\Models\Water_model;
use App\Models\Gas_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Industria extends UNO_Controller
{
    protected $input;
    protected $email;

    /**
     * @var Datatables
     */
    protected Datatables $datatables;

    /**
     * @var Condominio_model
     */
    private Industria_model $industria_model;

    /**
     * @var Energy_model
     */
    private Energy_model $energy_model;

    /**
     * @var Water_model
     */
    private Water_model $water_model;

    /**
     * @var Water_model
     */
    private Gas_model $gas_model;

    public $url;
    public $user;

    public function __construct()
    {
        parent::__construct();

        // load services
        $this->input = \Config\Services::request();
        $this->email = \Config\Services::email();

        // load models
        $this->industria_model = new Industria_model();
        $this->energy_model = new Energy_model();
        $this->water_model = new Water_model();
        $this->gas_model = new Gas_model();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);

        // set variables
        $this->url = service('uri')->getSegment(1);

        $this->user->unidade = (object)[];
        $this->user->entity = (object)[];

        if ($this->user->inGroup('superadmin')) {
            $this->user->entity->classificacao = $this->user->page;
        } else if ($this->user->inGroup('admin', 'unity')) {
            $this->user->entity = $this->industria_model->get_entidade_by_user($this->user->id);
            $this->user->unidade = $this->industria_model->get_unidade_by_user($this->user->id);
        } else if ($this->user->inGroup('admin')) {
            $this->user->entity = $this->industria_model->get_entidade_by_user($this->user->id);
        } else if ($this->user->inGroup('unity')) {
            $this->user->unidade = $this->industria_model->get_unidade_by_user($this->user->id);
        }
    }

    public function index()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['medidores'] = $this->industria_model->get_medidores_geral($this->user->entity->id);

        $data['pocos'] = $this->industria_model->get_medidores_geral($this->user->entity->id, "nivel", true);

        for($i = 0; $i < count($data['pocos']); $i++) {
            $m = $this->industria_model->get_last_nivel($data['pocos'][$i]['id']);

            $data['pocos'][$i]['dinamico'] = number_format($m->profundidade_total - round(($m->leitura - 1162) * $m->mca / 4649), 0, ",", ".");
            $data['pocos'][$i]['minimo']   = number_format($m->profundidade_total - round(($m->minimo - 1162) * $m->mca / 4649), 0, ",", ".");
            $data['pocos'][$i]['estatico'] = number_format($m->profundidade_total - round(($m->estatico - 1162) * $m->mca / 4649), 0, ",", ".");
            $data['pocos'][$i]['tank']     = ($m->leitura - 1162) / 4649 * 100;
        }

        $consumo = $this->industria_model->get_consumo_medidores_geral($this->user->entity->id, date("Y-m-01"), date("Y-m-t"));

        $data['mes']      = number_format(round($consumo->value), 0, ",", ".")." <span style='font-size:10px;'>m³</span>";
        $data['previsao'] = number_format(round($consumo->value / ceil(abs(time() - strtotime(date("Y-m-01"))) / 86400) * date("t")), 0, ",", ".")." <span style='font-size:10px;'>m³</span>";

        echo $this->render('home', $data);
    }

    public function reports($rid = 0)
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;

        if ($rid) {

            $data["id"]     = $rid;
            $data["report"] = $this->industria_model->get_report_by_id($rid);
            $data["data"]   = $this->industria_model->get_report_data_by_id($rid);

            if ($data["report"]->tipo == 1) {
                $data["competencia"] = competencia_nice($data['report']->competencia);
            } else if ($data["report"]->tipo == 2) {
                $data["competencia"] = $data['report']->competencia;
            }

            echo $this->render('report', $data);

        } else {

            echo $this->render('reports', $data);
        }
    }

    public function alerts()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;

        echo $this->render('alerts', $data);
    }

    //////
    /// REQUESTS BELOW
    //////

    private function chart_config($type, $stacked, $series, $titles, $labels, $unit, $decimals, $extra = array(), $footer = "", $dates = array())
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
                "showForSingleSeries" => false,
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
                ),
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $titles,
                    "decimals" => $decimals,
                ),
                "unit"     => $unit,
                "decimals" => 0,
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

    private function chart_footer($data)
    {
        $html = '<div class="card-footer total text-right"><div class="row">';

        foreach ($data as $d) {

            $html .= '<div class="col-12 col-lg-3 text-center">
            <div class="row">
                <div class="col-6 text-right">
                    <p class="text-3 text-muted mb-0">'.$d["label"].':</p>
                </div>
                <div class="col-6 ">
                    <p class="text-3 text-muted mb-0"><span class="h6 text-primary">'.$d["value"].'</p>
                </div>
            </div>
        </div>';
        }

        $html .= '</div></div>';

        return $html;
    }

    public function get_reports($id = 0)
    {
        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT
				competencia,
				consumo,
				gerado,
                id
			FROM
				esm_relatorios
			WHERE entidade_id = 26
            ORDER BY gerado DESC
        ");

        $dt->edit('competencia', function ($data) use ($id) {
            if ($id == 1)
                return competencia_nice($data['competencia']);
            else if ($id == 2)
                return week_day(date_create_from_format('d/m/Y', $data['competencia'])->format("N"), 2).", ".$data['competencia'];
            else
                return $data['competencia'];
        });

        $dt->edit('gerado', function ($data) {
            return date('d/m/Y', strtotime($data['gerado']));
        });

        $dt->edit('consumo', function ($data) {
            return number_format($data['consumo'], 0, ",", ".");
        });

        $dt->add('actions', function ($data) {
            return '<a href="'.site_url("painel/reports/".$data['id']).'" class="action-view"><i class="fas fa-eye" title="Visualizar"></i></a>';
        });

        echo $dt->generate();
    }

    public function get_chart()
    {
        $mid             = $this->input->getPost('id');
        $monitoramento   = $this->input->getPost('monitoramento');
        $start           = $this->input->getPost('start');
        $end             = $this->input->getPost('end');

        $leituras = $this->industria_model->get_leituras($mid, $start, $end, $monitoramento);
        $series   = array();
        $values   = array();
        $labels   = array();
        $titles   = array();
        $dates    = array();
        $total    = 0;
        $max      = 0;
        $min      = 999999999;

        if ($leituras) {
            foreach ($leituras as $v) {
                $values[]  = $v->value;
                $labels[]  = $v->label;
                if ($monitoramento == "agua") {
                    $total    += floatval($v->value);
                    if ($max < floatval($v->value)) $max = floatval($v->value);
                    if ($min > floatval($v->value) && !is_null($v->value)) $min = floatval($v->value);

                    if ($start == $end) {
                        $titles[] = $v->label." - ".$v->next;
                    } else {
                        $titles[] = $v->label." - ".weekDayName($v->dw);
                        $dates[]  = $v->date;
                    }
                } else if ($monitoramento == "nivel") {
                    $titles[] = $v->tooltip;
                }
            }

            $series[] = array(
                "name" => ($monitoramento == "agua") ? "Consumo" : "Nível",
                "data" => $values,
                "color" => "#007AB8",
            );
        }

        $footer = "";
        if ($monitoramento == "agua") {
            $footer = $this->chart_footer(array(
                array("label" => "Total",  "value" => number_format(round($total), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",                    "color" => "#0088cc"),
                array("label" => "Médio",  "value" => number_format(round($total / count($leituras)), 0, ",", ".")." <span style='font-size:10px;'>m³</span>", "color" => "#0088cc"),
                array("label" => "Máximo", "value" => number_format(round($max), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",                      "color" => "#0088cc"),
                array("label" => "Mínimo", "value" => number_format(round($min), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",                      "color" => "#0088cc"),
            ));

            $chart = $this->chart_config("bar", false, $series, $titles, $labels, "m³", 1, array(), $footer, $dates);

        } else if ($monitoramento == "nivel") {

            $chart = $this->chart_config("area", false, $series, $titles, $labels, "m", 1, array(), $footer, $dates);

            $chart["yaxis"] = array("labels" => array("formatter" => "function"), "tickAmount" => 5, "min" => 0, "max" => 240);
        }

        echo json_encode($chart);
    }

    public function get_consumo_total_periodo()
    {
        $cid   = $this->input->getPost('id');
        $start = $this->input->getPost('start');
        $end   = $this->input->getPost('end');

        $consumo = $this->industria_model->get_consumo_medidores_geral($cid, $start, $end);
        if ($start == $end)
            $consumo->days = 24;

        if ($consumo) {
            echo json_encode(array(
                "value"   => number_format(round($consumo->value), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",
                "average" => number_format(round($consumo->value / $consumo->days), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",
                "label"   => ($start == $end) ? "hora" : "dia"
            ));
        } else {
            echo json_encode(array(
                "value"   => "-",
                "average" => "-"
            ));
        }
    }

    public function get_alerts()
    {
        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_alertas.tipo, 
                esm_alertas.titulo, 
                esm_alertas.texto AS mensagem, 
                esm_alertas.enviada, 
                IFNULL(esm_alertas_envios.lida, 'unread') as DT_RowClass, 
                IF(ISNULL(esm_alertas.finalizado), 'active', 'ended') as active, 
                esm_alertas.monitoramento,
                esm_alertas.id
            FROM esm_alertas_envios 
            LEFT JOIN esm_alertas ON esm_alertas.id = esm_alertas_envios.alerta_id 
            LEFT JOIN auth_users ON auth_users.id = esm_alertas.enviado_por 
            WHERE
                esm_alertas_envios.user_id = 600 AND 
                esm_alertas.visibility = 'normal' AND 
                esm_alertas_envios.visibility = 'normal' AND
                esm_alertas.enviada IS NOT NULL
                ORDER BY esm_alertas.enviada DESC
        ");

        $dt->edit('tipo', function ($data) {
            $f = $data['active'] == 'active' ? '' : ' text-muted';
            return '<span class="fa-stack">'.alerta_tipo2icon($data['tipo'], 'fa-stack-2x' . $f).entrada_icon($data['monitoramento'], 'fa-stack-1x') . '</span>';
        });

        // formata data envio
        $dt->edit('enviada', function ($data) {
            return time_ago($data['enviada']);
        });

        $dt->add('actions', function ($data) {
            $show = '';
            if ($data['DT_RowClass'] == 'unread') $show = ' d-none';
            return '<a href="#" class="action-delete' . $show . '" data-id="' . $data['id'] . '"><i class="fas fa-trash" title="Excluir alerta"></i></a>';
        });

        echo $dt->generate();
    }

    public function get_data()
    {
        $mid             = 2284;
        $monitoramento   = 'agua';
        $start           = '2023-02-08';
        $end             = '2023-02-14';

        $leituras = $this->industria_model->get_leituras($mid, $start, $end, $monitoramento);
        $series   = array();
        $values   = array();
        $labels   = array();
        $titles   = array();
        $dates    = array();
        $total    = 0;
        $max      = 0;
        $min      = 999999999;

        if ($leituras) {
            foreach ($leituras as $v) {
                $values[]  = $v->value;
                $labels[]  = $v->label;
                if ($monitoramento == "agua") {
                    $total    += floatval($v->value);
                    if ($max < floatval($v->value)) $max = floatval($v->value);
                    if ($min > floatval($v->value) && !is_null($v->value)) $min = floatval($v->value);

                    if ($start == $end) {
                        $titles[] = $v->label." - ".$v->next;
                    } else {
                        $titles[] = $v->label." - ".weekDayName($v->dw);
                        $dates[]  = $v->date;
                    }
                } else if ($monitoramento == "nivel") {
                    $titles[] = $v->tooltip;
                }
            }

            $series[] = array(
                "name" => ($monitoramento == "agua") ? "Consumo" : "Nível",
                "data" => $values,
                "color" => "#007AB8",
            );
        }

        echo json_encode($series);
    }
}