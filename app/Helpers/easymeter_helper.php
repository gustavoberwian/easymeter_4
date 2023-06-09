<?php


use CodeIgniter\Database\Config;
use CodeIgniter\Database\Exceptions\DatabaseException;
function avatar($avatar)
{
    if ($avatar == '') {

        return base_url('assets/img/user.png');
        

    } elseif  (file_exists('assets/img/uploads/avatars/' . $avatar)) {

        return base_url('assets/img/uploads/avatars/' . $avatar);
        // return base_url('assets/img/sistema.png');

    } else {

        // verificar qdo mostra
        return base_url('assets/img/sistema.png');
       
    }
}

function group2page($auth)
{
    if ($auth->inGroup(array('admin', 'shopping'))) {
        return 'shopping';
    } elseif ($auth->inGroup('admin')) {
        return 'admin';
    } elseif ($auth->inGroup('shopping')) {
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


function user_groups_nice($id)
{
    $users = model('UserModel');
    $user = $users->findById($id);

    if ($user->inGroup('superadmin'))
        return 'Super Admin';
    if ($user->inGroup('industria'))
        return 'Indústria';
    if ($user->inGroup('sindicos', 'unidades'))
        return 'Morador e Síndico';
    if ($user->inGroup('sindicos', 'proprietarios'))
        return 'Proprietário e Síndico';
    if ($user->inGroup('sindicos'))
        return 'Síndico';
    if ($user->inGroup('proprietarios'))
        return 'Proprietário';
    if ($user->inGroup('unidades'))
        return 'Morador';
    if ($user->inGroup('admin'))
        return 'Administrador';
    if ($user->inGroup('zelador'))
        return 'Zelador';
    if ($user->inGroup('monitoramento'))
        return 'Monitoramento';
    if ($user->inGroup('administradora'))
        return 'Administradora';
    if ($user->inGroup('representante'))
        return 'Representante';
    if ($user->inGroup('trc'))
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

function numberToColor($value, $min, $max, $gradientColors = null)
{
    // Ensure value is in range
    if ($value < $min) {
        $value = $min;
    }
    if ($value > $max) {
        $value = $max;
    }

    // Normalize min-max range to [0, positive_value]
    $max -= $min;
    $value -= $min;
    $min = 0;

    // Calculate distance from min to max in [0,1]
    $distFromMin = $value / $max;

    // Define start and end color
    if (count($gradientColors) == 0) {
        return numberToColor($value, $min, $max, ['#CC0000', '#EEEE00', '#00FF00']);
    } else if (count($gradientColors) == 2) {
        $startColor = $gradientColors[0];
        $endColor = $gradientColors[1];
    } else if (count($gradientColors) > 2) {
        $startColor = $gradientColors[floor($distFromMin * (count($gradientColors) - 1))];
        $endColor = $gradientColors[ceil($distFromMin * (count($gradientColors) - 1))];

        $distFromMin *= count($gradientColors) - 1;
        while ($distFromMin > 1) {
            $distFromMin--;
        }
    } else {
        die("Please pass more than one color or null to use default red-green colors.");
    }

    // Remove hex from string
    if ($startColor[0] === '#') {
        $startColor = substr($startColor, 1);
    }
    if ($endColor[0] === '#') {
        $endColor = substr($endColor, 1);
    }

    // Parse hex
    list($ra, $ga, $ba) = sscanf("#$startColor", "#%02x%02x%02x");
    list($rz, $gz, $bz) = sscanf("#$endColor", "#%02x%02x%02x");

    // Get rgb based on
    $distFromMin = $distFromMin;
    $distDiff = 1 - $distFromMin;
    $r = intval(($rz * $distFromMin) + ($ra * $distDiff));
    $r = min(max(0, $r), 255);
    $g = intval(($gz * $distFromMin) + ($ga * $distDiff));
    $g = min(max(0, $g), 255);
    $b = intval(($bz * $distFromMin) + ($ba * $distDiff));
    $b = min(max(0, $b), 255);

    // Convert rgb back to hex
    $rgbColorAsHex = '#' .
        str_pad(dechex($r), 2, "0", STR_PAD_LEFT) .
        str_pad(dechex($g), 2, "0", STR_PAD_LEFT) .
        str_pad(dechex($b), 2, "0", STR_PAD_LEFT);

    return $rgbColorAsHex;
}

function entrada_icon($tipo, $class = '', $cor = false)
{
    if ($tipo == 'agua')
        return '<i class="fas fa-tint '.($cor ? $cor : 'text-primary').' '.$class.'" title="Água"></i>';
    if ($tipo == 'gas')
        return '<i class="fas fa-fire '.($cor ? $cor : 'text-warning').' '.$class.'" title="Gás"></i>';
    if ($tipo == 'energia')
        return '<i class="fas fa-bolt '.($cor ? $cor : 'text-danger').' '.$class.'" title="Energia"></i>';
}
function table_exists($tableName)
{
    $defaultDB = db_connect();

    // Consulta SQL para verificar a existência da tabela no banco de dados padrão
    $query = "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$defaultDB->database}' AND table_name = '{$tableName}'";

    $result = $defaultDB->query($query)->getRow();

    if ($result->count > 0) {
        return true;
    } else {
        $alternateDB = db_connect('easy_com_br');

        // Consulta SQL para verificar a existência da tabela no banco de dados alternativo
        $query = "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$alternateDB->database}' AND table_name = '{$tableName}'";

        $result = $alternateDB->query($query)->getRow();

        if ($result->count > 0) {
            return false;
        } 
    }
}
function format_pb_comparativo($consumo, $consumos)
{
    if (max($consumos) == $consumo)
        return 'danger';
    else if (min($consumos) == $consumo)
        return 'success';
    else
        return 'warning';

}
function num2alpha($num) {
    $str = '';
    while ($num > 0) {
        $num--;
        $remainder = $num % 26;
        $str = chr(65 + $remainder) . $str;
        $num = intval($num / 26);
    }
    return $str;
}
