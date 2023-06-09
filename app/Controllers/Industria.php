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
     * @var Admin_model
     */
    private Admin_model $admin_model;


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
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        $data['medidores'] = $this->industria_model->get_medidores_geral($this->user->entity->id);

        $data['pocos'] = $this->industria_model->get_medidores_geral($this->user->entity->id, "nivel", true);

        for($i = 0; $i < count($data['pocos']); $i++) {
            $m = $this->industria_model->get_last_nivel($data['pocos'][$i]['id'], $this->user->entity->tabela);

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
                $data["competencia"] = strftime('%b/%Y', strtotime($data['report']->competencia));
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
        $data['monitoria'] = $this->user->monitoria;
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
                return strftime('%b/%Y', strtotime($data['competencia']));
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
                array("label" => "Total",  "value" => number_format(round($total), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",                                              "color" => "#0088cc"),
                array("label" => "Médio",  "value" => number_format(round($total / count($leituras)), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",                           "color" => "#0088cc"),
                array("label" => "Máximo", "value" => number_format(round($max), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",                                                "color" => "#0088cc"),
                array("label" => "Mínimo", "value" => number_format(round(($min == 999999999) ? 0 : $min), 0, ",", ".")." <span style='font-size:10px;'>m³</span>",                      "color" => "#0088cc"),
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
        $m = $this->input->getPost('monitoramento');
        $user_id = auth()->user()->id;



        $dvc = 'esm_alertas.device';
        $join = 'JOIN esm_medidores ON esm_medidores.nome = ' . $dvc;

        $dt = $this->datatables->query("
            SELECT DISTINCT
                1 AS type, 
                esm_alertas_.tipo, 
                $dvc,
                esm_alertas.titulo, 
                esm_alertas.enviada, 
                0 as actions, 
                IF(ISNULL(esm_alertas_envios.lida), 'unread', '') as DT_RowClass,
                esm_alertas_envios.id AS DT_RowId
            FROM esm_alertas_envios 
            JOIN esm_alertas ON esm_alertas.id = esm_alertas_envios.alerta_id 
            " . $join . " 
            WHERE
                esm_alertas_envios.user_id = $user_id AND 
                esm_alertas.visibility = 'normal' AND 
                esm_alertas_envios.visibility = 'normal' AND
                esm_alertas.enviada IS NOT NULL
            ORDER BY esm_alertas.enviada DESC
        ");

        $dt->edit('type', function ($data) {
            if ($this->input->getPost('monitoramento') === 'energia')
                return "<i class=\"fas fa-bolt text-warning\"></i>";
            elseif ($this->input->getPost('monitoramento') === 'agua')
                return "<i class=\"fas fa-tint text-primary\"></i>";
            elseif ($this->input->getPost('monitoramento') === 'gas')
                return "<i class=\"fas fa-fire text-success\"></i>";
            elseif ($this->input->getPost('monitoramento') === 'nivel')
                return "<i class=\"fas fa-database text-info\"></i>";
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

    public function show_alert()
    {
        // pega o id do post
        $id = $this->input->getPost('id');
        $m = $this->input->getPost('monitoramento');

        // busca o alerta
        $data['alerta'] = $this->industria_model->Get_user_alert($id, $m, true);

        $data['alerta']->enviada = time_ago($data['alerta']->enviada);

        // verifica e informa erros
        if (!$data['alerta']) {
            return view('modals/erro', array('message' => 'Alerta não encontrado!'));
        }

        // carrega a modal
        return view('modals/alert', $data);
    }

    public function profile()
    {
        $data['validation'] = \Config\Services::validation();
        $data['session'] = \Config\Services::session();
        $data['set'] = false;
        $data['url'] = $this->url;
        $data['user'] = $this->user;
        helper('form');
        $data['emails'] = $this->industria_model->get_user_emails($this->user->id);
        $user_id = $this->user->id;

        if ($this->user->inGroup('shopping', 'admin'))
        {
            $data['condo'] = $this->admin_model->get_condo($this->user->type->entity_id);
        } elseif ($this->user->inGroup('group')) {
            $data['condo'] = $this->admin_model->get_condo_by_group($this->user->type->group_id);
        } elseif ($this->user->inGroup('unity')) {
            $data['condo'] = $this->admin_model->get_condo_by_unity($this->user->type->unity_id);
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
                    if ($this->admin_model->update_avatar($this->user->id, $img)) {
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

    public function md_profile_image_edit()
    {
        return view('industria/modals/md_profile_image_edit');
    }

}