<?php

namespace App\Models;

class Gas_model extends Base_model
{
    public function GetConsumption($device, $group_id, $start, $end, $st = array(), $group = true, $interval = null, $demo = false)
    {
        $entity = $this->get_entity_by_group($group_id);

        $dvc = "";
        $dvc1 = "";
        if (is_numeric($device)) {

            $dvc = "LEFT JOIN esm_medidores on esm_medidores.id = esm_leituras_" . $entity->tabela . "_gas.medidor_id AND esm_medidores.nome IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";

        } else if ($device == "C") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_gas.medidor_id
                    LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id AND esm_unidades.bloco_id = $group_id
                    LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 1 AND esm_unidades.bloco_id = $group_id";

        } else if ($device == "U") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_gas.medidor_id
                    LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id AND esm_unidades.bloco_id = $group_id
                    LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 2 AND esm_unidades.bloco_id = $group_id";

        } else if ($device == "T") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_gas.medidor_id
                    LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id AND esm_unidades.bloco_id = $group_id";

        } else if ($device == "ALL") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_" . $entity->tabela . "_gas.medidor_id
                    LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id AND esm_unidades.bloco_id = $group_id";

        } else {

            $dvc1 = "LEFT JOIN esm_medidores ON esm_medidores.nome = '$device'";
            $dvc = "AND medidor_id = esm_medidores.id";

        }

        $station = "";
        if (count($st)) {
            if ($st[0] == 'opened') {
                $station = "AND HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$st[1]})) AND HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$st[2]}))";
            } else if ($st[0] == 'closed') {
                $station = "AND (HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$st[1]})) OR HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$st[2]})))";
            }
        }

        $value = "SUM(consumo) AS value";

        if ($interval === 'h') {

            if ($demo)
                $value = "RAND() * 10 AS value";

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    $value
                FROM esm_hours
                $dvc1
                LEFT JOIN esm_leituras_" . $entity->tabela . "_gas ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > $start AND 
                    timestamp <= $end + 600
                    $station
                    $dvc
                $group_by
                ORDER BY esm_hours.num
            ");

        } elseif ($start == $end) {

            if ($demo)
                $value = "RAND() * 10 AS value";

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    $value
                FROM esm_hours
                $dvc1
                LEFT JOIN esm_leituras_" . $entity->tabela . "_gas ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    timestamp <= UNIX_TIMESTAMP('$end 23:59:59') + 600
                    $station
                    $dvc
                $group_by
                ORDER BY esm_hours.num
            ");

        } else {

            if ($demo)
                $value = "RAND() * 100 AS value";

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_calendar.dt";

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    $value
                FROM esm_calendar
                $dvc1
                LEFT JOIN esm_leituras_" . $entity->tabela . "_gas ON 
                    timestamp > esm_calendar.ts_start AND 
                    timestamp <= (esm_calendar.ts_end + 600)
                    $station
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                $group_by
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetDeviceLastRead($device, $gid = 0)
    {
        if ($device == "C") {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 1
                WHERE esm_medidores.tipo = 'gas'";
        } else if ($device == "U") {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 2
                WHERE esm_medidores.tipo = 'gas'";
        } else if (is_numeric($device)) {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                WHERE esm_medidores.nome IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";

        } else if ($device == "T") {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE esm_unidades.bloco_id = $gid AND esm_medidores.tipo = 'gas'";
        } else {
            $query = "
                SELECT ultima_leitura AS value
                FROM esm_medidores
                WHERE nome = '$device'
            ";
        }

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            return $result->getRow()->value;
        }

        return false;
    }

    public function GetResume($group, $config, $type, $demo = false)
    {
        $entity = $this->get_entity_by_group($group);

        $tabela = "esm_leituras_" . $entity->tabela . "_gas";

        $values = "LPAD(ROUND(esm_medidores.ultima_leitura, 0), 6, '0') AS value_read,
                FORMAT(m.value, 0, 'de_DE') AS value_month,
                FORMAT(h.value, 0, 'de_DE') AS value_month_open,
                FORMAT(m.value - h.value, 0, 'de_DE') AS value_month_closed,
                FORMAT(l.value, 0, 'de_DE') AS value_last,
                FORMAT(m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())), 0, 'de_DE') AS value_future";

        if ($demo) {

            $tabela = "esm_leituras_" . $entity->tabela . "_gas_demo";

            $values = "RAND() * 10000 AS value_read,
                RAND() * 10000 AS value_month,
                RAND() * 10000 AS value_month_open,
                RAND() * 10000 AS value_month_closed,
                RAND() * 10000 AS value_last,
                RAND() * 10000 AS value_future";
        }

        $result = $this->db->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades_config.luc AS luc, 
                esm_unidades.nome AS name, 
                $values
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
                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now()) AND
                    HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$config->open})) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$config->close}))
                GROUP BY medidor_id
            ) h ON h.device = esm_medidores.nome
            WHERE 
                esm_unidades.bloco_id = $group AND
                esm_medidores.tipo = 'gas'
            ORDER BY 
                esm_unidades_config.type, esm_unidades.nome
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }
}