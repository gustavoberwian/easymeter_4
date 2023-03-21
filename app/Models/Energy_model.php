<?php

namespace App\Models;

class Energy_model extends Base_model
{
    public function GetOverallConsumption($type, $grp)
    {
        $result = $this->db->query("
            SELECT
                esm_unidades.bloco_id,
                SUM( activePositiveConsumption ) AS value,
                SUM( activePositiveConsumption ) / ( DATEDIFF( CURDATE(), DATE_FORMAT( CURDATE(), '%Y-%m-01' )) + 1 ) * DAY (LAST_DAY(CURDATE())) AS prevision,
                SUM( activePositiveConsumption ) / ( DATEDIFF( CURDATE(), DATE_FORMAT( CURDATE(), '%Y-%m-01' )) + 1 ) AS average 
            FROM
                esm_leituras_ancar_energia 
            JOIN 
                esm_medidores ON esm_medidores.id = esm_leituras_ancar_energia.device
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id 
            WHERE
                TIMESTAMP > DATE_FORMAT( CURDATE(), '%Y-%m-01' ) 
                AND esm_leituras_ancar_energia.device IN (
                    SELECT
                        esm_medidores.nome 
                    FROM
                        esm_unidades_config
                        JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades_config.unidade_id
                        JOIN esm_unidades ON esm_unidades.id = esm_unidades_config.unidade_id 
                    WHERE esm_unidades.bloco_id = $grp AND esm_unidades_config.type = $type
                )
        ");

        if ($result->getNumRows()) {
            return array (
                "bloco"    => number_format(round($result->getRow()->bloco_id, 0), 0, ",", "."),
                "consum"    => number_format(round($result->getRow()->value, 0), 0, ",", "."),
                "prevision" => number_format(round($result->getRow()->prevision, 0), 0, ",", "."),
                "average"   => number_format(round($result->getRow()->average, 0), 0, ",", ".")
            );
        }

        return array ("consum"    => "-","prevision" => "-","average"   => "-");
    }

    //TODO exclude: usada apenas pelo mapa de calor
    public function get_values_factor($device, $interval, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else {
            $dvc = " AND d.device = '$device'";
        }

        $group = $interval == "day" ? "timestamp" : "DAY(FROM_UNIXTIME(timestamp)), MONTH(FROM_UNIXTIME(timestamp)), YEAR(FROM_UNIXTIME(timestamp))";
        $label = $interval == "day" ? "%H:%i" : "%d/%m";

        $result = $this->db->query("
            SELECT
                SUM(reactiveA) AS reactive_a,
                SUM(reactiveB) AS reactive_b,
                SUM(reactiveC) AS reactive_c,
                SUM(activeA) / SQRT(POW(SUM(activeA), 2) + POW(SUM(reactiveA), 2)) AS factor_a,
                SUM(activeB) / SQRT(POW(SUM(activeB), 2) + POW(SUM(reactiveB), 2)) AS factor_b,
                SUM(activeC) / SQRT(POW(SUM(activeC), 2) + POW(SUM(reactiveC), 2)) AS factor_c,
                SUM(reactiveA + reactiveB + reactiveC) as reactive,
                SUM(activeA + activeB + activeC) / SQRT(POW(SUM(activeA + activeB + activeC), 2) + POW(SUM(reactiveA + reactiveB + reactiveC), 2)) AS factor,
                DATE_FORMAT(FROM_UNIXTIME(timestamp), '$label') AS label
            FROM
                esm_leituras_ancar_energia
            WHERE
                timestamp BETWEEN $start AND $end AND
                $dvc
            GROUP BY $group
        ");

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetActivePositive($device, $start, $end, $st = array(), $gp = false)
    {
        $query = "";
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }


        if ($start == $end) {

            $station = "";
            if (count($st)) {
                if ($st[0] == 'fora') {
                    $station = " AND (MOD((d.timestamp), 86400) < $st[1] OR MOD((d.timestamp), 86400) > $st[2])";
                } else if ($st[0] == 'ponta') {
                    $station = "AND (MOD((d.timestamp), 86400) >= $st[1] AND MOD((d.timestamp), 86400) <= $st[2])";
//                } else if ($st == 'inter') {
//                    $station = "AND ((MOD((d.timestamp), 86400) >= 59400 AND MOD((d.timestamp), 86400) < 63000) OR (MOD((d.timestamp), 86400) > 73800 AND MOD((d.timestamp), 86400) <= 77400))";
                }
            }

            $group = "";
            if (!$gp)
                $group = "GROUP BY esm_hours.num";

            $query = "
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    SUM(activePositiveConsumption) AS value
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $station
                    $dvc
                $group
                ORDER BY esm_hours.num
            ";

        } else {

            $station = "";
            if (count($st)) {
                if ($st[0] == 'fora') {
                    $station = " AND (((MOD((d.timestamp), 86400) < $st[1] OR MOD((d.timestamp), 86400) > $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7) OR esm_calendar.dw = 1 OR esm_calendar.dw = 7)";
                } else if ($st[0] == 'ponta') {
                    $station = "AND ((MOD((d.timestamp), 86400) >= $st[1] AND MOD((d.timestamp), 86400) <= $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)";
                    //            } else if ($st == 'inter') {
                    //                $station = "AND (((MOD((d.timestamp), 86400) >= 59400 AND MOD((d.timestamp), 86400) < 63000) OR (MOD((d.timestamp), 86400) > 73800 AND MOD((d.timestamp), 86400) <= 77400)) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)";
                }
            }

            $group = "";
            if (!$gp)
                $group = "GROUP BY esm_calendar.dt";

            $query = "
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    SUM(activePositiveConsumption) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    $station
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                $group
                ORDER BY esm_calendar.dt
            ";
        }

        // if you need to see te query, just uncomment
        //return $query;

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetActivePositiveAverage($device, $st = array(), $period = true)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        $station = "";
        if (count($st)) {
            if ($st[0] == 'fora') {
                $station = " AND (((MOD((d.timestamp), 86400) < $st[1] OR MOD((d.timestamp), 86400) > $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7) OR esm_calendar.dw = 1 OR esm_calendar.dw = 7)";
            } else if ($st[0] == 'ponta') {
                $station = "AND ((MOD((d.timestamp), 86400) >= $st[1] AND MOD((d.timestamp), 86400) <= $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)";
            }
        }

        $where = "";
        if ($period)
            $where = "esm_calendar.dt BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()";
        else
            $where = "esm_calendar.dt = CURDATE() - INTERVAL 30 DAY";

        $result = $this->db->query("
            SELECT AVG(d.value) AS value
            FROM (
                SELECT 
                    IFNULL(SUM(activePositiveConsumption), 0) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end + 600) 
                    $station
                    $dvc
                WHERE 
                    $where
                GROUP BY 
                    esm_calendar.dt
                ORDER BY 
                    esm_calendar.dt
            ) d
        ");

        if ($result->getNumRows()) {
            return $result->getRow()->value;
        }

        return false;
    }

    public function GetConsumptionDay($device)
    {
        $dvc = "";
        $join = "";
        if (is_numeric($device)) {
            if ($device != 0) {
                $dvc = " AND esm_leituras_ancar_energia.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }
        } else if ($device == "C") {
            $dvc = "AND device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";
        } else if ($device == "U") {
            $dvc = "AND device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";
        } else {
            $dvc = " AND esm_leituras_ancar_energia.device = '$device'";
        }

        $result = $this->db->query("
            SELECT 
                SUM(activePositiveConsumption) AS value,
                IF(MINUTE(FROM_UNIXTIME(timestamp)) = 0, DATE_FORMAT(FROM_UNIXTIME(timestamp), \"%H:%i\"), \"\") AS label,
                DATE_FORMAT(FROM_UNIXTIME(timestamp), \"%H:%i\") AS title
            FROM 
                esm_leituras_ancar_energia
            $join
            WHERE 
                timestamp > UNIX_TIMESTAMP() - 86400
                $dvc
            GROUP BY 
                title
            ORDER BY 
                timestamp
        ");

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;

    }

    public function GetActiveDemand($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    MAX(activeDemand) AS valueMax,
                    SUM(activePositiveConsumption) AS valueSum
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    MAX(activeDemand) AS valueMax,
                    SUM(activePositiveConsumption) AS valueSum
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetMainReactive($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    SUM(reactivePositiveConsumption) AS valueInd,
                    SUM(ABS(reactiveNegativeConsumption)) AS valueCap
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    SUM(reactivePositiveConsumption) AS valueInd,
                    SUM(ABS(reactiveNegativeConsumption)) AS valueCap
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetMainFactor($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(esm_hours.num, ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    IF(SUM(reactivePositiveConsumption) > SUM(ABS(reactiveNegativeConsumption)), 'I', 'C') AS type,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(SUM(activePositiveConsumption) / SQRT(POW(SUM(activePositiveConsumption), 2) + POW(SUM(reactivePositiveConsumption) + SUM(ABS(reactiveNegativeConsumption)), 2)), 1)) AS value
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    IF(SUM(reactivePositiveConsumption) > SUM(ABS(reactiveNegativeConsumption)), 'I', 'C') AS type,
                    IF(esm_calendar.dt > DATE_FORMAT(CURDATE() ,'%Y-%m-%d'), NULL, IFNULL(SUM(activePositiveConsumption) / SQRT(POW(SUM(activePositiveConsumption), 2) + POW(SUM(reactivePositiveConsumption) + SUM(ABS(reactiveNegativeConsumption)), 2)), 1)) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetFactorPhases($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(esm_hours.num, ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    IF(SUM(reactiveA) > 0, 'I', 'C') AS type_a,
                    IF(SUM(reactiveB) > 0, 'I', 'C') AS type_b,
                    IF(SUM(reactiveC) > 0, 'I', 'C') AS type_c,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(SUM(activeA) / SQRT(POW(SUM(activeA), 2) + POW(SUM(ABS(reactiveA)), 2)), 1)) AS value_a,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(SUM(activeB) / SQRT(POW(SUM(activeB), 2) + POW(SUM(ABS(reactiveB)), 2)), 1)) AS value_b,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(SUM(activeC) / SQRT(POW(SUM(activeC), 2) + POW(SUM(ABS(reactiveC)), 2)), 1)) AS value_c
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label,
                    esm_calendar.dt AS date, 
                    esm_calendar.dw AS dw,
                    IF(SUM(reactiveA) > 0, 'I', 'C') AS type_a,
                    IF(SUM(reactiveB) > 0, 'I', 'C') AS type_b,
                    IF(SUM(reactiveC) > 0, 'I', 'C') AS type_c,
                    IFNULL(SUM(activeA) / SQRT(POW(SUM(activeA), 2) + POW(SUM(ABS(reactiveA)), 2)), 1) AS value_a,
                    IFNULL(SUM(activeB) / SQRT(POW(SUM(activeB), 2) + POW(SUM(ABS(reactiveB)), 2)), 1) AS value_b,
                    IFNULL(SUM(activeC) / SQRT(POW(SUM(activeC), 2) + POW(SUM(ABS(reactiveC)), 2)), 1) AS value_c
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetMainLoad($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(esm_hours.num, ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(AVG(activePositiveConsumption + ABS(activePositiveConsumption)) / MAX(activePositiveConsumption + ABS(activePositiveConsumption)), 1)) AS value
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    IFNULL(AVG(activePositiveConsumption + ABS(activePositiveConsumption)) / MAX(activePositiveConsumption + ABS(activePositiveConsumption)), 1) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetValuesPhases($device, $start, $end, $field)
    {
        $operation["active"] = ["active", "SUM("];
        $operation["current"] = ["current", "AVG("];
        $operation["voltage"] = ["voltage", "AVG("];
        $operation["power"] = ["active", "MAX("];
        $operation["reactive"] = ["active", "SUM(ABS"];

        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    {$operation[$field][1]}({$operation[$field][0]}A)) AS value_a,
                    {$operation[$field][1]}({$operation[$field][0]}B)) AS value_b,
                    {$operation[$field][1]}({$operation[$field][0]}C)) AS value_c
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    {$operation[$field][1]}({$operation[$field][0]}A)) AS value_a,
                    {$operation[$field][1]}({$operation[$field][0]}B)) AS value_b,
                    {$operation[$field][1]}({$operation[$field][0]}C)) AS value_c
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                        d.timestamp >= esm_calendar.ts_start AND 
                        d.timestamp <= esm_calendar.ts_end 
                        $dvc
                WHERE 
                        esm_calendar.dt >= '$start' AND 
                        esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetLoadPhases($device, $start, $end, $field)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(AVG(activeA) / MAX(activeA), 1)) AS value_a,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(AVG(activeB) / MAX(activeB), 1)) AS value_b,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(AVG(activeC) / MAX(activeC), 1)) AS value_c
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    IFNULL(AVG(activeA) / MAX(activeA), 1) AS value_a,
                    IFNULL(AVG(activeB) / MAX(activeB), 1) AS value_b,
                    IFNULL(AVG(activeC) / MAX(activeC), 1) AS value_c
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                        d.timestamp >= esm_calendar.ts_start AND 
                        d.timestamp <= esm_calendar.ts_end 
                        $dvc
                WHERE 
                        esm_calendar.dt >= '$start' AND 
                        esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetDeviceLastRead($device)
    {
        if ($device == "C") {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 1
                WHERE esm_medidores.tipo = 'energia'";
        } else if ($device == "U") {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 2
                WHERE esm_medidores.tipo = 'energia'";
        } else if (is_numeric($device)) {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                WHERE esm_medidores.nome IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
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

    public function GetClientConfig($gid)
    {
        $result = $this->db->query("
            SELECT 
                *
            FROM esm_client_config
            WHERE 
                group_id = $gid
        ");

        if ($result->getNumRows()) {
            return $result->getRow();
        }

        return false;
    }

    public function GetAlertCfg($aid)
    {
        $result = $this->db->query("
            SELECT 
                esm_alertas_cfg.notify_shopping,
                esm_alertas_cfg.notify_unity,
                esm_alertas_cfg.description,
                esm_alertas_cfg_devices.device            
            FROM esm_alertas_cfg
            JOIN 
                esm_alertas_cfg_devices ON esm_alertas_cfg_devices.config_id = esm_alertas_cfg.id
            WHERE 
                esm_alertas_cfg.active = 1 AND esm_alertas_cfg.type = $aid
        ");

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function GetAlert($aid, $device = "")
    {
        if ($aid == 0) {

            return false;

        } else if ($aid == 1) {

            $result = $this->db->query("
                SELECT 
                    today.value AS today,
                    last.value AS last
                FROM (
                    SELECT 
                        IFNULL(SUM(activePositiveConsumption), 0) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_ancar_energia d ON 
                        d.timestamp > (esm_calendar.ts_start) AND 
                        d.timestamp <= (esm_calendar.ts_end + 600) 
                        AND d.device = '$device'
                    WHERE 
                        esm_calendar.dt = CURDATE()
                    GROUP BY 
                        esm_calendar.dt
                ) today
                JOIN (
                    SELECT AVG(l.value) AS value
                    FROM (
                        SELECT 
                            SUM(activePositiveConsumption) AS value
                        FROM esm_calendar
                        LEFT JOIN esm_leituras_ancar_energia d ON 
                            d.timestamp > (esm_calendar.ts_start) AND 
                            d.timestamp <= (esm_calendar.ts_end + 600) 
                            AND d.device = '$device'
                        WHERE 
                            esm_calendar.dt BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() - INTERVAL 1 DAY
                        GROUP BY 
                            esm_calendar.dt
                    ) l
                ) last
            ");

            return ($result->getNumRows()) ? $result->getRow() : false;

        } else if ($aid == 2) {

            $previous = $this->db->query("
                SELECT 
                    SUM(activePositiveConsumption) AS value,
                    esm_unidades_config.alerta_consumo,
                    esm_unidades_config.unidade_id
                FROM esm_leituras_ancar_energia
                JOIN esm_medidores ON esm_medidores.nome = esm_leituras_ancar_energia.device
                JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id
                WHERE 
                    timestamp > UNIX_TIMESTAMP(DATE_FORMAT(CURDATE() - INTERVAL 1 MONTH,'%Y-%m-01 00:00:00')) AND
                    timestamp <= UNIX_TIMESTAMP(DATE_FORMAT(LAST_DAY(CURDATE()- INTERVAL 1 MONTH),'%Y-%m-31 23:59:59')) + 1 AND 
                    esm_leituras_ancar_energia.device = '$device'
            ");

            $current = $this->db->query("
                SELECT 
                    SUM(activePositiveConsumption) / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01'))) * DAY(LAST_DAY(CURDATE() - INTERVAL 1 DAY )) AS value
                FROM 
                    esm_leituras_ancar_energia
                WHERE 
                    timestamp > UNIX_TIMESTAMP(DATE_FORMAT(CURDATE() ,'%Y-%m-01 00:00:00')) AND timestamp <= UNIX_TIMESTAMP(DATE_FORMAT(CURDATE() - INTERVAL 1 DAY ,'%Y-%m-%d 23:59:59'))
                    AND device = '$device'
            ");

            return array("previous" => max(round($previous->getRow()->value), round($previous->getRow()->alerta_consumo)), "current" => round($current->getRow()->value), "unidade_id" => $previous->getRow()->unidade_id);

        } else if ($aid == 4) {

            $result = $this->db->query("
                SELECT 
                    esm_leituras_ancar_energia.device,
                    esm_unidades.nome,
                    FROM_UNIXTIME(timestamp),
                    esm_leituras_ancar_energia.currentA,
                    esm_leituras_ancar_energia.currentB,
                    esm_leituras_ancar_energia.currentC,
                    esm_unidades_config.disjuntor
                FROM esm_leituras_ancar_energia
                JOIN esm_medidores ON esm_medidores.nome = esm_leituras_ancar_energia.device
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id
                JOIN esm_alertas_cfg ON esm_alertas_cfg.device = esm_leituras_ancar_energia.device
                WHERE 
                    (esm_leituras_ancar_energia.currentA > esm_unidades_config.disjuntor OR
                    esm_leituras_ancar_energia.currentB > esm_unidades_config.disjuntor OR
                    esm_leituras_ancar_energia.currentC > esm_unidades_config.disjuntor) AND
                    timestamp > UNIX_TIMESTAMP() - 600
            ");

            if ($result->getNumRows()) {
                return $result->getResult();
            }
        }

        return false;
    }

    public function GetMonthByStation($st)
    {
        $station = "";
        if (count($st)) {
            if ($st[0] == 'fora') {
                $station = " AND (((MOD((d.timestamp), 86400) < $st[1] OR MOD((d.timestamp), 86400) > $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7) OR esm_calendar.dw = 1 OR esm_calendar.dw = 7)";
            } else if ($st[0] == 'ponta') {
                $station = "AND ((MOD((d.timestamp), 86400) >= $st[1] AND MOD((d.timestamp), 86400) <= $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)";
            } else if ($st[0] == 'open') {
                $station = "AND (MOD((d.timestamp), 86400) >= $st[1] AND MOD((d.timestamp), 86400) <= $st[2])";
            } else if ($st[0] == 'close') {
                $station = "AND (MOD((d.timestamp), 86400) < $st[1] OR MOD((d.timestamp), 86400) > $st[2])";
            }
        }

        $result = $this->db->query("
            SELECT 
                SUM(activePositiveConsumption) AS value
            FROM esm_calendar
            LEFT JOIN esm_leituras_ancar_energia d ON 
                (d.timestamp) > (esm_calendar.ts_start) AND 
                (d.timestamp) <= (esm_calendar.ts_end + 600) 
                $station
            JOIN esm_medidores ON esm_medidores.nome = d.device AND esm_medidores.entrada_id = 72
            WHERE 
                esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
        ");

        if ($result->getNumRows()) {
            return $result->getRow()->value;
        }

        return false;
    }

    public function VerifyCompetencia($entrada_id, $competencia)
    {
        $result = $this->db->query("
            SELECT
                id
            FROM 
                esm_fechamentos_energia
            WHERE
                entrada_id = $entrada_id AND competencia = '$competencia'
            LIMIT 1
        ");

        return ($result->getNumRows());
    }

    private function GetLastFechamento($entrada_id)
    {
        $result = $this->db->query("
            SELECT
                id
            FROM 
                esm_fechamentos_energia
            WHERE
                entrada_id = $entrada_id
            ORDER BY
                inicio DESC
            LIMIT 1
        ");

        if ($result->getNumRows()) {
            return $result->getRow()->id;
        }

        return 0;
    }

    public function GetLancamento($fid)
    {
        $result = $this->db->query("
            SELECT
                esm_blocos.nome,
                esm_fechamentos_energia.*
            FROM 
                esm_fechamentos_energia
            JOIN 
                esm_blocos ON esm_blocos.id = esm_fechamentos_energia.group_id
            WHERE
                esm_fechamentos_energia.id = $fid
        ");

        if ($result->getNumRows()) {
            return $result->getRow();
        }

        return 0;
    }

    public function GetLancamentos($gid)
    {
        $result = $this->db->query("
            SELECT
                competencia,
                FROM_UNIXTIME(inicio, '%d/%m/%Y') AS inicio,
                FROM_UNIXTIME(fim, '%d/%m/%Y') AS fim,
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
                esm_blocos ON esm_blocos.id = esm_fechamentos_energia.group_id AND esm_blocos.id = $gid
            ORDER BY cadastro DESC
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function GetFechamentoUnidades($fid, $config, $split)
    {
        $type = "";
        if ($config->split_report) {
            $type = "AND esm_fechamentos_energia_entradas.type = $split";
        }

        $result = $this->db->query("
            SELECT 
                esm_unidades.nome,
                esm_unidades_config.luc as luc,
                LPAD(ROUND(leitura_anterior), 6, '0') AS leitura_anterior,
                LPAD(ROUND(leitura_atual), 6, '0') AS leitura_atual,
                FORMAT(consumo, 3, 'de_DE') AS consumo,
                FORMAT(consumo_p, 3, 'de_DE') AS consumo_p,
                FORMAT(consumo_f, 3, 'de_DE') AS consumo_f,
                FORMAT(demanda, 3, 'de_DE') AS demanda,
                FORMAT(demanda_p, 3, 'de_DE') AS demanda_p,
                FORMAT(demanda_f, 3, 'de_DE') AS demanda_f
            FROM 
                esm_fechamentos_energia_entradas
            JOIN 
                esm_medidores ON esm_medidores.nome = esm_fechamentos_energia_entradas.device
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN
                esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            WHERE 
                esm_fechamentos_energia_entradas.fechamento_id = $fid 
                $type   
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    private function CalculateQuery($data, $inicio, $fim, $config, $type)
    {
        $query = $this->db->query("
            SELECT
                {$data['id']} AS fechamento_id,
                esm_medidores.nome AS device, 
                $type AS type,
                a.leitura_anterior,
                a.leitura_atual,
                a.consumo,
                p.consumo_p,
                f.consumo_f,
                a.demanda,
                p.demanda_p,
                f.demanda_f
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id AND esm_unidades_config.type = $type
            LEFT JOIN (
                SELECT 
                    device,
                    MIN(activePositive) AS leitura_anterior,
                    MAX(activePositive) AS leitura_atual,
                    MAX(activePositive) - MIN(activePositive) AS consumo,
                    MAX(activeDemand) AS demanda
                FROM esm_leituras_ancar_energia
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                GROUP BY device
            ) a ON a.device = esm_medidores.nome
            JOIN (  
                SELECT 
                    d.device,
                    SUM(activePositiveConsumption) AS consumo_p,
                    MAX(activeDemand) AS demanda_p
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    AND ((MOD((d.timestamp), 86400) >= 73800 AND MOD((d.timestamp), 86400) <= 84600) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)
                WHERE 
                    esm_calendar.dt >= '$inicio' AND 
                    esm_calendar.dt < '$fim'
                GROUP BY device
            ) p ON p.device = esm_medidores.nome
            JOIN (  
                SELECT 
                    d.device,
                    SUM(activePositiveConsumption) AS consumo_f,
                    MAX(activeDemand) AS demanda_f
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    AND (((MOD((d.timestamp), 86400) < 73800 OR MOD((d.timestamp), 86400) > 84600) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7) OR esm_calendar.dw = 1 OR esm_calendar.dw = 7)
                WHERE 
                    esm_calendar.dt >= '$inicio' AND 
                    esm_calendar.dt < '$fim'
                GROUP BY device
            ) f ON f.device = esm_medidores.nome
            WHERE 
                entrada_id = {$data['entrada_id']}
        ");
        return $query;
    }

    public function Calculate($data, $config)
    {
        $inicio = date_create_from_format('d/m/Y', $data["inicio"])->format('Y-m-d');
        $fim = date_create_from_format('d/m/Y', $data["fim"])->format('Y-m-d');

        $data["inicio"] = date_create_from_format('d/m/Y H:i', $data["inicio"] . ' 00:00')->format('U');
        $data["fim"] = date_create_from_format('d/m/Y H:i', $data["fim"] . ' 00:00')->format('U');

        // inicia transação
        $failure = array();
        $this->db->trans_start();

        // insere novo registro
        if (!$this->db->insert('esm_fechamentos_energia', $data)) {
            // se erro, salva info do erro
            $failure[] = $this->db->error();
        }

        // retorna fechamento id
        $data['id'] = $this->db->insert_id();

        $query = $this->CalculateQuery($data, $inicio, $fim, $config, 1);

        $comum = $query->getResult();
        $consumo_c = 0;
        $consumo_c_f = 0;
        $consumo_c_p = 0;
        $demanda_c_f = 0;
        $demanda_c_p = 0;

        foreach ($comum as $c) {
            $consumo_c += $c->consumo;
            $consumo_c_p += $c->consumo_p;
            $consumo_c_f += $c->consumo_f;
            $demanda_c_f = ($c->demanda_f > $demanda_c_f) ? $c->demanda_f : $demanda_c_f;
            $demanda_c_p = ($c->demanda_p > $demanda_c_p) ? $c->demanda_p : $demanda_c_p;
        }

        $query = $this->CalculateQuery($data, $inicio, $fim, $config, 2);

        $unidades = $query->getResult();
        $consumo_u = 0;
        $consumo_u_p = 0;
        $consumo_u_f = 0;
        $demanda_u_f = 0;
        $demanda_u_p = 0;

        foreach ($unidades as $u) {
            $consumo_u += $u->consumo;
            $consumo_u_p += $u->consumo_p;
            $consumo_u_f += $u->consumo_f;
            $demanda_u_f = ($u->demanda_f > $demanda_u_f) ? $u->demanda_f : $demanda_u_f;
            $demanda_u_p = ($u->demanda_p > $demanda_u_p) ? $u->demanda_p : $demanda_u_p;
        }

        // inclui dados na tabela esm_fechamentos_entradas
        if (!$this->db->insert_batch('esm_fechamentos_energia_entradas', $comum)) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->insert_batch('esm_fechamentos_energia_entradas', $unidades)) {
            $failure[] = $this->db->error();
        }

        // atualiza tabela esm_fechamento com dados da área comum
        if (!$this->db->update('esm_fechamentos_energia', array(
            'consumo' => $consumo_c,
            'consumo_p' => $consumo_c_p,
            'consumo_f' => $consumo_c_f,
            'demanda' => ($demanda_c_p > $demanda_c_f) ? $demanda_c_p : $demanda_c_f,
            'demanda_p' => $demanda_c_p,
            'demanda_f' => $demanda_c_f,
            'consumo_u' => $consumo_u,
            'consumo_u_p' => $consumo_u_p,
            'consumo_u_f' => $consumo_u_f,
            'demanda_u' => ($demanda_u_p > $demanda_u_f) ? $demanda_u_p : $demanda_u_f,
            'demanda_u_p' => $demanda_u_p,
            'demanda_u_f' => $demanda_u_f
        ), array('id' => $data['id']))) {

            $failure[] = $this->db->error();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return json_encode(array("status" => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status" => "success", "message" => "Lançamento calculado com sucesso!", "id" => $data['id']));
        }
    }

    /*
    public function Calculate($data, $config)
    {
        $inicio = date_create_from_format('d/m/Y', $data["inicio"])->format('Y-m-d');
        $fim    = date_create_from_format('d/m/Y', $data["fim"])->format('Y-m-d');

        $data["inicio"] = date_create_from_format('d/m/Y H:i', $data["inicio"] . ' 00:00')->format('U');
        $data["fim"]    = date_create_from_format('d/m/Y H:i', $data["fim"] . ' 00:00')->format('U');

        // inicia transação
        $failure = array();
        $this->db->trans_start();

        // insere novo registro
        if (!$this->db->insert('esm_fechamentos_energia', $data)) {
            // se erro, salva info do erro
            $failure[] = $this->db->error();
        }

        // get fechamento anterior id

        // retorna fechamento id
        $data['id'] = $this->db->insert_id();

        $query = $this->CalculateQuery($data, $inicio, $fim, $config, "fracionar");

        $unidades         = $query->getResult();
        $fracao_consumo   = 0;
        $fracao_consumo_f = 0;
        $fracao_consumo_p = 0;

        foreach ($unidades as $u) {
            $fracao_consumo   += $u->consumo;
            $fracao_consumo_p += $u->consumo_p;
            $fracao_consumo_f += $u->consumo_f;
        }

        $query = $this->CalculateQuery($data, $inicio, $fim, $config, "incluir");

        // verifica se retornou algo
        if ($query->getNumRows() == 0) {
            $failure[] = array('code' => 0, 'message' => 'Nenhuma leitura encontrada no período.');
        }

        $leituras  = $query->getResult();
        $consumo   = 0;
        $consumo_p = 0;
        $consumo_f = 0;
        $demanda_f = 0;
        $demanda_p = 0;
        $f_consumo = 0;

        foreach ($leituras as $u) {
            $consumo   += $u->consumo;
            $consumo_p += $u->consumo_p;
            $consumo_f += $u->consumo_f;
            $demanda_f  = ($u->demanda_f > $demanda_f) ? $u->demanda_f : $demanda_f;
            $demanda_p  = ($u->demanda_p > $demanda_p) ? $u->demanda_p : $demanda_p;

            $u->fracao_consumo = $fracao_consumo / count($leituras);
        }

        // inclui dados na tabela esm_fechamentos_entradas
        if (!$this->db->insert_batch('esm_fechamentos_energia_entradas', $leituras)) {
            $failure[] = $this->db->error();
        }

        // atualiza tabela esm_fechamento com dados da área comum
        if (!$this->db->update('esm_fechamentos_energia', array(
                'consumo'          => $consumo,
                'consumo_p'        => $consumo_p,
                'consumo_f'        => $consumo_f,
                'demanda'          => ($demanda_p > $demanda_f) ? $demanda_p : $demanda_f,
                'demanda_p'        => $demanda_p,
                'demanda_f'        => $demanda_f,
                'fracao_consumo'   => $fracao_consumo,
                'fracao_consumo_p' => $fracao_consumo_p,
                'fracao_consumo_f' => $fracao_consumo_f

            ), array('id' => $data['id']))) {

            $failure[] = $this->db->error();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "message" => "Fechamento calculado com sucesso!", "id" => $data['id']));
        }
    }
*/
    public function get_device_value($device)
    {
        $result = $this->db->query("
            SELECT 
                ultima_leitura AS value
            FROM esm_medidores
            WHERE 
                nome = '$device'
        ");

        if ($result->getNumRows()) {
            return $result->getRow()->value;
        }

        return false;
    }

    // **
    // Exclui Fechamento. Origem modal em painel/gestao
    // [in] id
    // [out] Json com status da operação
    // **
    public function DeleteLancamento($id)
    {
        if (!$this->db->delete('esm_fechamentos_energia', array('id' => $id))) {
            echo json_encode(array("status" => "error", "message" => $this->db->error()));
        } else {
            echo json_encode(array("status" => "success", "message" => "Lançamento excluído com sucesso"));
        }
    }

    public function GetResume($config, $split)
    {
        $type = "";
        if ($config->split_report) {
            $type = "AND esm_unidades_config.type = $split";
        }

        $result = $this->db->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades_config.luc AS luc, 
                esm_unidades.nome AS name, 
                LPAD(ROUND(esm_medidores.ultima_leitura, 0), 6, '0') AS value_read,
                FORMAT(m.value, 3, 'de_DE') AS value_month,
                FORMAT(h.value, 3, 'de_DE') AS value_month_open,
                FORMAT(m.value - h.value, 3, 'de_DE') AS value_month_closed,
                FORMAT(p.value, 3, 'de_DE') AS value_ponta,
                FORMAT(m.value - p.value, 3, 'de_DE') AS value_fora,
                FORMAT(l.value, 3, 'de_DE') AS value_last,
                FORMAT(m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())), 3, 'de_DE') AS value_future
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id $type
            LEFT JOIN (  
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_ancar_energia
                    WHERE 
                        timestamp > UNIX_TIMESTAMP() - 86400
                    GROUP BY device
                ) l ON l.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        d.device,
                        SUM(activePositiveConsumption) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_ancar_energia d ON 
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
                        esm_leituras_ancar_energia
                    WHERE 
                        MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                    GROUP BY device                
                ) c ON c.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_ancar_energia
                    WHERE 
                        MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now())
                        AND (MOD((timestamp), 86400) >= {$config->open} AND MOD((timestamp), 86400) <= {$config->close})
                    GROUP BY device
                ) h ON h.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        d.device,
                        SUM(activePositiveConsumption) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_ancar_energia d ON 
                        (d.timestamp) > (esm_calendar.ts_start) AND 
                        (d.timestamp) <= (esm_calendar.ts_end + 600) 
                        AND ((MOD((d.timestamp), 86400) >= {$config->ponta_start} AND MOD((d.timestamp), 86400) <= {$config->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)
                    WHERE 
                        esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                        esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.device
                ) p ON p.device = esm_medidores.nome
            WHERE 
                entrada_id = 72
            ORDER BY 
                esm_unidades.nome
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function GetAbnormal($device, $init, $end, $type, $min, $max)
    {
        if (!is_null($min))
            $min = floatval(str_replace(array('.', ','), array('', '.'), $min));
        if (!is_null($max))
            $max = floatval(str_replace(array('.', ','), array('', '.'), $max));

        $abnormal = "";
        if (!is_null($type)) {
            if ($type == "activePositiveConsumption")
                $abnormal = "AND (activePositiveConsumption < $min OR activePositiveConsumption > $max)";
            else
                $abnormal = "AND ({$type}A < $min OR {$type}A > $max OR {$type}B < $min OR {$type}B > $max OR {$type}C < $min OR {$type}C > $max)";
        }

        // realiza a query via dt
        $result = $this->db->query("
            SELECT
                DATE_FORMAT(FROM_UNIXTIME(timestamp) ,'%d/%m%Y %H:%i') AS date,
                FORMAT(voltageA, 3, 'de_DE') AS voltageA,
				FORMAT(voltageB, 3, 'de_DE') AS voltageB,
				FORMAT(voltageC, 3, 'de_DE') AS voltageC,
				FORMAT(currentA, 3, 'de_DE') AS currentA,
				FORMAT(currentB, 3, 'de_DE') AS currentB,
				FORMAT(currentC, 3, 'de_DE') AS currentC,
                FORMAT(activeA, 3, 'de_DE') AS activeA,
                FORMAT(activeB, 3, 'de_DE') AS activeB,
                FORMAT(activeC, 3, 'de_DE') AS activeC,
                FORMAT(reactiveA, 3, 'de_DE') AS reactiveA,
                FORMAT(reactiveB, 3, 'de_DE') AS reactiveB,
                FORMAT(reactiveC, 3, 'de_DE') AS reactiveC,
                FORMAT(activePositiveConsumption, 3, 'de_DE') AS activePositiveConsumption
            FROM
                esm_leituras_ancar_energia
            WHERE
                timestamp >= UNIX_TIMESTAMP('$init 00:00:00') AND 
                timestamp <= UNIX_TIMESTAMP('$end 23:59:59') AND 
                device = '$device'
                $abnormal
            ORDER BY timestamp
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function GetUserAlert($id, $readed = false)
    {
        $query = $this->db->query("
            SELECT 
                esm_alertas_energia.tipo, 
                esm_alertas_energia.titulo, 
                esm_alertas_energia.texto, 
                COALESCE(esm_alertas_energia.enviada, 0) AS enviada,
                COALESCE(esm_alertas_energia_envios.lida, '') AS lida
            FROM esm_alertas_energia_envios
            JOIN esm_alertas_energia ON esm_alertas_energia.id = esm_alertas_energia_envios.alerta_id
            WHERE esm_alertas_energia_envios.id = $id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        $ret = $query->getRow();

        if ($readed) {
            // atualiza esm_alertas
            $this->db->where('id', $id);
            $this->db->where('lida', NULL);
            $this->db->update('esm_alertas_energia_envios', array('lida' => date("Y-m-d H:i:s")));
        }

        return $ret;
    }

    // **
    // Exclui alerta. Origem modal admin/confirm -> alertas
    // [in] id
    // [out] Json com status da operação
    // **
    public function DeleteAlert($id)
    {
        if (!$this->db->update('esm_alertas_energia_envios', array('visibility' => 'delbyuser'), array('id' => $id))) {
            echo json_encode(array("status" => "error", "message" => $this->db->error()));
            return;
        }

        echo json_encode(array("status" => "success", "message" => "Alerta excluído com sucesso.", "id" => $id));
    }

    public function CountAlerts($uid)
    {
        $query = $this->db->query("
            SELECT 
                COUNT(*) AS count
            FROM 
                esm_alertas_energia_envios
            WHERE 
                esm_alertas_energia_envios.user_id = $uid AND
                ISNULL(lida) AND
                visibility = 'normal'
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return 0;

        return $query->getRow()->count;
    }

    public function ReadAllAlert($user_id)
    {
        // atualiza data
        if (!$this->db->query("
                UPDATE esm_alertas_energia_envios 
                SET lida = '" . date('Y-m-d H:i:s') . "' 
                WHERE user_id = $user_id AND ISNULL(lida)
            ")) {

            echo json_encode(array("status" => "error", "message" => $this->db->error()));
        } else {
            echo json_encode(array("status" => "success", "message" => "Alertas marcados com sucesso."));
        }
    }

    private function GetUserIdByDevice($device)
    {
        $result = $this->db->query("
            SELECT auth_users.id
            FROM auth_users
            JOIN auth_users_unidades ON auth_users_unidades.user_id = auth_users.id
            JOIN esm_medidores ON esm_medidores.unidade_id = auth_users_unidades.unidade_id
            WHERE esm_medidores.nome = '$device'        
        ");

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    private function GetGroupUserIdByDevice($device)
    {
        $result = $this->db->query("
            SELECT auth_users_group.user_id AS id
            FROM auth_users_group
            WHERE group_id = (
                SELECT esm_unidades.bloco_id
                FROM esm_medidores
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE esm_medidores.nome = '$device'
            )
        ");

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function AddAlerts($data, $cfg, $set = array())
    {
        foreach ($data as $d) {

            $this->db->trans_start();

            // insere alerta
            $this->db->insert('esm_alertas_energia', $d);

            $id = $this->db->insert_id();

            // envia para ancar
            $this->db->insert('esm_alertas_energia_envios', array("user_id" => 538, "alerta_id" => $id));

            // envia para shopping
            if ($cfg->notify_shopping) {

                $group = $this->GetGroupUserIdByDevice($d["device"]);

                if ($group) {
                    foreach ($group as $g) {
                        $this->db->insert('esm_alertas_energia_envios', array("user_id" => $g->id, "alerta_id" => $id));
                    }
                }
            }

            // envia para lojas
            if ($cfg->notify_unity) {
                $users = $this->GetUserIdByDevice($d["device"]);
                if ($users) {
                    foreach ($users as $u) {
                        $this->db->insert('esm_alertas_energia_envios', array("user_id" => $u->id, "alerta_id" => $id));
                    }
                }
            }

            // atualiza dados
            if ($cfg->type == 2) {
                $this->db->update('esm_unidades_config', array('alerta_consumo' => $set["current"]), array('unidade_id' => $set["unidade_id"]));
            }

            $this->db->trans_complete();
        }
    }
}