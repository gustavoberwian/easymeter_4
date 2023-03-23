<?php
function avatar($avatar)
{
    if ($avatar == 'none') {

        return base_url('assets/img/user.png');

    } elseif  (file_exists('uploads/avatars/'.$avatar)) {

        return base_url('uploads/avatars/' . $avatar);

    } else {

        // verificar qdo mostra
        return base_url('assets/img/sistema.png');
    }
}

function group2page($auth)
{
    if ($auth->in_group(array('admin', 'shopping'))) {
        return 'shopping';
    } elseif ($auth->in_group('admin')) {
        return 'admin';
    } elseif ($auth->in_group('shopping')) {
        return 'shopping';
    }

    return "site";
}

function type2unit($type)
{
    if ($type == "voltage")
        return array("name" => "Tensão", "unit" => "V");
    else if ($type == "current")
        return array("name" => "Corrente", "unit" => "A");
    else if ($type == "active")
        return array("name" => "Potência Ativa", "unit" => "kW");
    else if ($type == "reactive")
        return array("name" => "Potência Reativa", "unit" => "kVAr");
    else if ($type == "activePositiveConsumption")
        return array("name" => "Potência Reativa", "unit" => "kWh");
}

function weekDayName($day)
{
    $names = array('', 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');

    return $names[$day];
}

function MonthName($month)
{
    $names = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

    return $names[intval($month)];
}

function alerta_tipo2icon($tipo, $class = '')
{
    if ($tipo == 1)
        return '<span class="badge badge-info"> Informativo </span>';
    if ($tipo == 2)
        return '<span class="badge badge-warning"> Preventivo </span>';
    if ($tipo == 3)
        return '<span class="badge badge-danger"> Perigoso </span>';;
}

function alerta_tipo2color($tipo)
{
    if ($tipo == 1) {
        return 'info';
    } else if ($tipo == 2) {
        return 'warning';
    } else if ($tipo == 3) {
        return 'danger';
    }
}

function time_ago($date)
{
    $timestamp = strtotime($date);

    $strTime = array("segundo", "minuto", "hora", "dia", "mês", "ano");
    $strPlural = array("segundos", "minutos", "horas", "dias", "meses", "anos");
    $strUnidade = array("um", "um", "uma", "um", "um", "um");
    $length = array("60", "60", "24", "30", "12", "10");

    $currentTime = time();
    if ($currentTime >= $timestamp) {
        $diff = time() - $timestamp;

        if ($diff > 172800) return date('d/m/Y', $timestamp);       // 2 dias: só data
        if ($diff > 86400) return date('d/m/Y h:i', $timestamp);    // 1 dia: data e hora

        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        }

        $diff = round($diff);

        if ($diff == 1)
            $time_ago = $strUnidade[$i] . " " . $strTime[$i] . " atrás ";
        else
            $time_ago = $diff . " " . (($diff > 1) ? $strPlural[$i] : $strTime[$i]) . " atrás ";

        $time_ago = str_replace('um dia atrás', 'ontem', $time_ago);

        $time_ago = str_replace('0 segundo atrás', 'agora mesmo', $time_ago);

        return $time_ago;
    }
}


function user_groups_nice($id, $ion_auth)
{
    if ($ion_auth->in_group(array('industria'), $id, true))
        return 'Indústria';
    if ($ion_auth->in_group(array('sindicos', 'unidades'), $id, true))
        return 'Morador e Síndico';
    if ($ion_auth->in_group(array('sindicos', 'proprietarios'), $id, true))
        return 'Proprietário e Síndico';
    if ($ion_auth->in_group('sindicos', $id))
        return 'Síndico';
    if ($ion_auth->in_group('proprietarios', $id))
        return 'Proprietário';
    if ($ion_auth->in_group('unidades', $id))
        return 'Morador';
    if ($ion_auth->in_group('admin', $id))
        return 'Administrador';
    if ($ion_auth->in_group('zelador', $id))
        return 'Zelador';
    if ($ion_auth->in_group('monitoramento', $id))
        return 'Monitoramento';
    if ($ion_auth->in_group('administradora', $id))
        return 'Administradora';
    if ($ion_auth->in_group('representante', $id))
        return 'Representante';
    if ($ion_auth->in_group('trc', $id))
        return 'NeoWater';

    return 'Usuário';
}

function competencia_nice($competencia, $sep = '/')
{
    $c = explode('/', $competencia);
    $meses = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
    return $meses[intval($c[0])] . $sep . $c[1];
}

function set_historico($user_id, $description)
{
    //$CI =& get_instance();

    //$CI->db->insert('esm_user_logs', array('user_id' => $user_id, 'descricao' => $description));
}

function checkDateFormat($date)
{
    $d = explode('-', $date);

    return checkdate($d[1] ?? 0, $d[2] ?? 0, $d[0] ?? 0);
}

function format_online_status($ts, $central = "")
{
    if ($ts == 0)
        return 'text-muted';
    elseif ($ts > time() - 3600)
        return 'text-success';
    elseif ($ts > time() - 3600 * 2)
        return 'text-warning';
    else
        return 'text-danger';
}