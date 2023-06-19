<?php

/**
 * @property $energy_model
 */

namespace App\Controllers;

use App\Models\Energy_model;
use App\Models\Shopping_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Energia extends UNO_Controller
{
    protected $input;
    protected Datatables $datatables;

    private Shopping_model $shopping_model;
    private Energy_model $energy_model;

    public function __construct()
    {
        parent::__construct();

        // load models
        $this->shopping_model = new Shopping_model();
        $this->energy_model = new Energy_model();

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
                    "decimals" => 3,
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

    private function chartMainActivePositive()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group    = $this->input->getPost('group');

        if ($start == $end && date("N", strtotime($start)) >= 6) {

            $period_p = false;
            $period_f = $this->energy_model->GetActivePositive($device, $group, $start, $end, array(), false, $this->user->demo);
            $period_i = false;

            $trans = $period_f;

        } else {

            $period_p = $this->energy_model->GetActivePositive($device, $group, $start, $end, array("ponta", $this->user->config->ponta_start, $this->user->config->ponta_end), false, $this->user->demo);
            $period_f = $this->energy_model->GetActivePositive($device, $group, $start, $end, array("fora", $this->user->config->ponta_start, $this->user->config->ponta_end), false, $this->user->demo);
            $period_i = false;

            $trans = $period_p;
            foreach ($period_f as $v) {
                $trans[] = $v;
            }

        }

        $month_p = $this->energy_model->GetActivePositive($device, $group, date("Y-m-01"), date("Y-m-d"), array("ponta", $this->user->config->ponta_start, $this->user->config->ponta_end), $this->user->demo)[0]->value;
        $month_f = $this->energy_model->GetActivePositive($device, $group, date("Y-m-01"), date("Y-m-d"), array("fora", $this->user->config->ponta_start, $this->user->config->ponta_end), $this->user->demo)[0]->value;

        $main  = $this->energy_model->GetDeviceLastRead($device, $group);
        $day   = $this->energy_model->GetActivePositiveAverage($device, $group);
        $day_p = $this->energy_model->GetActivePositiveAverage($device, $group, array("ponta", $this->user->config->ponta_start, $this->user->config->ponta_end));
        $day_f = $this->energy_model->GetActivePositiveAverage($device, $group, array("fora", $this->user->config->ponta_start, $this->user->config->ponta_end));

        $values_p  = array();
        $values_f  = array();
        $values_i  = array();
        $labels    = array();
        $titles    = array();
        $dates     = array();
        $total_p   = 0;
        $total_f   = 0;
        $total_i   = 0;

        $max_p = -1;
        $max_f = -1;
        $min_p = 999999999;
        $min_f = 999999999;

        $maior = 0;

        foreach ($trans as $t) {
            if ($t->value > $maior) {
                $maior = $t->value;
            }
        }

        if ($maior > 999 && $maior <= 999999) {
            $unidade_medida = "kWh";
            $divisor = 1000;
            $decimals = 1;
        } elseif ($maior > 999999) {
            $unidade_medida = "MWh";
            $divisor = 1000000;
            $decimals = 3;
        } else {
            $unidade_medida = "Wh";
            $divisor = 1;
            $decimals = 0;
        }

        if ($period_f) {

            foreach ($period_f as $v) {

                $values_f[] = $v->value / $divisor;
                $labels[]   = $v->label;

                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
                $total_f += is_null($v->value) ? 0 : floatval($v->value);
                if ($max_f < floatval($v->value) && !is_null($v->value)) $max_f = floatval($v->value);
                if ($min_f > floatval($v->value) && !is_null($v->value)) $min_f = floatval($v->value);
            }
        }

        if ($period_p) {
            foreach ($period_p as $v) {
                $values_p[] = $v->value / $divisor;
                $total_p += is_null($v->value) ? 0 : floatval($v->value);
                if ($max_p < floatval($v->value) && !is_null($v->value)) $max_p = floatval($v->value);
                if ($min_p > floatval($v->value) && !is_null($v->value)) $min_p = floatval($v->value);
            }
        }

        if ($period_i) {
            foreach ($period_i as $v) {
                $values_i[] = $v->value / $divisor;
                $total_i += is_null($v->value) ? 0 : floatval($v->value);
            }
        }

        $series = array();

        if ($period_f) {
            $series[] = array(
                "name" => "Fora Ponta",
                "data" => $values_f,
                "color" => "#007AB8",
            );
        }
        if ($period_i) {
            $series[] = array(
                "name" => "Intermediário",
                "data" => $values_i,
                "color" => "#EDC241",
            );
        }
        if ($period_p) {
            $series[] = array(
                "name" => "Ponta",
                "data" => $values_p,
                "color" => "#ff4560",
            );
        }

        $dias   = ((strtotime(date("Y-m-d")) - strtotime(date("Y-m-01"))) / 86400) + 1;
        $dias_t = date('d', mktime(0, 0, 0, date("m") + 1, 0, date("Y")));

        $extra = array(
            "main"        => str_pad(round($main), 6 , '0' , STR_PAD_LEFT). " <span style='font-size:12px;'>Wh</span>",
            "period"      => number_format(round(($total_p + $total_f) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "period_p"    => number_format(round($total_p / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "period_f"    => number_format(round($total_f / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "month"       => number_format(round(($month_p + $month_f) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "month_p"     => number_format(round($month_p / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "month_f"     => number_format(round($month_f / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "prevision"   => number_format(round((($month_p + $month_f) / $dias * $dias_t) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "prevision_p" => number_format(round(($month_p / $dias * $dias_t) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "prevision_f" => number_format(round(($month_f / $dias * $dias_t) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "day"         => number_format(round(($day_p + $day_f) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "day_p"       => number_format(round($day_p / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
            "day_f"       => number_format(round($day_f / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>",
        );

        $data = array(
            array("Máximo Fora", ($max_f == -1) ? "-" : number_format(round($max_f / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#268ec3"),
            array("Mínimo Fora", ($min_f == 999999999) ? "-" : number_format(round($min_f / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#268ec3"),
            array("Médio Fora",  ($min_f == 999999999) ? "-" : number_format(round(($total_f / count($period_f)) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#268ec3"),
        );

        if ($start != $end || date("N", strtotime($start)) < 6) {
            $data[] = array("Máximo Ponta", ($max_p == -1) ? "-" : number_format(round($max_p / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#ff6178");
            $data[] = array("Mínimo Ponta", ($min_p == 999999999) ? "-" : number_format(round($min_p / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#ff6178");
            $data[] = array("Médio Ponta",  ($min_p == 999999999) ? "-" : number_format(round(($total_p / count($period_p)) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#ff6178");
        }

        $footer = $this->chartFooter($data);

        $config = $this->chartConfig("bar", true, $series, $titles, $labels, $unidade_medida, 0, $extra, $footer, $dates);

        return json_encode($config);
    }

    private function chartMainStation()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group      = $this->input->getPost('group');

        if ($start == $end && date("N", strtotime($start)) >= 6) {

            $period_p = false;
            $period_f = $this->energy_model->GetActivePositive($device, $group, $start, $end, array(), false, $this->user->demo);

        } else {
            $period_p = $this->energy_model->GetActivePositive($device, $group, $start, $end, array("ponta", $this->user->config->ponta_start, $this->user->config->ponta_end), false, $this->user->demo);
            $period_f = $this->energy_model->GetActivePositive($device, $group, $start, $end, array("fora", $this->user->config->ponta_start, $this->user->config->ponta_end), false, $this->user->demo);
        }

        $consumption_p = 0;
        if ($period_p) {
            foreach ($period_p as $v) {
                $consumption_p += $v->value;
            }
        }

        $consumption_f = 0;
        if ($period_f) {
            foreach ($period_f as $v) {
                $consumption_f += $v->value;
            }
        }

        $options = array(
            "chart" => array(
                "type" => "donut",
                "width" => "100%",
                "height" => 380,
                "toolbar" => array("show" => false),
            ),
            "series" => [$consumption_f, $consumption_p],
            "labels" => ["Fora Ponta", "Ponta"],
            "colors" => ["#007AB8", "#ff4560"],
            "stroke" => array("width" => 4),
            "legend" => array("position" => "bottom"),
            "tooltip" => array(
                "enabled" => false,
                "x" => array(
                    "formatter" => "function",
                    "show" => false
                ),
                "y" => array(
                    "formatter" => "function",
                    "show" => false
                )
            ),
            "yaxis" => array(
                "labels" => array("formatter" => "function")
            ),
            "dataLabels" => array(
                "dropShadow" => array("enabled" => false)
            )
        );

        return json_encode($options);
    }

    private function chartMainActiveDemand()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group      = $this->input->getPost('group');

        if ($start == $end) {

            $period = $this->energy_model->GetActiveDemand($device, $group, $start, $end, $this->user->demo);
            $count  = 1;

        } else {

            $period = $this->energy_model->GetActiveDemand($device, $group, $start, $end, $this->user->demo);
            $count  = 24;
        }

        $values_max = array();
        $values_avg = array();
        $labels     = array();
        $titles     = array();
        $dates      = array();

        $maior = 0;

        if ($period) {
            foreach ($period as $t) {
                if ($t->valueSum / $count > $maior) {
                    $maior = $t->valueSum / $count;
                }
            }
        }

        if ($maior > 999 && $maior <= 999999) {
            $unidade_medida = "kWh";
            $divisor = 1000;
            $decimals = 1;
        } elseif ($maior > 999999) {
            $unidade_medida = "MWh";
            $divisor = 1000000;
            $decimals = 3;
        } else {
            $unidade_medida = "Wh";
            $divisor = 1;
            $decimals = 0;
        }

        if ($period) {
            foreach ($period as $v) {
                $values_max[] = $v->valueMax / $divisor;
                $values_avg[] = $v->valueSum === null ? null : ($v->valueSum / $count) / $divisor;
                $labels[]   = $v->label;
                if ($start == $end)
                    $titles[] = $v->label." - ".$v->next;
                else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }
        }

        $series = array(
            array(
                "name" => "Demanda Máxima",
                "data" => $values_max,
                "color" => "#007AB8",
            ),
            array(
                "name" => "Demanda Média",
                "data" => $values_avg,
                "color" => "#87c1de",
            )
        );

        return json_encode($this->chartConfig("bar", false, $series, $titles, $labels, $unidade_medida, 0, array(), "", $dates));
    }

    private function chartMainFactor()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group      = $this->input->getPost('group');

        $limite = "Limite: " . 0.92;
        $value  = array();
        $labels = array();
        $titles = array();
        $dates  = array();
        $max    = 0;
        $values = $this->energy_model->GetMainFactor($device, $group, $start, $end, $this->user->demo);

        if ($values) {

            foreach ($values as $v) {

                if (is_null($v->value)) {
                    $value[] = null;
                } else {
                    $vl = ($v->type == "I") ? 1 - $v->value : -1 + $v->value;
                    $value[] = $vl;
                    if (abs($vl) > $max) $max = abs($vl);
                }

                $labels[]  = $v->label;
                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }

        } else {

            $value[]  = 0;
            $labels[] = "";
            $dates[]  = "";
        }

        if ($max < 0.2) $max = 0.2;

        $series = array(
            array(
                "data" => $value,
                "name" => "Fator de Potência",
                "color" => "#007AB8",
            ),
        );

        $options = $this->chartConfig("line", true, $series, $titles, $labels, "", 1, array(), "", $dates);

        $options["yaxis"] = array("labels" => array("formatter" => "function"), "tickAmount" => 4,"min" => $max * -1,"max" => $max);

        /*        $options["tooltip"] = array(
                    "enabled" => false,
                    "x" => array("formatter" => "function","show" => true),
                );
        */
        $options["annotations"] = array(
            "yaxis" => [
                array("y" => 0.08, "borderColor" => "red", "label" => array("text" => $limite)),
                array("y" => -0.08, "borderColor" => "red", "label" => array("text" => $limite))
            ]
        );

        return json_encode($options);
    }

    private function chartFactorPhases()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group      = $this->input->getPost('group');

        $limite  = "Limite: " . 0.92;
        $value_a = array();
        $value_b = array();
        $value_c = array();
        $labels  = array();
        $titles  = array();
        $dates   = array();
        $max     = 0;
        $values  = $this->energy_model->GetFactorPhases($device, $group, $start, $end, $this->user->demo);

        if ($values) {

            foreach ($values as $v) {

                if (is_null($v->value_a)) {
                    $value_a[] = null;
                } else {
                    $vl = ($v->type_a == "I") ? 1 - $v->value_a : (1 -$v->value_a) * -1;
                    $value_a[] = $vl;
                    if (abs($vl) > $max) $max = abs($vl);
                }

                if (is_null($v->value_b)) {
                    $value_b[] = null;
                } else {
                    $vl = ($v->type_b == "I") ? 1 - $v->value_b : (1 -$v->value_b) * -1;
                    $value_b[] = $vl;
                    if (abs($vl) > $max) $max = abs($vl);
                }

                if (is_null($v->value_c)) {
                    $value_c[] = null;
                } else {
                    $vl = ($v->type_c == "I") ? 1 - $v->value_c : (1 -$v->value_c) * -1;
                    $value_c[] = $vl;
                    if (abs($vl) > $max) $max = abs($vl);
                }

                $labels[]  = $v->label;
                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }

        } else {

            $value_a[] = 0;
            $value_b[] = 0;
            $value_c[] = 0;
            $labels[]  = "";
            $dates[]   = "";
        }

        if ($max < 0.2) $max = 0.2;

        $series = array(
            array(
                "data" => $value_a,
                "name" => "Fase R",
                "color" => "#FF4560",
            ),
            array(
                "data" => $value_b,
                "name" => "Fase S",
                "color" => "#00E396",
            ),
            array(
                "data" => $value_c,
                "name" => "Fase T",
                "color" => "#007AB8",
            ),
        );

        $options = $this->chartConfig("line", false, $series, $titles, $labels, "", 1, array(), "", $dates);

        $options["yaxis"] = array("labels" => array("formatter" => "function"), "tickAmount" => 4,"min" => round($max, 3, PHP_ROUND_HALF_UP) * -1,"max" => round($max, 3, PHP_ROUND_HALF_UP));

        $options["annotations"] = array(
            "yaxis" => [
                array("y" => 0.08, "borderColor" => "red", "label" => array("text" => $limite)),
                array("y" => -0.08, "borderColor" => "red", "label" => array("text" => $limite))
            ]
        );

        return json_encode($options);
    }

    private function chartConsumption()
    {
        $device  = $this->input->getPost('device');
        $group  = $this->input->getPost('group');

        $value = array();
        $labels  = array();
        $titles  = array();

        $values  = $this->energy_model->GetConsumptionDay($device, $group, $this->user->demo);

        $maior = 0;

        if ($values) {
            foreach ($values as $t) {
                if ($t->value > $maior) {
                    $maior = $t->value;
                }
            }
        }

        if ($maior > 999 && $maior <= 999999) {
            $unidade_medida = "kWh";
            $divisor = 1000;
            $decimals = 1;
        } elseif ($maior > 999999) {
            $unidade_medida = "MWh";
            $divisor = 1000000;
            $decimals = 3;
        } else {
            $unidade_medida = "Wh";
            $divisor = 1;
            $decimals = 0;
        }

        if ($values) {

            foreach ($values as $v) {

                $value[]  = $v->value / $divisor;
                $labels[] = $v->label;
                $titles[] = $v->title;
            }

        } else {

            $value[]  = 0;
            $labels[] = "";
            $titles[] = "";
        }

        $series = array(
            array(
                "data" => $value,
                "name" => "Consumo",
            ),
        );

        $options = $this->chartConfig("line", true, $series, $titles, $labels, $unidade_medida, 1);

        $options["chart"]["height"] = 200;

        return json_encode($options);
    }

    private function chartMainReactive()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group      = $this->input->getPost('group');

        $period = $this->energy_model->GetMainReactive($device, $group, $start, $end, $this->user->demo);

        $values_c = array();
        $values_i = array();
        $labels   = array();
        $titles   = array();
        $dates    = array();

        $total_c = 0;
        $total_i = 0;
        $max_c = 0;
        $max_i = 0;
        $min_c = 999999999;
        $min_i = 999999999;

        $maior = 0;

        if ($period) {
            foreach ($period as $t) {
                if (max($t->valueCap, $t->valueInd) > $maior) {
                    $maior = max($t->valueCap, $t->valueInd);
                }
            }
        }

        if ($maior > 999 && $maior <= 999999) {
            $unidade_medida = "kVArh";
            $divisor = 1000;
            $decimals = 1;
        } elseif ($maior > 999999) {
            $unidade_medida = "MVArh";
            $divisor = 1000000;
            $decimals = 3;
        } else {
            $unidade_medida = "VArh";
            $divisor = 1;
            $decimals = 0;
        }

        if ($period) {
            foreach ($period as $v) {
                $values_c[] = $v->valueCap / $divisor;
                $values_i[] = $v->valueInd / $divisor;

                $total_c += floatval($v->valueCap);
                $total_i += floatval($v->valueInd);
                if ($max_c < floatval($v->valueCap)) $max_c = floatval($v->valueCap);
                if ($max_i < floatval($v->valueInd)) $max_i = floatval($v->valueInd);
                if ($min_c > floatval($v->valueCap) && !is_null($v->valueCap)) $min_c = floatval($v->valueCap);
                if ($min_i > floatval($v->valueInd) && !is_null($v->valueInd)) $min_i = floatval($v->valueInd);

                $labels[]   = $v->label;
                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }
        }

        $series = array(
            array(
                "name" => "Reativa Capacitiva",
                "data" => $values_c,
                "color" => "#007AB8",
            ),
            array(
                "name" => "Reativa Indutiva",
                "data" => $values_i,
                "color" => "#87c1de",
            )
        );

        $data = array(
            array("Máxima Capacitiva", number_format(round($max_c / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#999"),
            array("Mínima Capacitiva", number_format(round(($min_c == 999999999) ? 0 : $min_c / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#999"),
            array("Média Capacitiva", number_format(round(($total_c / count($period)) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#999"),
            array("Máxima Indutiva", number_format(round($max_i / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#999"),
            array("Mínima Indutiva", number_format(round(($min_i == 999999999) ? 0 : $min_i / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#999"),
            array("Média Indutiva", number_format(round(($total_i / count($period)) / $divisor, $decimals), $decimals, ",", ".") . " <span style='font-size:12px;'>$unidade_medida</span>", "#999"),
        );

        $footer = $this->chartFooter($data);

        return json_encode($this->chartConfig("bar", true, $series, $titles, $labels, $unidade_medida, 0, array(), $footer, $dates));
    }

    private function chartMainLoad()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group      = $this->input->getPost('group');

        $value_load = array();
        $labels     = array();
        $titles     = array();
        $dates      = array();

        $values = $this->energy_model->getMainLoad($device, $group, $start, $end, $this->user->demo);

        if ($values) {

            foreach ($values as $v) {

                $value_load[] = $v->value;

                $labels[]  = $v->label;

                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }

        } else {

            $value_load[] = 0;
            $labels[]     = "";
            $title[]      = "";
            $dates[]      = "";
        }

        $series[] = array(
            "name" => "Fator de Carga",
            "data" => $value_load,
            "color" => "#007AB8",
        );


        $config = $this->chartConfig("bar", true, $series, $titles, $labels, "", 1, array(), "", $dates);

        $config["yaxis"] = array(
            "labels" => array(
                "formatter" => "function"
            ),
            "min" => 0,
            "max" => 1
        );

        return json_encode($config);
    }

    private function chartMainCarbon()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group      = $this->input->getPost('group');

        $value  = array();
        $labels = array();
        $titles = array();
        $dates  = array();

        //Carbon factor
        $factor = 0.090;

        $values = $this->energy_model->GetActivePositive($device, $group, $start, $end, array(), false, $this->user->demo);

        $maior = 0;

        if ($values) {
            foreach ($values as $t) {
                if ($t->value * $factor > $maior) {
                    $maior = $t->value * $factor;
                }
            }
        }

        if ($maior > 999 && $maior <= 999999) {
            $unidade_medida = "kg";
            $divisor = 1000;
            $decimals = 1;
        } elseif ($maior > 999999) {
            $unidade_medida = "ton";
            $divisor = 1000000;
            $decimals = 3;
        } else {
            $unidade_medida = "g";
            $divisor = 1;
            $decimals = 0;
        }

        if ($values) {

            foreach ($values as $v) {
                $value[] = is_null($v->value) ? null : round(($v->value * $factor) / $divisor, $decimals);
                $labels[]   = $v->label;
                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }

        } else {

            $value[]  = 0;
            $labels[] = "";
            $titles[] = "";
            $dates[]  = "";
        }

        $series = array(
            array(
                "name" => "Emissão de CO²",
                "data" => $value,
                "color" => "#01B28C",
            ),
        );

        return json_encode($this->chartConfig("area", true, $series, $titles, $labels, $unidade_medida, 1, array(), "", $dates));
    }

    public function chart_engineering()
    {
        $field    = $this->input->getPost('field');

        $this->user->config = $this->shopping_model->get_client_config($this->input->getPost('group'));
        if (!$this->user->config) {
            return json_encode(array(
                "status" => "error",
                "message" => "Dados não foram carregados corretamente. Configurações gerais do shopping não fornecidas."
            ));
        }

        $divisor  = 1;
        $decimals = 0;
        $unidade  = "";
        $type     = "line";

        if ($field == "mainActivePositive") {
            echo $this->chartMainActivePositive();
            return;
        } else if ($field == "mainStation") {
            echo $this->chartMainStation();
            return;
        } else if ($field == "mainActiveDemand") {
            echo $this->chartMainActiveDemand();
            return;
        } else if ($field == "mainFactor") {
            echo $this->chartMainFactor();
            return;
        } else if ($field == "mainReactive") {
            echo $this->chartMainReactive();
            return;
        } else if ($field == "mainLoad") {
            echo $this->chartMainLoad();
            return;
        } else if ($field == "mainCarbon") {
            echo $this->chartMainCarbon();
            return;
        } else if ($field == "active") {
            $divisor  = 1000;
            $unidade  = "kW";
            $decimals = 0;
        } else if ($field == "current") {
            $unidade  = "A";
            $decimals = 1;
        } else if ($field == "voltage") {
            $unidade = "V";
            $decimals = 1;
        } else if ($field == "power") {
            $unidade  = "Kw";
            $divisor  = 1000;
            $decimals = 1;
            $type     = "bar";
        } else if ($field == "load") {
            $unidade  = "";
            $divisor  = 1;
            $decimals = 1;
            $type     = "bar";
        } else if ($field == "reactive") {
            $unidade  = "kVAr";
            $divisor  = 1000;
            $decimals = 0;
        } else if ($field == "factor") {
            echo $this->chartFactorPhases();
            return;
        } else if ($field == "consumption") {
            echo $this->chartConsumption();
            return;
        }

        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $group    = $this->input->getPost('group');
        $field    = $this->input->getPost('field');

        $value_a = array();
        $value_b = array();
        $value_c = array();
        $labels  = array();
        $titles  = array();
        $dates   = array();

        $total_a   = 0;
        $total_b   = 0;
        $total_c   = 0;
        $max_a = 0;
        $max_b = 0;
        $max_c = 0;
        $min_a = 999999999;
        $min_b = 999999999;
        $min_c = 999999999;

        if ($field == "load")
            $values = $this->energy_model->GetLoadPhases($device, $group, $start, $end, $field, $this->user->demo);
        else
            $values = $this->energy_model->GetValuesPhases($device, $group, $start, $end, $field, $this->user->demo);

        if ($values) {

            foreach ($values as $v) {

                $_a = is_null($v->value_a) ? null : $v->value_a / $divisor;
                $_b = is_null($v->value_b) ? null : $v->value_b / $divisor;
                $_c = is_null($v->value_c) ? null : $v->value_c / $divisor;

                $value_a[] = $_a;
                $value_b[] = $_b;
                $value_c[] = $_c;

                $total_a += is_null($_a) ? 0 : $_a;
                $total_b += is_null($_b) ? 0 : $_b;
                $total_c += is_null($_c) ? 0 : $_c;

                if ($max_a < floatval($_a)) $max_a = floatval($_a);
                if ($max_b < floatval($_b)) $max_b = floatval($_b);
                if ($max_c < floatval($_c)) $max_c = floatval($_c);

                if ($min_a > floatval($_a) && !is_null($_a)) $min_a = floatval($_a);
                if ($min_b > floatval($_b) && !is_null($_b)) $min_b = floatval($_b);
                if ($min_c > floatval($_c) && !is_null($_c)) $min_c = floatval($_c);

                $labels[]  = $v->label;


                if ($start == $end) {
                    $titles[] = $v->label." - ".$v->next;
                } else {
                    $titles[] = $v->label." - ".weekDayName($v->dw);
                    $dates[]  = $v->date;
                }
            }

        } else {

            $value_a[] = 0;
            $value_b[] = 0;
            $value_c[] = 0;
            $labels[]  = "";
            $dates[]   = "";
        }

        $series = array(
            array(
                "name" => "Fase R",
                "data" => $value_a,
                "color" => "#FF4560",
            ),
            array(
                "name" => "Fase S",
                "data" => $value_b,
                "color" => "#00E396",
            ),
            array(
                "name" => "Fase T",
                "data" => $value_c,
                "color" => "#007AB8",
            ),
        );

        $footer = "";
        if ($field == "current" || $field == "voltage") {
            $data = array(
                array("Máx / Min R:", number_format(round($max_a, $decimals), $decimals, ",", ".") . $unidade. " / " . number_format(round(($min_a = 999999999) ? 0 : $min_a, $decimals), $decimals, ",", ".") . $unidade, '#ff4560'),
                array("Méd R:", number_format(round($total_a / count($values), $decimals), $decimals, ",", ".") . $unidade, '#ff4560'),
                array("Máx / Min S:", number_format(round($max_b, $decimals), $decimals, ",", ".")  . $unidade. " / " . number_format(round(($min_b = 999999999) ? 0 : $min_b, $decimals), $decimals, ",", ".") . $unidade, '#00e396'),
                array("Méd S:", number_format(round($total_b / count($values), $decimals), $decimals, ",", ".") . $unidade, '#00e396'),
                array("Máx / Min T:", number_format(round($max_c, $decimals), $decimals, ",", ".")  . $unidade . " / " . number_format(round(($min_c = 999999999) ? 0 : $min_c, $decimals), $decimals, ",", ".") . $unidade, '#007ab8'),
                array("Méd T:", number_format(round($total_c / count($values),$decimals), $decimals, ",", ".") . $unidade, '#007ab8'),
            );

            $footer = $this->chartFooter($data);

        } else if ($field == "active" || $field == "power" || $field == "reactive") {
            $data = array(
                array("Máx R:", number_format(round($max_a, $decimals), $decimals, ",", ".") . $unidade, "#ff4560"),
                array("Mín R:", number_format(round(($min_a = 999999999) ? 0 : $min_a, $decimals), $decimals, ",", ".") . $unidade, "#ff4560"),
                array("Méd R:", number_format(round($total_a / count($values), $decimals), $decimals, ",", ".") . $unidade, "#ff4560"),
                array("Máx S:", number_format(round($max_b, $decimals), $decimals, ",", ".") . $unidade, "#00e396"),
                array("Mín S:", number_format(round(($min_b = 999999999) ? 0 : $min_b, $decimals), $decimals, ",", ".") . $unidade, "#00e396"),
                array("Méd S:", number_format(round($total_b / count($values), $decimals), $decimals, ",", ".") . $unidade, "#00e396"),
                array("Máx T:", number_format(round($max_c, $decimals), $decimals, ",", ".") . $unidade, "#007ab8"),
                array("Mín T:", number_format(round(($min_c = 999999999) ? 0 : $min_c, $decimals), $decimals, ",", ".") . $unidade, "#007ab8"),
                array("Méd T:", number_format(round($total_c / count($values), $decimals), $decimals, ",", ".") . $unidade, "#007ab8"),
            );
            $footer = $this->chartFooter($data);
        }

        $config = $this->chartConfig($type, false, $series, $titles, $labels, $unidade, $decimals, array(), $footer, $dates);

        $config["tooltip"] = array(
            "enabled"   => true,
//            "intersect" => false,
//            "shared"    => true,
            "x" => array(
                "formatter" => "function",
                "show"      => true
            ),
            "y" => array(
                "formatter" => "function",
                "show"      => true
            )
        );

        if ($field == "load") {

            $config["yaxis"] = array(
                "labels" => array(
                    "formatter" => "function"
                ),
                "min" => 0,
                "max" => 1
            );
        }

        echo json_encode($config);
    }

    public function chart_active()
    {
        $medidor_id = $this->input->getPost('mid');
        $start      = $this->input->getPost('start');
        $end        = $this->input->getPost('end');
        $period     = $this->input->getPost('period');

        $semana  = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $current = strtotime($start);
        $days    = array();
        while ($current <= strtotime($end . ' 23:59:59')) {
            $days[]  = $semana[date('w', $current)];
            $current = strtotime('+1 day', $current);
        }

        $leituras = $this->energy_model->get_consumo_ativafase($medidor_id, 'fora', $period, strtotime($start . ' 00:00:00'), strtotime($end . ' 23:59:59'));

        // transforma resultado em um array flat
        $consumo_a = array();
        $consumo_b = array();
        $consumo_c = array();
        $labels    = array();
        $max       = 0;
        if ($leituras) {
            foreach ($leituras as $l) {
                // Preenche array com leituras
                $consumo_a[] = $l->fase_a;
                $consumo_b[] = $l->fase_b;
                $consumo_c[] = $l->fase_c;
                $labels[]     = $l->label;

                if ($max < $l->fase_a) $max = $l->fase_a;
                if ($max < $l->fase_b) $max = $l->fase_b;
                if ($max < $l->fase_c) $max = $l->fase_c;
            }
        } else {
            $consumo_a[] = 0;
            $consumo_b[] = 0;
            $consumo_c[] = 0;
            $labels[]     = "";
        }

        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "line",
                "width" => "100%",
                "height" => 380,
                "foreColor" => "#777",
                "stacked" => false,
                "events" => array(
                    "click" => "function"
                )
            ),
            "series" => [
                array(
                    "name" => "Fase R",
                    "data" => $consumo_a,
                    "color" => "#FF0000",
                ),
                array(
                    "name" => "Fase S",
                    "data" => $consumo_b,
                    "color" => "#000",
                ),
                array(
                    "name" => "Fase T",
                    "data" => $consumo_c,
                    "color" => "#8B4513",
                ),
            ],
            "stroke" => array(
                "curve" => "smooth",
                "width" => 2
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $days,
                ),
                "unit" => array("limit" => $max, "sup" => "kWh", "sup_dec" => 1, "inf" => "Wh", "inf_dec" => 2)
            ),
            "plotOptions" => array(
                "bar" => array(
                    "dataLabels" => array(
                        "position" => "top"
                    )
                )
            ),
            "dataLabels" => array(
                "enabled" => false,
                "style" => array(
                    "colors" => ["#777"]
                ),
                "offsetY" => -20,
                "distributed" => true,
            ),
            "xaxis" => array(
                "categories" => $labels,
                "axisBorder" => array(
                    "show" => false
                ),
                "axisTicks" => array(
                    "show" => false
                ),
                "tickAmount" => $period == "day" ? 24 : count($labels)
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                )
            ),
            "tooltip" => array(
                "enabled" => true,
                "intersect" => false,
                "shared" => true,
                "y" => array(
                    "formatter" => "function",
                    "title" => array(
                        "formatter" => "function"
                    ),
                ),
                "x" => array(
                    "formatter" => "function",
                    "show" => true
                )
            ),
        );

        echo json_encode($options);
    }

    public function chart_current()
    {
        $medidor_id = $this->input->getPost('mid');
        $start      = $this->input->getPost('start');
        $end        = $this->input->getPost('end');
        $period     = $this->input->getPost('period');

        $semana  = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $current = strtotime($start);
        $days    = array();
        while ($current <= strtotime($end . ' 23:59:59')) {
            $days[]  = $semana[date('w', $current)];
            $current = strtotime('+1 day', $current);
        }

        $leituras = $this->energy_model->get_correntefase($medidor_id, 'fora', $period, strtotime($start . ' 00:00:00'), strtotime($end . ' 23:59:59'));

        $values_a = array();
        $values_b = array();
        $values_c = array();
        $labels = array();

        if ($leituras) {
            foreach ($leituras as $l) {
                // insere resultado no array
                $values_a[] = $l->current_a;
                $values_b[] = $l->current_b;
                $values_c[] = $l->current_c;
                $labels[]   = $l->label;
            }
        }

        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "line",
                "width" => "100%",
                "height" => 380,
                "stacked" => false,
                "foreColor" => "#777",
                "events" => array(
                    "click" => "function"
                )
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $days,
                ),
                "unit" => array("limit" => 0, "sup" => "A", "sup_dec" => 1, "inf" => "A", "inf_dec" => 1)
            ),
            "series" => [
                array(
                    "name" => "Fase R",
                    "data" => $values_a,
                    "color" => "#FF0000",
                ),
                array(
                    "name" => "Fase S",
                    "data" => $values_b,
                    "color" => "#000",
                ),
                array(
                    "name" => "Fase T",
                    "data" => $values_c,
                    "color" => "#8B4513",
                ),
            ],
            "stroke" => array(
                "curve" => "smooth",
                "width" => 2
            ),
            "plotOptions" => array(
                "bar" => array(
                    "dataLabels" => array(
                        "position" => "top"
                    )
                )
            ),
            "dataLabels" => array(
                "enabled" => false,
                "style" => array(
                    "colors" => ["#777"]
                ),
                "offsetY" => -20,
                "formatter" => "function"
            ),
            "xaxis" => array(
                "categories" => $labels,
                "axisBorder" => array(
                    "show" => false
                ),
                "axisTicks" => array(
                    "show" => false
                ),
                "tickAmount" => $period == "day" ? 24 : count($labels)
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                ),
                "tickAmount" => 6
            ),
            "annotations" => array(
                "yaxis" => [
                    array(
                        "y" => 9000,
                        "borderColor" => "#EDC241",
                        "label" => array(
                            "show" => false
                        )
                    ),
                    array(
                        "y" => 9500,
                        "borderColor" => "red",
                        "label" => array(
                            "show" => false
                        )
                    )
                ]
            ),
            "tooltip" => array(
                "enabled" => true,
                "intersect" => false,
                "shared" => true,
                "y" => array(
                    "formatter" => "empty",
                    "title" => array(
                        "formatter" => "empty"
                    ),
                ),
                "x" => array(
                    "formatter" => "empty",
                    "format" => "dd MMM",
                    "show" => true
                )
            ),
        );

        echo json_encode($options);
    }

    public function chart_voltage()
    {
        $medidor_id = $this->input->getPost('mid');
        $start      = $this->input->getPost('start');
        $end        = $this->input->getPost('end');
        $period     = $this->input->getPost('period');

        $semana  = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $current = strtotime($start);
        $days    = array();
        while ($current <= strtotime($end . ' 23:59:59')) {
            $days[]  = $semana[date('w', $current)];
            $current = strtotime('+1 day', $current);
        }

        $leituras = $this->energy_model->get_voltage($medidor_id, 'fora', $period, strtotime($start . ' 00:00:00'), strtotime($end . ' 23:59:59'));

        $values_a = array();
        $values_b = array();
        $values_c = array();
        $labels = array();

        if ($leituras) {
            foreach ($leituras as $l) {
                // insere resultado no array
                $values_a[] = $l->voltage_a;
                $values_b[] = $l->voltage_b;
                $values_c[] = $l->voltage_c;
                $labels[]   = $l->label;
            }
        }

        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "line",
                "width" => "100%",
                "height" => 380,
                "stacked" => false,
                "foreColor" => "#777",
                "events" => array(
                    "click" => "function"
                )
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $days,
                ),
                "unit" => array("limit" => 0, "sup" => "V", "sup_dec" => 1, "inf" => "V", "inf_dec" => 2)
            ),
            "series" => [
                array(
                    "name" => "Fase R",
                    "data" => $values_a,
                    "color" => "#FF0000",
                ),
                array(
                    "name" => "Fase S",
                    "data" => $values_b,
                    "color" => "#000",
                ),
                array(
                    "name" => "Fase T",
                    "data" => $values_c,
                    "color" => "#8B4513",
                ),
            ],
            "stroke" => array(
                "curve" => "smooth",
                "width" => 2
            ),
            "plotOptions" => array(
                "bar" => array(
                    "dataLabels" => array(
                        "position" => "top"
                    )
                )
            ),
            "dataLabels" => array(
                "enabled" => false,
                "style" => array(
                    "colors" => ["#777"]
                ),
                "offsetY" => -20,
                "formatter" => "function"
            ),
            "xaxis" => array(
                "categories" => $labels,
                "axisBorder" => array(
                    "show" => false
                ),
                "axisTicks" => array(
                    "show" => false
                ),
                "tickAmount" => $period == "day" ? 24 : count($labels)
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                ),
                "tickAmount" => 6
            ),
            "annotations" => array(
                "yaxis" => [
                    array(
                        "y" => 9000,
                        "borderColor" => "#EDC241",
                        "label" => array(
                            "show" => false
                        )
                    ),
                    array(
                        "y" => 9500,
                        "borderColor" => "red",
                        "label" => array(
                            "show" => false
                        )
                    )
                ]
            ),
            "tooltip" => array(
                "enabled" => true,
                "intersect" => false,
                "shared" => true,
                "y" => array(
                    "formatter" => "empty",
                    "title" => array(
                        "formatter" => "empty"
                    ),
                ),
                "x" => array(
                    "formatter" => "empty",
                    "format" => "dd MMM",
                    "show" => true
                )
            ),
        );

        echo json_encode($options);
    }
/*
    private function chart_active_demand()
    {
        $device   = $this->input->getPost('device', true);
        $start    = $this->input->getPost('start', true);
        $end      = $this->input->getPost('end', true);

        $values_a   = array();
        $values_b   = array();
        $values_c   = array();
        $labels     = array();
        $days       = array();

        $values = $this->energy_model->get_active_demand($device, $start, $end);

        if ($values) {
            foreach ($values as $v) {
                // insere resultado no array
                $values_a[] = $v->value_a;
                $values_b[] = $v->value_b;
                $values_c[] = $v->value_c;
                $labels[]   = $v->label;

                if ($start != $end)
                    $days[]     = weekDayName($v->dw);
            }

        } else {

            $values_a[] = 0;
            $values_b[] = 0;
            $values_c[] = 0;
            $labels[]  = "";
        }


        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "line",
                "width" => "100%",
                "height" => 380,
                "stacked" => false,
                "foreColor" => "#777",
                "events" => array(
                    "click" => "function"
                )
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $days,
                ),
                "unit" => array("limit" => 0, "sup" => "kWh", "sup_dec" => 3, "inf" => "kWh", "inf_dec" => 3)
            ),
            "series" => [
                array(
                    "name" => "Fase R",
                    "data" => $values_a,
                    "color" => "#FF0000",
                ),
                array(
                    "name" => "Fase S",
                    "data" => $values_b,
                    "color" => "#000",
                ),
                array(
                    "name" => "Fase T",
                    "data" => $values_c,
                    "color" => "#8B4513",
                ),
            ],
            "stroke" => array(
                "curve" => "smooth",
                "width" => 2
            ),
            "plotOptions" => array(
                "bar" => array(
                    "dataLabels" => array(
                        "position" => "top"
                    )
                )
            ),
            "dataLabels" => array(
                "enabled" => false,
                "style" => array(
                    "colors" => ["#777"]
                ),
                "offsetY" => -20,
                "formatter" => "function"
            ),
            "xaxis" => array(
                "categories" => $labels,
                "axisBorder" => array(
                    "show" => false
                ),
                "axisTicks" => array(
                    "show" => false
                ),
                "tickAmount" => $start == $end ? 24 : count($labels)
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                ),
                "tickAmount" => 6
            ),
            "annotations" => array(
                "yaxis" => [
                    array(
                        "y" => 9000,
                        "borderColor" => "#EDC241",
                        "label" => array(
                            "show" => false
                        )
                    ),
                    array(
                        "y" => 9500,
                        "borderColor" => "red",
                        "label" => array(
                            "show" => false
                        )
                    )
                ]
            ),
            "tooltip" => array(
                "enabled" => true,
                "intersect" => false,
                "shared" => true,
                "y" => array(
                    "formatter" => "empty",
                    "title" => array(
                        "formatter" => "empty"
                    ),
                ),
                "x" => array(
                    "formatter" => "empty",
                    "format" => "dd MMM",
                    "show" => true
                )
            ),
        );

        return json_encode($options);
    }
*/
    private function chart_carbon()
    {
        $device   = $this->input->getPost('device');
        $start    = $this->input->getPost('start');
        $end      = $this->input->getPost('end');
        $interval = $this->input->getPost('interval');

        $semana  = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $current = strtotime($start);
        $value_a = array();
        $value_b = array();
        $value_c = array();
        $labels  = array();
        $days    = array();

        //Carbon factor
        $factor = 0.090;

        if ($interval != "day") {
            while ($current <= strtotime($end . ' 23:59:59')) {
                $days[]  = $semana[date('w', $current)];
                $current = strtotime('+1 day', $current);
            }
        }

        $values = $this->energy_model->get_values_phases($device, "active", "SUM((", $interval, strtotime($start . ' 00:00:00'), strtotime($end . ' 23:59:59'));

        if ($values) {

            foreach ($values as $l) {
                $value_a[] = $l->value_a * $factor;
                $value_b[] = $l->value_b * $factor;
                $value_c[] = $l->value_c * $factor;
                $labels[]  = $l->label;
            }

        } else {

            $value_a[] = 0;
            $value_b[] = 0;
            $value_c[] = 0;
            $labels[]  = "";
        }

        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "line",
                "width" => "100%",
                "height" => 380,
                "foreColor" => "#777",
                "stacked" => false,
                "events" => array(
                    "click" => "function"
                )
            ),
            "series" => [
                array(
                    "name" => "Fase R",
                    "data" => $value_a,
                    "color" => "#FF0000",
                ),
                array(
                    "name" => "Fase S",
                    "data" => $value_b,
                    "color" => "#000",
                ),
                array(
                    "name" => "Fase T",
                    "data" => $value_c,
                    "color" => "#8B4513",
                ),
            ],
            "stroke" => array(
                "curve" => "smooth",
                "width" => 2
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $days,
                ),
                "unit" => array("limit" => 0, "sup" => "Kg", "sup_dec" => 0, "inf" => "Kg", "inf_dec" => 0)
            ),
            "plotOptions" => array(
                "bar" => array(
                    "dataLabels" => array(
                        "position" => "top"
                    )
                )
            ),
            "dataLabels" => array(
                "enabled" => false,
                "style" => array(
                    "colors" => ["#777"]
                ),
                "offsetY" => -20,
                "distributed" => true,
            ),
            "xaxis" => array(
                "categories" => $labels,
                "axisBorder" => array(
                    "show" => false
                ),
                "axisTicks" => array(
                    "show" => false
                ),
                "tickAmount" => $interval == "day" ? 24 : count($labels)
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                )
            ),
            "tooltip" => array(
                "enabled" => true,
                "intersect" => false,
                "shared" => true,
                "y" => array(
                    "formatter" => "function",
                    "title" => array(
                        "formatter" => "function"
                    ),
                ),
                "x" => array(
                    "formatter" => "function",
                    "show" => true
                )
            ),
        );

        return json_encode($options);
    }
/*
    private function chart_load_profile()
    {
        $device   = $this->input->getPost('device', true);
        $start    = $this->input->getPost('start', true);
        $end      = $this->input->getPost('end', true);
        $interval = $this->input->getPost('interval', true);

        $semana  = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $current = strtotime($start);
        $value_a = array();
        $value_b = array();
        $value_c = array();
        $labels  = array();
        $days    = array();

        if ($interval != "day") {
            while ($current <= strtotime($end . ' 23:59:59')) {
                $days[]  = $semana[date('w', $current)];
                $current = strtotime('+1 day', $current);
            }
        }

        $values = $this->energy_model->get_values_load($device, $interval, strtotime($start . ' 00:00:00'), strtotime($end . ' 23:59:59'));

        if ($values) {

            foreach ($values as $l) {

                if ($l->value_max_a > 0)
                    $value_a[] = $l->value_avg_a / $l->value_max_a;
                else
                    $value_a[] = 0;

                if ($l->value_max_b > 0)
                    $value_b[] = $l->value_avg_b / $l->value_max_b;
                else
                    $value_b[] = 0;

                if ($l->value_max_c > 0)
                    $value_c[] = $l->value_avg_c / $l->value_max_c;
                else
                    $value_c[] = 0;

                $labels[]  = $l->label;
            }

        } else {

            $value_a[] = 0;
            $value_b[] = 0;
            $value_c[] = 0;
            $labels[]  = "";
        }

        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "line",
                "width" => "100%",
                "height" => 380,
                "foreColor" => "#777",
                "stacked" => false,
                "events" => array(
                    "click" => "function"
                )
            ),
            "series" => [
                array(
                    "name" => "Fase R",
                    "data" => $value_a,
                    "color" => "#FF0000",
                ),
                array(
                    "name" => "Fase S",
                    "data" => $value_b,
                    "color" => "#000",
                ),
                array(
                    "name" => "Fase T",
                    "data" => $value_c,
                    "color" => "#8B4513",
                ),
            ],
            "stroke" => array(
                "curve" => "smooth",
                "width" => 2
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $days,
                ),
                "unit" => array("limit" => 0, "sup" => "", "sup_dec" => 1, "inf" => "", "inf_dec" => 1)
            ),
            "plotOptions" => array(
                "bar" => array(
                    "dataLabels" => array(
                        "position" => "top"
                    )
                )
            ),
            "dataLabels" => array(
                "enabled" => false,
                "style" => array(
                    "colors" => ["#777"]
                ),
                "offsetY" => -20,
                "distributed" => true,
            ),
            "xaxis" => array(
                "categories" => $labels,
                "axisBorder" => array(
                    "show" => false
                ),
                "axisTicks" => array(
                    "show" => false
                ),
                "tickAmount" => $interval == "day" ? 24 : count($labels)
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                )
            ),
            "tooltip" => array(
                "enabled" => true,
                "intersect" => false,
                "shared" => true,
                "y" => array(
                    "formatter" => "function",
                    "title" => array(
                        "formatter" => "function"
                    ),
                ),
                "x" => array(
                    "formatter" => "function",
                    "show" => true
                )
            ),
        );

        return json_encode($options);
    }

    private function chart_factor()
    {
        $device   = $this->input->getPost('device', true);
        $start    = $this->input->getPost('start', true);
        $end      = $this->input->getPost('end', true);
        $interval = 'last';

        $limite = "Limite: " . 0.92;

        $semana  = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $current = strtotime($start);
        $value_a = array();
        $value_b = array();
        $value_c = array();
        $labels  = array();
        $days    = array();

        if ($interval != "day") {
            while ($current <= strtotime($end . ' 23:59:59')) {
                $days[]  = $semana[date('w', $current)];
                $current = strtotime('+1 day', $current);
            }
        }

        $values = $this->energy_model->get_values_factor($device, $interval, strtotime($start . ' 00:00:00'), strtotime($end . ' 23:59:59'));

        if ($values) {

            foreach ($values as $l) {

                if (is_null($l->factor_a))
                    $value_a[] = 0;
                else
                    $value_a[] = ($l->reactive_a >= 0) ? 1 - $l->factor_a : -1 + $l->factor_a;

                if (is_null($l->factor_b))
                    $value_b[] = 0;
                else
                    $value_b[] = ($l->reactive_b >= 0) ? 1 - $l->factor_b : -1 + $l->factor_b;

                if (is_null($l->factor_c))
                    $value_c[] = 0;
                else {
                    $value_c[] = ($l->reactive_c >= 0) ? 1 - $l->factor_c : -1 + $l->factor_c;
                }

                $labels[]  = $l->label;
            }

        } else {

            $value_a[] = 0;
            $value_b[] = 0;
            $value_c[] = 0;
            $labels[]  = "";
        }

        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "line",
                "width" => "100%",
                "height" => 380,
                "foreColor" => "#777",
                "stacked" => false,
                "events" => array(
                    "click" => "function"
                )
            ),
            "series" => [
                array(
                    "name" => "Fase R",
                    "data" => $value_a,
                    "color" => "#FF0000",
                ),
                array(
                    "name" => "Fase S",
                    "data" => $value_b,
                    "color" => "#000",
                ),
                array(
                    "name" => "Fase T",
                    "data" => $value_c,
                    "color" => "#8B4513",
                ),
            ],
            "stroke" => array(
                "curve" => "smooth",
                "width" => 2
            ),
            "extra" => array(
                "tooltip" => array(
                    "title" => $days,
                ),
                "unit" => array("limit" => 0, "sup" => "", "sup_dec" => 1, "inf" => "", "inf_dec" => 1)
            ),
            "plotOptions" => array(
                "bar" => array(
                    "dataLabels" => array(
                        "position" => "top"
                    )
                )
            ),
            "dataLabels" => array(
                "enabled" => false,
                "style" => array(
                    "colors" => ["#777"]
                ),
                "offsetY" => -20,
                "distributed" => true,
            ),
            "xaxis" => array(
                "categories" => $labels,
                "axisBorder" => array(
                    "show" => false
                ),
                "axisTicks" => array(
                    "show" => false
                ),
                "tickAmount" => $interval == "day" ? 24 : count($labels)
            ),
            "yaxis" => array(
                "labels" => array(
                    "formatter" => "function"
                ),
                "tickAmount" => 4,
                "min" => -1,
                "max" => 1,
            ),
            "tooltip" => array(
                "enabled" => true,
                "intersect" => false,
                "shared" => true,
                "y" => array(
                    "formatter" => "function",
                    "title" => array(
                        "formatter" => "function"
                    ),
                ),
                "x" => array(
                    "formatter" => "function",
                    "show" => true
                )
            ),
            "annotations" => array(
                "yaxis" => [
                    array(
                        "y" => 0.08,
                        "borderColor" => "red",
                        "label" => array(
                            "text" => $limite,
                        )
                    ),
                    array(
                        "y" => -0.08,
                        "borderColor" => "red",
                        "label" => array(
                            "text" => $limite,
                        )
                    )
                ]
            )
        );

        return json_encode($options);
    }
*/
    public function chart_consumption_station()
    {
        $options = array(
            "chart" => array(
                "toolbar" => array(
                    "show" => false
                ),
                "type" => "donut",
                "width" => "100%",
                "height" => 380
            ),
            //"series" => [$fora_ponta, $ponta],
            "series" => [79.7, 20.3],
            "labels" => ["Fora Ponta", "Ponta"],
            "colors" => ["#007AB8", "#EDC241"],
            "stroke" => array(
                "width" => 4
            ),
            "legend" => array(
                "position" => "bottom"
            ),
            "tooltip" => array(
                "y" => array(
                    "formatter" => "function",
                    "title" => array(
                        "formatter" => "function"
                    )
                ),
                "x" => array(
                    "formatter" => "function",
                    "show" => false
                )

            )
        );

        return json_encode($options);
    }

    public function data()
    {
        $entity = $this->shopping_model->get_entity_by_group($this->input->getPost('group'));

        $device = $this->input->getPost('device');
        $init   = $this->input->getPost('init');
        $finish = $this->input->getPost('finish');
        $type   = $this->input->getPost('type');
        $min    = $this->input->getPost('min');
        $max    = $this->input->getPost('max');

        // print_r(sprintf("%.2f", $min));

        if (!is_null($min))
            $min    = floatval(str_replace(array('.', ','), array('', '.'), sprintf("%.2f", $this->input->getPost('min'))));
          
        if (!is_null($max))
            $max    = floatval(str_replace(array('.', ','), array('', '.'), sprintf("%.2f", $this->input->getPost('max'))));
        

    

        $abnormal = "";
        $field    = "activePositive,";
        if (!is_null($type)) {
            $field = "";
            if ($type == "activePositiveConsumption")
                $abnormal = "AND (activePositiveConsumption < $min OR activePositiveConsumption > $max)";
            else
                $abnormal = "AND ({$type}A < $min OR {$type}A > $max OR {$type}B < $min OR {$type}B > $max OR {$type}C < $min OR {$type}C > $max)";
        }

        $values = "voltageA, 
				voltageB, 
				voltageC, 
				currentA, 
				currentB, 
				currentC, 
                activeA, 
                activeB, 
                activeC, 
                reactiveA,
                reactiveB,
                reactiveC,
                activePositiveConsumption";

        if ($this->user->demo) {
            $values = "RAND() * 50 AS voltageA, 
				RAND() * 50 AS voltageB, 
				RAND() * 50 AS voltageC, 
				RAND() * 50 AS currentA, 
				RAND() * 50 AS currentB, 
				RAND() * 50 AS currentC, 
                RAND() * 50 AS activeA, 
                RAND() * 50 AS activeB, 
                RAND() * 50 AS activeC, 
                RAND() * 50 AS reactiveA,
                RAND() * 50 AS reactiveB,
                RAND() * 50 AS reactiveC,
                RAND() * 50 AS activePositiveConsumption";
        }

        $tabela = "esm_leituras_".$entity->tabela."_energia";
        $where_demo = " AND device = '$device' $abnormal";

        if ($this->user->demo) {
            $tabela = "esm_leituras_" . $entity->tabela . "_energia_demo";
            $where_demo = " AND device = '03D2559E'";
        }

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT
                timestamp AS date,
                $field
                $values
            FROM
                $tabela
            WHERE
                timestamp >= UNIX_TIMESTAMP('$init 00:00:00') AND 
                timestamp <= UNIX_TIMESTAMP('$finish 23:59:59')
                $where_demo
        ");

        $dt->edit('date', function ($data) {

            return date("d/m/Y H:i", $data['date']);
        });

        if ($field === 'activePositive') {
            $dt->edit('activePositive', function ($data) {
                return str_pad(round($data["activePositive"]), 6, '0', STR_PAD_LEFT);
            });
        }

        $dt->edit('voltageA', function ($data) use ($type, $max, $min) {
            $val = number_format($data["voltageA"], 3, ",", ".");
            if ($type == "voltage") {
                if ($data["voltageA"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["voltageA"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }

            return $val;
        });

        $dt->edit('voltageB', function ($data) use ($type, $max, $min) {
            $val = number_format($data["voltageB"], 3, ",", ".");
            if ($type == "voltage") {
                if ($data["voltageB"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["voltageB"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }

            return $val;
        });

        $dt->edit('voltageC', function ($data) use ($type, $max, $min) {
            $val = number_format($data["voltageC"], 3, ",", ".");
            if ($type == "voltage") {
                if ($data["voltageC"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["voltageC"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }

            return $val;
        });

        $dt->edit('currentA', function ($data) use ($type, $max, $min) {
            $val = number_format($data["currentA"], 3, ",", ".");
            if ($type == "current") {
                if ($data["currentA"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["currentA"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }

            return $val;
        });

        $dt->edit('currentB', function ($data) use ($type, $max, $min) {
            $val = number_format($data["currentB"], 3, ",", ".");
            if ($type == "current") {
                if ($data["currentB"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["currentB"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }

            return $val;
        });

        $dt->edit('currentC', function ($data) use ($type, $max, $min) {
            $val = number_format($data["currentC"], 3, ",", ".");
            if ($type == "current") {
                if ($data["currentC"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["currentC"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }

            return $val;
        });

        $dt->edit('activeA', function ($data) use ($type, $max, $min) {
            $val = number_format($data["activeA"], 3, ",", ".");
            if ($type == "active") {
                if ($data["activeA"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["activeA"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }
            return $val;
        });

        $dt->edit('activeB', function ($data) use ($type, $max, $min) {
            $val = number_format($data["activeB"], 3, ",", ".");
            if ($type == "active") {
                if ($data["activeB"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["activeB"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }
            return $val;
        });

        $dt->edit('activeC', function ($data) use ($type, $max, $min) {
            $val = number_format($data["activeC"], 3, ",", ".");
            if ($type == "active") {
                if ($data["activeC"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["activeC"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }
            return $val;
        });

        $dt->edit('reactiveA', function ($data) use ($type, $max, $min) {
            $val = number_format($data["reactiveA"], 3, ",", ".");
            if ($type == "reactive") {
                if ($data["reactiveA"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["reactiveA"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }
            return $val;
        });

        $dt->edit('reactiveB', function ($data) use ($type, $max, $min) {
            $val = number_format($data["reactiveB"], 3, ",", ".");
            if ($type == "reactive") {
                if ($data["reactiveB"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["reactiveB"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }
            return $val;
        });

        $dt->edit('reactiveC', function ($data) use ($type, $max, $min) {
            $val = number_format($data["reactiveC"], 3, ",", ".");
            if ($type == "reactive") {
                if ($data["reactiveC"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["reactiveC"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }
            return $val;
        });

        $dt->edit('activePositiveConsumption', function ($data) use ($type, $max, $min) {
            $val = number_format($data["activePositiveConsumption"], 3, ",", ".");
            if ($type == "activePositiveConsumption") {
                if ($data["activePositiveConsumption"] > $max)
                    return "<span class='text-danger'>$val</span>";
                else if ($data["activePositiveConsumption"] < $min)
                    return "<span class='text-warning'>$val</span>";
            }
            return $val;
        });

        // gera resultados
        echo $dt->generate();
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

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                esm_medidores.ultima_leitura AS value_read,
                m.value AS value_month,
                p.value AS value_ponta,
                m.value - p.value AS value_fora,
                l.value AS value_last,
                m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())) AS value_future,
                c.value AS value_last_month
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            LEFT JOIN (  
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_".$entity->tabela."_energia
                    WHERE 
                        timestamp > UNIX_TIMESTAMP() - 86400
                    GROUP BY device
                ) l ON l.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        d.device,
                        SUM(activePositiveConsumption) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_".$entity->tabela."_energia d ON 
                        (d.timestamp) > (esm_calendar.ts_start) AND 
                        (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    WHERE 
                        esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                        esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.device
                ) m ON m.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_".$entity->tabela."_energia
                    WHERE 
                        MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                    GROUP BY device                
                ) c ON c.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_".$entity->tabela."_energia
                    WHERE 
                        MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now())
                        AND (MOD((timestamp), 86400) >= {$this->user->config->open} AND MOD((timestamp), 86400) <= {$this->user->config->close})
                    GROUP BY device
                ) h ON h.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        d.device,
                        SUM(activePositiveConsumption) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_".$entity->tabela."_energia d ON 
                        (d.timestamp) > (esm_calendar.ts_start) AND 
                        (d.timestamp) <= (esm_calendar.ts_end + 600) 
                        AND ((MOD((d.timestamp), 86400) >= {$this->user->config->ponta_start} AND MOD((d.timestamp), 86400) <= {$this->user->config->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)
                    WHERE 
                        esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                        esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.device
                ) p ON p.device = esm_medidores.nome
            WHERE 
                esm_unidades.agrupamento_id = " . $this->input->getPost("group") . " AND
                esm_medidores.tipo = 'energia'
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
            if ($data["value_last"] > 999 && $data["value_last"] <= 999999) {
                $unidade_medida = "kWh";
                $divisor = 1000;
                $decimals = 1;
            } elseif ($data["value_last"] > 999999) {
                $unidade_medida = "MWh";
                $divisor = 1000000;
                $decimals = 3;
            } else {
                $unidade_medida = "Wh";
                $divisor = 1;
                $decimals = 0;
            }

            return number_format(round($data["value_last"] / $divisor, $decimals), $decimals, ",", ".") . " <small>$unidade_medida</small>";
        });

        $dt->edit('value_month', function ($data) {
            if ($data["value_month"] > 999 && $data["value_month"] <= 999999) {
                $unidade_medida = "kWh";
                $divisor = 1000;
                $decimals = 1;
            } elseif ($data["value_month"] > 999999) {
                $unidade_medida = "MWh";
                $divisor = 1000000;
                $decimals = 3;
            } else {
                $unidade_medida = "Wh";
                $divisor = 1;
                $decimals = 0;
            }

            return number_format(round($data["value_month"] / $divisor, $decimals), $decimals, ",", ".") . " <small>$unidade_medida</small>";
        });

        $dt->edit('value_fora', function ($data) {
            if ($data["value_fora"] > 999 && $data["value_fora"] <= 999999) {
                $unidade_medida = "kWh";
                $divisor = 1000;
                $decimals = 1;
            } elseif ($data["value_fora"] > 999999) {
                $unidade_medida = "MWh";
                $divisor = 1000000;
                $decimals = 3;
            } else {
                $unidade_medida = "Wh";
                $divisor = 1;
                $decimals = 0;
            }

            return number_format(round($data["value_fora"] / $divisor, $decimals), $decimals, ",", ".") . " <small>$unidade_medida</small>";
        });

        $dt->edit('value_ponta', function ($data) {
            if ($data["value_ponta"] > 999 && $data["value_ponta"] <= 999999) {
                $unidade_medida = "kWh";
                $divisor = 1000;
                $decimals = 1;
            } elseif ($data["value_ponta"] > 999999) {
                $unidade_medida = "MWh";
                $divisor = 1000000;
                $decimals = 3;
            } else {
                $unidade_medida = "Wh";
                $divisor = 1;
                $decimals = 0;
            }

            return number_format(round($data["value_ponta"] / $divisor, $decimals), $decimals, ",", ".") . " <small>$unidade_medida</small>";
        });

        $dt->edit('value_last_month', function ($data) {
            if ($data["value_last_month"] > 999 && $data["value_last_month"] <= 999999) {
                $unidade_medida = "kWh";
                $divisor = 1000;
                $decimals = 1;
            } elseif ($data["value_last_month"] > 999999) {
                $unidade_medida = "MWh";
                $divisor = 1000000;
                $decimals = 3;
            } else {
                $unidade_medida = "Wh";
                $divisor = 1;
                $decimals = 0;
            }

            return number_format(round($data["value_last_month"] / $divisor, $decimals), $decimals, ",", ".") . " <small>$unidade_medida</small>";
        });

        $dt->edit('value_future', function ($data) {
            $icon = "";
            if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-up-alt text-danger ms-2\"></i>";
            else if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-down-alt text-success ms-2\"></i>";

            if ($data["value_future"] > 999 && $data["value_future"] <= 999999) {
                $unidade_medida = "kWh";
                $divisor = 1000;
                $decimals = 1;
            } elseif ($data["value_future"] > 999999) {
                $unidade_medida = "MWh";
                $divisor = 1000000;
                $decimals = 3;
            } else {
                $unidade_medida = "Wh";
                $divisor = 1;
                $decimals = 0;
            }

            return number_format(round($data["value_future"] / $divisor, $decimals), $decimals, ",", ".") . " <small>$unidade_medida</small> $icon";
        });

        // gera resultados
        echo $dt->generate();
    }

    public function alert_test($aid)
    {
        $cfg = $this->energy_model->GetAlertCfg($aid);

        if (!$cfg) {
            echo "Nenhum alerta configurado";
            return;
        }

        if ($aid == 1) {
            // consumo do dia for maior que a média dos últimos 30 dias
            // todo dia as 1:00

            $values = array();
            foreach ($cfg as $d) {
                $alert = $this->energy_model->GetAlert($aid, $d->device);
                if ($alert) {
                    if ($alert->today > $alert->last) {
                        $values[] = array(
                                "tipo"    => '1', //info
                                "titulo"  => 'Consumo hoje maior que a média diária dos últimos 30 dias',
                                "texto"   => 'O consumo hoje ('.round($alert->today).' kWh) foi maior que a média diária dos últimos 30 dias ('.round($alert->last).' kWh).',
                                "enviada" => date("Y-m-d H:i:s"),
                                "device"  => $d->device
                        );
                    }
                }
            }

            if (count($values)) {
                $this->energy_model->AddAlerts($values, $cfg[0]);
            }

        } else if($aid == 2) {

            $values = array();
            foreach ($cfg as $d) {
                $alert = $this->energy_model->GetAlert($aid, $d->device);
                if ($alert["current"] > $alert["previous"]) {
                    $values[] = array(
                            "tipo"    => '1', //info
                            "titulo"  => 'O consumo previsto para o mês é maior que o consumo do mês anterior',
                            "texto"   => "O consumo previsto para o mês ({$alert["current"]}) é maior que o consumo do mês anterior ({$alert["previous"]}).",
                            "enviada" => date("Y-m-d H:i:s"),
                            "device"  => $d->device
                    );
                }
            }

            if (count($values)) {
                $this->energy_model->AddAlerts($values, $cfg[0], $alert);
            }

        } else if($aid == 4) {  //sobre corrente

            $alerts = $this->energy_model->GetAlert($aid);

            $values = array();
            foreach ($alerts as $a) {
                $value = max(array($a->currentA, $a->currentB, $a->currentC));
                if ($value > $a->disjuntor) {
                    $values[] = array(
                            "tipo"    => "3", //danger
                            "titulo"  => "Sobreconsumo de corrente",
                            "texto"   => "A unidade {$a->nome} consumiu em uma das fases $value A. Este valor é superior ao limite nominal da rede de {$a->disjuntor} A.",
                            "enviada" => date("Y-m-d H:i:s"),
                            "device"  => $a->device
                    );
                }
            }

            if (count($values)) {
                $this->energy_model->AddAlerts($values, $cfg[0]);
            }
        }
    }

    private function GetFactorInsight($group)
    {
        $entity = $this->shopping_model->get_entity_by_group($group);

        $value = "p.value AS value";
        $tabela = "esm_leituras_".$entity->tabela."_energia";
        if ($this->user->demo) {
            $value = "RAND() * 1 AS value";
            $tabela = "esm_leituras_".$entity->tabela."_energia_demo";
            $group = 113;
        }

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.nome AS name, 
                p.type AS type,
                $value
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN (
                SELECT 
                    d.device,
                    IF(SUM(reactivePositiveConsumption) > SUM(ABS(reactiveNegativeConsumption)), 'I', 'C') AS type,
                    IFNULL(SUM(activePositiveConsumption) / SQRT(POW(SUM(activePositiveConsumption), 2) + POW(SUM(reactivePositiveConsumption) + SUM(ABS(reactiveNegativeConsumption)), 2)), 1) AS value
                FROM esm_calendar
                LEFT JOIN $tabela d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                WHERE 
                    esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                    esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                GROUP BY d.device 
            ) p ON p.device = esm_medidores.nome
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

        $dt->edit('type', function ($data) {
            if ($data["type"] == "I")
                return "<span class=\"badge badge-warning\"> Indutivo </span>";
            else
                return "<span class=\"badge badge-primary\"> Capacitivo </span>";
        });

        return $dt->generate();
    }

    public function insights($iud)
    {
        $group = $this->input->getPost("group");

        $entity = $this->shopping_model->get_entity_by_group($group);

        $this->user->config = $this->shopping_model->get_client_config($group);

        if (!$this->user->config) {
            return json_encode(array(
                "status" => "error",
                "message" => "Dados não foram carregados corretamente. Configurações gerais do shopping não fornecidas."
            ));
        }

        $station = "";
        $st = "";
        $total = false;
        $factor = 1;
        if ($iud == 1) {
            $station = "AND ((MOD((d.timestamp), 86400) >= {$this->user->config->ponta_start} AND MOD((d.timestamp), 86400) <= {$this->user->config->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)";
            $st = "ponta";
            $total = $this->energy_model->GetMonthByStation(array($st, $this->user->config->ponta_start, $this->user->config->ponta_end), $group, $this->user->demo);
        } else if ($iud == 2) {
            $station = "AND (((MOD((d.timestamp), 86400) < {$this->user->config->ponta_start} OR MOD((d.timestamp), 86400) > {$this->user->config->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7) OR esm_calendar.dw = 1 OR esm_calendar.dw = 7)";
            $st = "fora";
            $total = $this->energy_model->GetMonthByStation(array($st, $this->user->config->ponta_start, $this->user->config->ponta_end), $group, $this->user->demo);
        } else if ($iud == 3) {
            $station = "AND (MOD((d.timestamp), 86400) >= {$this->user->config->open} AND MOD((d.timestamp), 86400) <= {$this->user->config->close})";
            $st = "open";
            $total = $this->energy_model->GetMonthByStation(array($st, $this->user->config->open, $this->user->config->close), $group, $this->user->demo);
        } else if ($iud == 4) {
            $station = "AND (MOD((d.timestamp), 86400) < {$this->user->config->open} OR MOD((d.timestamp), 86400) > {$this->user->config->close})";
            $st = "close";
            $total = $this->energy_model->GetMonthByStation(array($st, $this->user->config->open, $this->user->config->close), $group, $this->user->demo);
        } else if ($iud == 5) {
            $station = "";
            $factor = 0.090;
            $total = $this->energy_model->GetMonthByStation(array(), $group, $this->user->demo) * $factor;
        } else if ($iud == 6) {

            echo $this->GetFactorInsight($group);
            return;
        }

        $value = "p.value AS value";
        $tabela = "esm_leituras_".$entity->tabela."_energia";
        if ($this->user->demo) {
            $value = "RAND() * 1000 AS value";
            $tabela = "esm_leituras_".$entity->tabela."_energia_demo";
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
                        d.device,
                        SUM(activePositiveConsumption) AS value
                    FROM esm_calendar
                    LEFT JOIN $tabela d ON 
                        (d.timestamp) > (esm_calendar.ts_start) AND 
                        (d.timestamp) <= (esm_calendar.ts_end + 600) 
                        $station
                    WHERE 
                        esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                        esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.device
                ) p ON p.device = esm_medidores.nome
            WHERE 
                esm_unidades.agrupamento_id = $group
            ORDER BY value DESC
            LIMIT 10
        ");

        $dt->add('id', function ($data) {
            return "-";
        });

        $dt->edit('value', function ($data) use ($factor) {
            if ($data["value"] > 999999 && $factor == 1) {
                return number_format(($data["value"] / 1000000) * $factor, 3, ",", "."). "MWh";
            } elseif ($data["value"] > 999 && $factor == 1) {
                return number_format(($data["value"] / 1000) * $factor, 3, ",", "."). "KWh";
            } elseif ( $factor == 1) {
                return number_format($data["value"] * $factor, 3, ",", ".")."Wh";
            } else {
                return number_format($data["value"] * $factor, 3, ",", ".")."Kg";
            }
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

    /**
     * A função retorna informações no datatable de fechamento
     *
     */
    public function GetFaturamentos()
    {
        $group = $this->input->getPost('gid');
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        $dt = $this->datatables->query("
            SELECT
                esm_fechamentos_energia.id,
                competencia AS competencia,
                DATE_FORMAT(inicio, '%d/%m/%Y') AS inicio,
                DATE_FORMAT(fim, '%d/%m/%Y') AS fim,
                FORMAT(consumo, 3, 'de_DE') AS consumo,
                FORMAT(consumo_p, 3, 'de_DE') AS consumo_p,
                FORMAT(consumo_f, 3, 'de_DE') AS consumo_f,
                FORMAT(demanda_p, 3, 'de_DE') AS demanda,

                FORMAT(consumo_u, 3, 'de_DE') AS consumo_u,
                FORMAT(consumo_u_p, 3, 'de_DE') AS consumo_u_p,
                FORMAT(consumo_u_f, 3, 'de_DE') AS consumo_u_f,
                FORMAT(demanda_u_p, 3, 'de_DE') AS demanda_u,

                DATE_FORMAT(cadastro, '%d/%m/%Y') AS emissao
            FROM
                esm_fechamentos_energia
            JOIN 
                esm_agrupamentos ON esm_agrupamentos.id = esm_fechamentos_energia.agrupamento_id AND esm_agrupamentos.id = $group
            ORDER BY cadastro DESC

        ");

        $dt->edit('competencia', function ($data) {
            return strftime('%b/%Y', strtotime($data['competencia']));
        });

        // inclui actions
		$dt->add('action', function ($data) {
			return '<a href="#" class="action-download text-primary me-2" data-id="' . $data['id'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
				<a href="#" class="action-delete text-danger" data-id="' . $data['id'] . '"><i class="fas fa-trash" title="Excluir"></i></a>';
		});

        echo $dt->generate();
    }

    public function get_faturamento_unidade($entity_id)
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        $dt = $this->datatables->query("
        SELECT 
            esm_fechamentos_energia.competencia AS competencia,
            esm_unidades.nome,
            esm_fechamentos_energia.id AS fid,
            FORMAT(esm_fechamentos_energia_entradas.consumo, 3, 'de_DE') AS consumo,
            FORMAT(esm_fechamentos_energia_entradas.consumo_p, 3, 'de_DE') AS consumo_p,
            FORMAT(esm_fechamentos_energia_entradas.consumo_f, 3, 'de_DE') AS consumo_f,
            FORMAT(esm_fechamentos_energia_entradas.demanda_p, 3, 'de_DE') AS demanda,
            DATE_FORMAT(cadastro, '%d/%m/%Y') AS emissao,
            esm_fechamentos_energia_entradas.id AS entrada_id,
            esm_unidades.id AS uid
        FROM 
            esm_fechamentos_energia_entradas
        JOIN
            esm_medidores ON esm_medidores.nome = esm_fechamentos_energia_entradas.device
        JOIN 
            esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
        JOIN
            esm_fechamentos_energia ON esm_fechamentos_energia.id = esm_fechamentos_energia_entradas.fechamento_id
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

    public function GetFechamentoUnidades($type)
    {
        $fid = $this->input->getPost('fid');

        $dt = $this->datatables->query("
            SELECT 
                esm_unidades.nome,
                esm_unidades_config.luc as luc,
                leitura_anterior,
                leitura_atual,
                consumo,
                consumo_p,
                consumo_f,
                demanda,
                demanda_p,
                demanda_f,
                esm_fechamentos_energia_entradas.id AS DT_RowId
            FROM 
                esm_fechamentos_energia_entradas
            JOIN 
                esm_medidores ON esm_medidores.nome = esm_fechamentos_energia_entradas.device
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN
                esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id    
            WHERE 
                esm_fechamentos_energia_entradas.fechamento_id = $fid AND
                esm_fechamentos_energia_entradas.type = $type
        ");

        $dt->edit('leitura_anterior', function ($data) {
            return str_pad(round($data["leitura_anterior"]), 6 , '0' , STR_PAD_LEFT);
        });

        $dt->edit('leitura_atual', function ($data) {
            return str_pad(round($data["leitura_atual"]), 6 , '0' , STR_PAD_LEFT);
        });

        $dt->edit('consumo', function ($data) {
            return number_format($data['consumo'], 3, ",", ".");
        });

        $dt->edit('consumo_p', function ($data) {
            return number_format($data['consumo_p'], 3, ",", ".");
        });

        $dt->edit('consumo_f', function ($data) {
            return number_format($data['consumo_f'], 3, ",", ".");
        });

        $dt->edit('demanda', function ($data) {
            return number_format($data['demanda'], 3, ",", ".");
        });

        $dt->edit('demanda_p', function ($data) {
            return number_format($data['demanda_p'], 3, ",", ".");
        });

        $dt->edit('demanda_f', function ($data) {
            return number_format($data['demanda_f'], 3, ",", ".");
        });

        echo $dt->generate();
    }

	// **
	// Exclui Faturamento
	// [post] id
	// [out] Json com status
	// **
	public function DeleteLancamento()
	{
		$id = $this->input->getPost('id');

		echo $this->energy_model->DeleteLancamento($id);
	}

    public function faturamento()
    {
        $data = array(
            "agrupamento_id"    => $this->input->getPost('tar-group'),
            "entrada_id"  => 72,
            "competencia" => $this->input->getPost('tar-competencia'),
            "inicio"      => $this->input->getPost('tar-data-ini'),
            "fim"         => $this->input->getPost('tar-data-fim'),
            "mensagem"    => $this->input->getPost('tar-msg'),
        );

		if ($this->energy_model->VerifyCompetencia($data["entrada_id"], $data["competencia"])) {
			echo '{ "status": "message", "field": "tar-competencia", "message" : "Competência já possui lançamento"}';
			return;
		}

        if (date_create_from_format('d/m/Y', $data["inicio"]) == date_create_from_format('d/m/Y', $data["fim"])) {
			echo '{ "status": "message", "field": "tar-data-fim", "message" : "Data final igual a inicial"}';
			return;
		}

		if (date_create_from_format('d/m/Y', $data["inicio"]) > date_create_from_format('d/m/Y', $data["fim"])) {
			echo '{ "status": "message", "field": "tar-data-fim", "message" : "Data final menor que a inicial"}';
			return;
		}

        echo $this->energy_model->Calculate($data, $this->user->config, $data['agrupamento_id']);
    }

    public function download()
    {
        $fechamento_id = $this->input->getPost('id');

        $group = $this->shopping_model->get_group_by_fechamento($fechamento_id);
        $this->user->config = $this->shopping_model->get_client_config($group->agrupamento_id);

        // busca fechamento
        $fechamento = $this->energy_model->GetLancamento($fechamento_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if(!$fechamento || is_null($fechamento->id)) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Lançamento não encontrado"));
            return;
        }

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Leitura Anterior', 'Leitura Atual', 'Total', 'Ponta', 'Fora Ponta', 'Total', 'Ponta', 'Fora Ponta' ]
		];

        $spreadsheet->getProperties()
			->setCreator('Easymeter')
			->setLastModifiedBy('Easymeter')
			->setTitle('Relatório de Consumo')
			->setSubject(strftime('%B/%Y', strtotime($fechamento->competencia)))
			->setDescription('Relatório de Consumo - '.$fechamento->nome.' - '.$fechamento->competencia)
			->setKeywords($fechamento->nome.' '.(strftime('%B/%Y', strtotime($fechamento->competencia))))
			->setCategory('Relatório')->setCompany('Easymeter');

        $split = 0;
        if ($this->user->config->split_report) {

            $spreadsheet->getActiveSheet()->setTitle($this->user->config->area_comum);

            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Unidades');
            $spreadsheet->addSheet($myWorkSheet, 1);

            $split = 1;
        }


        for ($i = 0; $i <= $split; $i++) {

            $spreadsheet->setActiveSheetIndex($i);

            $spreadsheet->getActiveSheet()->getStyle('A1:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($fechamento->nome));
            $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
            if ($this->user->config->split_report) {
                $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Consumo de Energia - '.($i == 0 ? $this->user->config->area_comum : "Unidades").' - '. date("d/m/Y", strtotime($fechamento->inicio)).' a '.date("d/m/Y", strtotime($fechamento->fim)));
            } else {
                $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Consumo de Energia - '.date("d/m/Y", $fechamento->inicio).' a '.date("d/m/Y", $fechamento->fim));
            }

            $spreadsheet->getActiveSheet()->mergeCells('A2:I2');

            $spreadsheet->getActiveSheet()->setCellValue('A4', 'Unidade')->mergeCells('A4:A5');
            $spreadsheet->getActiveSheet()->setCellValue('B4', 'Leitura')->mergeCells('B4:C4');
            $spreadsheet->getActiveSheet()->setCellValue('D4', 'Consumo - kWh')->mergeCells('D4:E4');
            $spreadsheet->getActiveSheet()->setCellValue('F4', 'Demanda - kW')->mergeCells('F4:G4');

            $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'B5');

            $spreadsheet->getActiveSheet()->getStyle('A1:I5')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A4:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $linhas = $this->energy_model->GetFechamentoUnidades($fechamento_id, $this->user->config, $i + 1);

            $spreadsheet->getActiveSheet()->fromArray($linhas, NULL, 'A6');

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);

            $spreadsheet->getActiveSheet()->getStyle('B6:I'.(count($linhas) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $spreadsheet->getActiveSheet()->setCellValue('A'.(count($linhas) + 7), 'Gerado em '.date("d/m/Y H:i"));

            $spreadsheet->getActiveSheet()->setSelectedCell('A1');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $filename = $fechamento->nome.' Energia - '.(strftime('%B/%Y', strtotime($fechamento->competencia)));

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
        $fechamentos = $this->energy_model->GetLancamentos($group_id);
        $group       = $this->shopping_model->get_group_info($group_id);
        $this->user->config = $this->shopping_model->get_client_config($group_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if(!$fechamentos) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Nenhum lançamento encontrado"));
            return;
        }
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        foreach($fechamentos as &$f) {
            $f['competencia'] = strftime('%B/%Y', strtotime($f['competencia']));
        }

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Total', 'Ponta', 'Fora Ponta', '', 'Total', 'Ponta', 'Fora Ponta' ]
		];

        $spreadsheet->getProperties()
			->setCreator('Easymeter')
			->setLastModifiedBy('Easymeter')
			->setTitle('Relatório de Lançamentos - Energia')
			->setSubject($group->group_name)
			->setDescription('Relatório de Lançamentos - Energia - '.$group->group_name)
			->setKeywords($group->group_name.' Lançamentos Energia')
			->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:L2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($group->group_name));
        $spreadsheet->getActiveSheet()->mergeCells('A1:L1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Lançamentos - Energia');
        $spreadsheet->getActiveSheet()->mergeCells('A2:L2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Competência')->mergeCells('A4:A6');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Data Inicial')->mergeCells('B4:B6');
        $spreadsheet->getActiveSheet()->setCellValue('C4', 'Data Final')->mergeCells('C4:C6');
        $spreadsheet->getActiveSheet()->setCellValue('D4', $this->user->config->area_comum)->mergeCells('D4:G4');
        $spreadsheet->getActiveSheet()->setCellValue('H4', "Unidades")->mergeCells('H4:K4');
        $spreadsheet->getActiveSheet()->setCellValue('L4', 'Emissão')->mergeCells('L4:L6');

        $spreadsheet->getActiveSheet()->setCellValue('D5', 'Consumo - kWh')->mergeCells('D5:F5');
        $spreadsheet->getActiveSheet()->setCellValue('G5', 'Demanda - kW')->mergeCells('G5:G6');
        $spreadsheet->getActiveSheet()->setCellValue('H5', 'Consumo - kWh')->mergeCells('H5:J5');
        $spreadsheet->getActiveSheet()->setCellValue('K5', 'Demanda - kW')->mergeCells('K5:K6');

        $spreadsheet->getActiveSheet()->getStyle('A1:L6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:L6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'D6');

		$spreadsheet->getActiveSheet()->fromArray($fechamentos, NULL, 'A7');

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(18);

        $spreadsheet->getActiveSheet()->getStyle('A7:L'.(count($fechamentos) + 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A'.(count($fechamentos) + 8), 'Gerado em '.date("d/m/Y H:i"));

		$spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);

        $filename = "Lançamentos Energia ".$group->group_name;

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

    public function download_unity()
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        $uid = $this->input->getPost('id');
        $eid = $this->input->getPost('fid');
        // busca fechamento
        $fechamento = $this->energy_model->get_faturamento_unidade($uid, $eid);
        $fechamento['competencia'] = strftime('%B/%Y', strtotime($fechamento['competencia']));

      
        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Total', 'Ponta', 'Fora Ponta']
		];

        $spreadsheet->getProperties()
        ->setCreator('Easymeter')
        ->setLastModifiedBy('Easymeter')
        ->setTitle('Relatório de Lançamentos - Energia')
        ->setSubject($fechamento['nome'])
        ->setDescription('Relatório de Lançamentos - Energia - '.$fechamento['nome'])
        ->setKeywords($fechamento['nome'].' Lançamentos Energia')
        ->setCategory('Relatório')->setCompany('Easymeter');

    $spreadsheet->getActiveSheet()->getStyle('A1:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($fechamento['nome']));
    $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
    $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Lançamentos - Energia');
    $spreadsheet->getActiveSheet()->mergeCells('A2:G2');

    $spreadsheet->getActiveSheet()->setCellValue('A4', 'Competência')->mergeCells('A4:A5');
    $spreadsheet->getActiveSheet()->setCellValue('B4', 'Loja')->mergeCells('B4:B5');
    $spreadsheet->getActiveSheet()->setCellValue('G4', 'Emissão')->mergeCells('G4:G5');

    $spreadsheet->getActiveSheet()->setCellValue('C4', 'Consumo - kWh')->mergeCells('C4:E4');
    $spreadsheet->getActiveSheet()->setCellValue('F4', 'Demanda - kW')->mergeCells('F4:F5');

    $spreadsheet->getActiveSheet()->getStyle('A1:G5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'C5');

    $spreadsheet->getActiveSheet()->fromArray($fechamento, NULL, 'A6');

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);


    $spreadsheet->getActiveSheet()->getStyle('A7:L'.(count($fechamento) + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->setCellValue('A'.(count($fechamento) + 4), 'Gerado em '.date("d/m/Y H:i"));

    $spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);

        $filename = $fechamento['nome'].' Energia - '.(strftime('%B/%Y', strtotime($fechamento['competencia'])));

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

    public function download_lancamento_unity()
    {
        $id = $this->input->getPost('id');
        
        // busca fechamento
        $fechamentos = $this->energy_model->get_faturamentos_unidade($id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if(!$fechamentos) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Nenhum lançamento encontrado"));
            return;
        }
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        foreach($fechamentos as &$f) {
            $f['competencia'] = strftime('%B/%Y', strtotime($f['competencia']));
        }

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Total', 'Ponta', 'Fora Ponta']
		];

        $spreadsheet->getProperties()
        ->setCreator('Easymeter')
        ->setLastModifiedBy('Easymeter')
        ->setTitle('Relatório de Lançamentos - Energia')
        ->setSubject($fechamentos[0]['nome'])
        ->setDescription('Relatório de Lançamentos - Energia - '.$fechamentos[0]['nome'])
        ->setKeywords($fechamentos[0]['nome'].' Lançamentos Energia')
        ->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:L2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($fechamentos[0]['nome']));
        $spreadsheet->getActiveSheet()->mergeCells('A1:L1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Lançamentos - Energia');
        $spreadsheet->getActiveSheet()->mergeCells('A2:L2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Competência')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Loja')->mergeCells('B4:B5');
        $spreadsheet->getActiveSheet()->setCellValue('G4', 'Emissão')->mergeCells('G4:G5');

        $spreadsheet->getActiveSheet()->setCellValue('C4', 'Consumo - kWh')->mergeCells('C4:E4');
        $spreadsheet->getActiveSheet()->setCellValue('F4', 'Demanda - kW')->mergeCells('F4:F5');

        $spreadsheet->getActiveSheet()->getStyle('A1:G5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'C5');

		$spreadsheet->getActiveSheet()->fromArray($fechamentos, NULL, 'A6');

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);


        $spreadsheet->getActiveSheet()->getStyle('A6:G'.(count($fechamentos) + 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A'.(count($fechamentos) + 8), 'Gerado em '.date("d/m/Y H:i"));

		$spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);

        $filename = "Lançamentos Energia ".$fechamentos[0]['nome'];

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

    public function download_resume()
    {
        $group_id = $this->input->getPost('id');

        $this->user->config = $this->shopping_model->get_client_config($group_id);

        if (!$this->user->config) {
            return json_encode(array(
                "status" => "error",
                "message" => "Dados não foram carregados corretamente. Configurações gerais não fornecidas."
            ));
        }

        $group  = $this->shopping_model->get_group_info($group_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Mês', 'Mês Anterior', 'Ponta', 'Fora Ponta', 'Últimas 24h', "Previsão Mês" ]
		];

        $spreadsheet->getProperties()
			->setCreator('Easymeter')
			->setLastModifiedBy('Easymeter')
			->setTitle('Relatório Resumo Energia')
			->setSubject(MonthName(date("m"))."/".date("Y"))
			->setDescription('Relatório Resumo - '.date("01/m/Y").' - '.date("d/m/Y"))
			->setKeywords($group->group_name.' Resumo '.MonthName(date("m"))."/".date("Y"))
			->setCategory('Relatório')->setCompany('Easymeter');

        $split = 0;
        if ($this->user->config->split_report) {

            $spreadsheet->getActiveSheet()->setTitle($this->user->config->area_comum);

            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Unidades');
            $spreadsheet->addSheet($myWorkSheet, 1);

            $split = 1;
        }

        for ($i = 0; $i <= $split; $i++) {

            $resume = $this->energy_model->GetResume($group_id, $this->user->config, $i + 1, $this->user->demo);

            $spreadsheet->setActiveSheetIndex($i);

            $spreadsheet->getActiveSheet()->getStyle('A1:J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($group->group_name));
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório Resumo Energia - '. date("01/m/Y").' a '.date("d/m/Y"));
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');

            $spreadsheet->getActiveSheet()->setCellValue('A4', 'Medidor')->mergeCells('A4:A5');
            $spreadsheet->getActiveSheet()->setCellValue('B4', 'Nome')->mergeCells('B4:B5');
            $spreadsheet->getActiveSheet()->setCellValue('C4', 'Leitura')->mergeCells('C4:C5');
            $spreadsheet->getActiveSheet()->setCellValue('D4', 'Consumo - kWh')->mergeCells('D4:I4');

            $spreadsheet->getActiveSheet()->getStyle('A1:J5')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);

            $spreadsheet->getActiveSheet()->getStyle('A6:A'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B6:B'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('C6:J'.(count($resume) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $spreadsheet->getActiveSheet()->setCellValue('A'.(count($resume) + 7), 'Gerado em '.date("d/m/Y H:i"));

            $spreadsheet->getActiveSheet()->setSelectedCell('A1');
        }

        $writer = new Xlsx($spreadsheet);

        $filename = "Resumo Energia ".$group->group_name;

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

    public function download_abnormal()
    {
        $group_id = $this->input->getPost('id');
        $device   = $this->input->getPost('device');
        $init     = $this->input->getPost('init');
        $end      = $this->input->getPost('finish');
        $type     = $this->input->getPost('type');
        $min      = $this->input->getPost('min');
        $max      = $this->input->getPost('max');

        // busca fechamento
        $data  = $this->energy_model->GetAbnormal($group_id, $device, $init, $end, $type, $min, $max);
        $group = $this->shopping_model->get_group_info($group_id);

        //TODO verificar se usuário tem acesso a esse fechamento

        // verifica retorno
        if(!$data) {
            // mostra erro
            echo json_encode(array("status"  => "error", "message" => "Dados não encontrados"));
            return;
        }

        $spreadsheet = new Spreadsheet();

		$titulos = [
			['Fase R', 'Fase S', 'Fase T', 'Fase R', 'Fase S', 'Fase T', 'Fase R', 'Fase S', 'Fase T', 'Fase R', 'Fase S', 'Fase T' ]
		];

        $unit = type2unit($type);

        $spreadsheet->getProperties()
			->setCreator('Easymeter')
			->setLastModifiedBy('Easymeter')
			->setTitle('Relatório de Anormalidades')
			->setSubject($unit["name"] . date_create_from_format('Y-m-d', $init)->format('d/m/Y') ." a ".date_create_from_format('Y-m-d', $end)->format('d/m/Y'))
			->setDescription('Relatório de Anormalidades - '.$unit["name"])
			->setKeywords($group->group_name.' Anormalidades')
			->setCategory('Relatório')->setCompany('Easymeter');

        $spreadsheet->getActiveSheet()->getStyle('A1:N3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A1', strtoupper($group->group_name));
        $spreadsheet->getActiveSheet()->mergeCells('A1:N1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Relatório de Anormalidades - '.date_create_from_format('Y-m-d', $init)->format('d/m/Y') ." a ".date_create_from_format('Y-m-d', $end)->format('d/m/Y')." - ".$unit["name"]." menor que $min ".$unit["unit"]." ou ".$unit["name"]." maior que $max ".$unit["unit"]);
        $spreadsheet->getActiveSheet()->mergeCells('A2:N2');

        $spreadsheet->getActiveSheet()->setCellValue('A4', 'Horário')->mergeCells('A4:A5');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'Tensão - V')->mergeCells('B4:D4');
        $spreadsheet->getActiveSheet()->setCellValue('E4', 'Corrente - A')->mergeCells('E4:G4');
        $spreadsheet->getActiveSheet()->setCellValue('H4', 'Potência Ativa Instantânea -kW')->mergeCells('H4:J4');
        $spreadsheet->getActiveSheet()->setCellValue('K4', 'Potência Reativa Instantânea -kVAr')->mergeCells('K4:M4');
        $spreadsheet->getActiveSheet()->setCellValue('N4', 'Consumo - kWh')->mergeCells('N4:N5');

        $spreadsheet->getActiveSheet()->getStyle('A1:N5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:N5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->fromArray($titulos, NULL, 'B5');

		$spreadsheet->getActiveSheet()->fromArray($data, NULL, 'A6');

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);

        $spreadsheet->getActiveSheet()->getStyle('A6:N'.(count($data) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A'.(count($data) + 7), 'Gerado em '.date("d/m/Y H:i"));

		$spreadsheet->getActiveSheet()->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);

        $filename = "Anormalidades ".$group->group_name;

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

    public function resume_demo()
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

        $dt = $this->datatables->query("SELECT 
                esm_medidores.nome AS device, 
                esm_unidades_config.luc AS luc, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                RAND() * 100000 AS value_read,
                RAND() * 100000 AS value_month,
                RAND() * 100000 AS value_month_open,
                RAND() * 100000 AS value_month_closed,
                RAND() * 100000 AS value_ponta,
                RAND() * 100000 AS value_fora,
                RAND() * 100000 AS value_last,
                RAND() * 100000 AS value_future,
                RAND() * 100000 AS value_last_month
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            LEFT JOIN (
                SELECT 
                    device,
                    SUM(activePositiveConsumption) AS value
                FROM 
                    esm_leituras_".$entity->tabela."_energia
                WHERE 
                    timestamp > UNIX_TIMESTAMP() - 86400
                GROUP BY device
            ) l ON l.device = esm_medidores.nome
            LEFT JOIN (
                SELECT 
                    d.device,
                    SUM(activePositiveConsumption) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_".$entity->tabela."_energia d ON
                (d.timestamp) > (esm_calendar.ts_start) AND
                (d.timestamp) <= (esm_calendar.ts_end + 600) 
                WHERE 
                    esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND
                    esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.device
                ) m ON m.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                                    device,
                                    SUM(activePositiveConsumption) AS value
                                FROM 
                                    esm_leituras_".$entity->tabela."_energia
                                WHERE 
                                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                GROUP BY device                
                            ) c ON c.device = esm_medidores.nome
                        LEFT JOIN (
                    SELECT 
                                    device,
                                    SUM(activePositiveConsumption) AS value
                                FROM 
                                    esm_leituras_".$entity->tabela."_energia
                                WHERE 
                                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now())
                                    AND (MOD((timestamp), 86400) >= {$this->user->config->open} AND MOD((timestamp), 86400) <= {$this->user->config->close})
                                GROUP BY device
                            ) h ON h.device = esm_medidores.nome
                        LEFT JOIN (
                    SELECT 
                                    d.device,
                                    SUM(activePositiveConsumption) AS value
                                FROM esm_calendar
                                LEFT JOIN esm_leituras_".$entity->tabela."_energia d ON
                (d.timestamp) > (esm_calendar.ts_start) AND
                (d.timestamp) <= (esm_calendar.ts_end + 600)
                AND ((MOD((d.timestamp), 86400) >= {$this->user->config->ponta_start} AND MOD((d.timestamp), 86400) <= {$this->user->config->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)
                                WHERE 
                                    esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND
                                    esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                                GROUP BY d.device
                            ) p ON p.device = esm_medidores.nome
                        WHERE 
                            esm_unidades.agrupamento_id = " . $this->input->getPost("group") . " AND
                esm_medidores.tipo = 'energia'
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
            return number_format($data["value_last"], 3, ",", ".");
        });

        $dt->edit('value_month', function ($data) {
            return number_format($data["value_month"], 3, ",", ".");
        });

        $dt->edit('value_fora', function ($data) {
            return number_format($data["value_fora"], 3, ",", ".");
        });

        $dt->edit('value_ponta', function ($data) {
            return number_format($data["value_ponta"], 3, ",", ".");
        });

        $dt->edit('value_last_month', function ($data) {
            return number_format($data["value_last_month"], 3, ",", ".");
        });

        $dt->edit('value_future', function ($data) {
            $icon = "";
            if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-up-alt text-danger ms-2\"></i>";
            else if ($data["value_future"] > $data["value_last_month"])
                $icon = "<i class=\"fa fa-level-down-alt text-success ms-2\"></i>";

            return number_format($data["value_future"], 3, ",", ".").$icon;
        });

        // gera resultados
        echo $dt->generate();
    }

    
}