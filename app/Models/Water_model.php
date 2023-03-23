<?php

namespace App\Models;

class Water_model extends Base_model
{

    public function GetConsumption($device, $start, $end, $st = array(), $group = true, $interval = null)
    {
        $dvc  = "";
        $dvc1 = "";
        if (is_numeric($device)) {

            $dvc = "JOIN esm_medidores on esm_medidores.id = esm_leituras_ancar_agua.medidor_id AND esm_medidores.nome IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";

        } else if ($device == "C") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                    LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 1";

        } else if ($device == "U") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                    LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 2";

        } else if ($device == "T") {

        } else if ($device == "ALL") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                    LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id";

        }  else {

            $dvc1 = "JOIN esm_medidores ON esm_medidores.nome = '$device'";
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

        if ($interval === 'h') {
            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    SUM(consumo) AS value
                FROM esm_hours
                $dvc1
                JOIN esm_leituras_ancar_agua ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > $start AND 
                    timestamp <= $end + 600
                    $station
                    $dvc
                $group_by
                ORDER BY esm_hours.num
            ");

        } elseif ($start == $end) {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    SUM(consumo) AS value
                FROM esm_hours
                $dvc1
                JOIN esm_leituras_ancar_agua ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    timestamp <= UNIX_TIMESTAMP('$end 23:59:59') + 600
                    $station
                    $dvc
                $group_by
                ORDER BY esm_hours.num
            ");

        } else {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_calendar.dt";
            
            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    SUM(consumo) AS value
                FROM esm_calendar
                $dvc1
                JOIN esm_leituras_ancar_agua ON 
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

    public function GetResume($config, $type)
    {
        $result = $this->db->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades_config.luc AS luc, 
                esm_unidades.nome AS name, 
                LPAD(ROUND(esm_medidores.ultima_leitura, 0), 6, '0') AS value_read,
                FORMAT(m.value, 0, 'de_DE') AS value_month,
                FORMAT(h.value, 0, 'de_DE') AS value_month_open,
                FORMAT(m.value - h.value, 0, 'de_DE') AS value_month_closed,
                FORMAT(l.value, 0, 'de_DE') AS value_last,
                FORMAT(m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())), 0, 'de_DE') AS value_future
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
                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now()) AND
                    HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$config->open})) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$config->close}))
                GROUP BY medidor_id
            ) h ON h.device = esm_medidores.nome
            WHERE 
                entrada_id = 73 AND esm_unidades_config.type = $type
            ORDER BY 
                esm_unidades_config.type, esm_unidades.nome
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
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
                WHERE esm_medidores.tipo = 'agua'";
        } else if ($device == "U") {
            $query = "
                SELECT SUM(ultima_leitura) AS value
                FROM esm_medidores
                JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 2
                WHERE esm_medidores.tipo = 'agua'";
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
                WHERE esm_unidades.bloco_id = $gid AND esm_medidores.tipo = 'agua'";
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

    public function VerifyCompetencia($entrada_id, $competencia)
    {
        $result = $this->db->query("
            SELECT
                id
            FROM 
                esm_fechamentos_agua
            WHERE
                entrada_id = $entrada_id AND competencia = '$competencia'
            LIMIT 1
        ");

        return ($result->getNumRows());
    }

    private function CalculateQuery($data, $inicio, $fim, $type, $config)
    {
        $query = $this->db->query("
            SELECT
                {$data['id']} AS fechamento_id,
                esm_medidores.nome AS device,
                esm_unidades_config.type AS type,
                a.leitura_anterior,
                a.leitura_atual,
                a.consumo,
                o.consumo AS consumo_o,
                c.consumo AS consumo_c
            FROM esm_medidores
            LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id AND esm_unidades_config.type = $type
            LEFT JOIN (
                SELECT 
                    medidor_id,
                    MIN(leitura) AS leitura_anterior,
                    MAX(leitura) AS leitura_atual,
                    MAX(leitura) - MIN(leitura) AS consumo
                FROM esm_leituras_ancar_agua
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                GROUP BY medidor_id
            ) a ON a.medidor_id = esm_medidores.id
            LEFT JOIN (
                SELECT 
                    medidor_id,
                    SUM(consumo) AS consumo
                FROM esm_leituras_ancar_agua
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                    AND HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$config->open})) AND HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$config->close}))
                GROUP BY medidor_id
            ) o ON o.medidor_id = esm_medidores.id
            LEFT JOIN (
                SELECT 
                    medidor_id,
                    SUM(consumo) AS consumo
                FROM esm_leituras_ancar_agua
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
					AND (HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$config->open})) OR HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$config->close})))
                GROUP BY medidor_id
            ) c ON c.medidor_id = esm_medidores.id
            WHERE 
                esm_medidores.entrada_id = {$data['entrada_id']}
        ");

        return $query;
    }

    public function Calculate($data, $config)
    {
        $inicio = date_create_from_format('d/m/Y', $data["inicio"])->format('Y-m-d');
        $fim    = date_create_from_format('d/m/Y', $data["fim"])->format('Y-m-d');

        $data["inicio"] = date_create_from_format('d/m/Y H:i', $data["inicio"] . ' 00:00')->format('U');
        $data["fim"]    = date_create_from_format('d/m/Y H:i', $data["fim"] . ' 00:00')->format('U');

        // inicia transação
        $failure = array();
        $this->db->transStart();

        // insere novo registro
        if (!$this->db->table('esm_fechamentos_agua')->insert($data)) {
            // se erro, salva info do erro
            $failure[] = $this->db->error();
        }

        // retorna fechamento id
        $data['id'] = $this->db->insertID();

        $query = $this->CalculateQuery($data, $inicio, $fim, 1, $config);

        $comum       = $query->getResult();
        $consumo_c   = 0; 
        $consumo_c_c = 0; 
        $consumo_c_o = 0; 

        foreach ($comum as $c) {
            $consumo_c   += $c->consumo;
            $consumo_c_c += $c->consumo_c;
            $consumo_c_o += $c->consumo_o;
        }

        $query = $this->CalculateQuery($data, $inicio, $fim, 2, $config);

        $unidades    = $query->getResult();
        $consumo_u   = 0;
        $consumo_u_c = 0; 
        $consumo_u_o = 0; 

        foreach ($unidades as $u) {
            $consumo_u   += $u->consumo;
            $consumo_u_c += $u->consumo_c;
            $consumo_u_o += $u->consumo_o;
        }

        // inclui dados na tabela esm_fechamentos_entradas
        if (!$this->db->table('esm_fechamentos_agua_entradas')->insertBatch($comum)) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->table('esm_fechamentos_agua_entradas')->insertBatch($unidades)) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->table('esm_fechamentos_agua')->set(array(
            'consumo_c'        => $consumo_c,
            'consumo_u'        => $consumo_u,
            'consumo_c_c'      => $consumo_c_c,
            'consumo_c_o'      => $consumo_c_o,
            'consumo_u_c'      => $consumo_u_c,
            'consumo_u_o'      => $consumo_u_o,
        ))->where(array('id' => $data['id']))->update()) {
            $failure[] = $this->db->error();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "message" => "Lançamento calculado com sucesso!", "id" => $data['id']));
        }
    }

    public function GetLancamento($fid)
    {
        $result = $this->db->query("
            SELECT
                esm_blocos.nome,
                esm_unidades_config.luc as luc,
                esm_fechamentos_agua.*
            FROM 
                esm_fechamentos_agua
            JOIN 
                esm_blocos ON esm_blocos.id = esm_fechamentos_agua.group_id
            JOIN 
                esm_unidades ON esm_blocos.id = esm_unidades.bloco_id
            JOIN
                esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            WHERE
                esm_fechamentos_agua.id = $fid
        ");

        if ($result->getNumRows()) {
            return $result->getRow();
        }

        return 0;
    }

    public function GetLancamentoUnidades($fid, $config, $split)
    {
        $type = "";
//        if ($config->split_report) {
//            $type = "AND esm_fechamentos_agua_entradas.type = $split";
//        }

        $result = $this->db->query("
            SELECT 
                esm_unidades.nome,
                esm_unidades_config.luc as luc,
                LPAD(ROUND(leitura_anterior), 6, '0') AS leitura_anterior,
                LPAD(ROUND(leitura_atual), 6, '0') AS leitura_atual,
                FORMAT(consumo, 1, 'de_DE') AS consumo
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
                $type   
            ORDER BY esm_unidades.nome
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function GetLancamentos($gid)
    {
        $result = $this->db->query("
            SELECT
                competencia,
                FROM_UNIXTIME(inicio, '%d/%m/%Y') AS inicio,
                FROM_UNIXTIME(fim, '%d/%m/%Y') AS fim,
                FORMAT(consumo_c + consumo_u, 1, 'de_DE') AS consumo,
                FORMAT(consumo_c_o + consumo_u_o, 1, 'de_DE') AS consumo_o,
                FORMAT(consumo_c_c + consumo_u_c, 1, 'de_DE') AS consumo_c,
                DATE_FORMAT(cadastro, '%d/%m/%Y') AS emissao
            FROM 
                esm_fechamentos_agua
            WHERE
                group_id = $gid
            ORDER BY cadastro DESC
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function DeleteLancamento($id)
    {
        if (!$this->db->table('esm_fechamentos_agua')->where(array('id' => $id))->delete()) {
            echo json_encode(array("status"  => "error", "message" => $this->db->error()));
        } else {
            echo json_encode(array("status"  => "success", "message" => "Lançamento excluído com sucesso"));
        }
    }
}
