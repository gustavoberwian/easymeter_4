<?php

namespace App\Models;

class Gas_model extends Base_model
{
    public function GetConsumption($device, $start, $end, $st = array(), $group = true, $interval = null)
    {
        $dvc1 = "LEFT JOIN esm_medidores ON esm_medidores.id = '$device'";
        $dvc = "AND medidor_id = esm_medidores.id";

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

            $query = "
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    SUM(consumo) AS value
                FROM esm_hours
                $dvc1
                LEFT JOIN esm_leituras_bancada_gas ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > $start AND 
                    timestamp <= $end + 600
                    $station
                    $dvc
                $group_by
                ORDER BY esm_hours.num
            ";

        } elseif ($start == $end) {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $query = "
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    SUM(consumo) AS value
                FROM esm_hours
                $dvc1
                LEFT JOIN esm_leituras_bancada_gas ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    timestamp <= UNIX_TIMESTAMP('$end 23:59:59') + 600
                    $station
                    $dvc
                $group_by
                ORDER BY esm_hours.num
            ";

        } else {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_calendar.dt";

            $query = "
                SELECT 
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    SUM(consumo) AS value
                FROM esm_calendar
                $dvc1
                LEFT JOIN esm_leituras_bancada_gas ON 
                    timestamp > esm_calendar.ts_start AND 
                    timestamp <= (esm_calendar.ts_end + 600)
                    $station
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                $group_by
                ORDER BY esm_calendar.dt
            ";
        }

        $result = $this->db->query($query);

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
                WHERE esm_unidades.agrupamento_id = $gid AND esm_medidores.tipo = 'gas'";
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

        $tabela = "esm_leituras_bancada_gas";

        $values = "LPAD(ROUND(esm_medidores.ultima_leitura, 0), 6, '0') AS value_read,
                FORMAT(m.value, 0, 'de_DE') AS value_month,
                FORMAT(h.value, 0, 'de_DE') AS value_month_open,
                FORMAT(m.value - h.value, 0, 'de_DE') AS value_month_closed,
                FORMAT(l.value, 0, 'de_DE') AS value_last,
                FORMAT(m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())), 0, 'de_DE') AS value_future";

        if ($demo) {

            $tabela = "esm_leituras_bancada_gas_demo";

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
                esm_unidades.agrupamento_id = $group AND
                esm_medidores.tipo = 'gas'
            ORDER BY 
                esm_unidades_config.type, esm_unidades.nome
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function delete_fechamento($id)
    {
        $failure = array();

        $this->db->transStart();

        if (!$this->db->table('esm_fechamentos_gas_entradas')->where(array('fechamento_id' => $id))->delete()) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->table('esm_fechamentos_gas')->where(array('id' => $id))->delete()) {
            $failure[] = $this->db->error();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return json_encode(array("status" => "error", "message" => $failure));
        } else {
            return json_encode(array("status" => "success", "message" => "Lançamento excluído com sucesso"));
        }
    }

    public function get_fechamento($fid)
    {
        $result = $this->db->query("
            SELECT
                esm_entidades.nome,
                esm_fechamentos_gas.*
            FROM esm_fechamentos_gas
            JOIN esm_entidades ON esm_entidades.id = esm_fechamentos_gas.entidade_id
            JOIN esm_agrupamentos ON esm_entidades.id = esm_agrupamentos.entidade_id
            JOIN esm_unidades ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            WHERE esm_fechamentos_gas.id = $fid
        ");

        if ($result->getNumRows()) {
            return $result->getRow();
        }

        return 0;
    }

    public function get_fechamento_unidades($fid, $split)
    {
        $type = "";

        $result = $this->db->query("
            SELECT 
                esm_unidades.nome,
                esm_medidores.nome as medidor,
                LPAD(ROUND(esm_fechamentos_gas_entradas.leitura_anterior), 6, '0') AS leitura_anterior,
                LPAD(ROUND(esm_fechamentos_gas_entradas.leitura_atual), 6, '0') AS leitura_atual,
                FORMAT(esm_fechamentos_gas_entradas.leitura_atual - esm_fechamentos_gas_entradas.leitura_anterior, 1, 'de_DE') AS consumo
            FROM 
                esm_fechamentos_gas_entradas
            JOIN 
                esm_medidores ON esm_medidores.id = esm_fechamentos_gas_entradas.medidor_id
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE 
                esm_fechamentos_gas_entradas.fechamento_id = $fid 
                $type   
            ORDER BY esm_unidades.nome
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function get_fechamentos($entidade_id)
    {
        $result = $this->db->query("
            SELECT
                competencia,
                FROM_UNIXTIME(inicio, '%d/%m/%Y') AS inicio,
                FROM_UNIXTIME(fim, '%d/%m/%Y') AS fim,
                FORMAT(leitura_atual - leitura_anterior, 1, 'de_DE') AS consumo,
                DATE_FORMAT(cadastro, '%d/%m/%Y') AS emissao
            FROM 
                esm_fechamentos_gas
            WHERE
                entidade_id = $entidade_id
            ORDER BY cadastro DESC
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function verify_competencia($entidade_id, $ramal_id, $competencia)
    {
        $result = $this->db->query("
            SELECT
                id
            FROM 
                esm_fechamentos_gas
            WHERE
                entidade_id = $entidade_id AND ramal_id = $ramal_id AND competencia = '$competencia'
            LIMIT 1
        ");

        return ($result->getNumRows());
    }

    public function calculate($data, $entidade_id)
    {
        $inicio = date_create_from_format('d/m/Y', $data["inicio"])->format('Y-m-d');
        $fim = date_create_from_format('d/m/Y', $data["fim"])->format('Y-m-d');

        $data["inicio"] = date_create_from_format('d/m/Y H:i', $data["inicio"] . ' 00:00')->format('U');
        $data["fim"] = date_create_from_format('d/m/Y H:i', $data["fim"] . ' 00:00')->format('U');

        // inicia transação
        $failure = array();
        $this->db->transStart();

        if (!$this->db->table('esm_fechamentos_gas')->insert($data)) {
            // insere novo registro
            $failure[] = $this->db->error();
        }

        // retorna fechamento id
        $data['id'] = $this->db->insertID();

        $entity = $this->get_entidade($entidade_id);

        $query = $this->db->query("
            SELECT
                {$data['id']} AS fechamento_id,
                esm_medidores.nome AS device,
                esm_medidores.id AS medidor_id,
                a.leitura_anterior,
                a.leitura_atual,
                a.consumo
            FROM esm_medidores
            LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            LEFT JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            LEFT JOIN (
                SELECT 
                    medidor_id,
                    MIN(leitura) AS leitura_anterior,
                    MAX(leitura) AS leitura_atual,
                    MAX(leitura) - MIN(leitura) AS consumo
                FROM esm_leituras_" . $entity->tabela . "_gas
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                GROUP BY medidor_id
            ) a ON a.medidor_id = esm_medidores.id
            WHERE 
                esm_agrupamentos.entidade_id = {$data['entidade_id']}
        ");

        $unidades = $query->getResult();
        $leitura_anterior = 0;
        $leitura_atual = 0;

        foreach ($unidades as $c) {
            $leitura_anterior += $c->leitura_anterior;
            $leitura_atual += $c->leitura_atual;
        }

        if (!$this->db->table('esm_fechamentos_gas_entradas')->insertBatch($unidades)) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->table('esm_fechamentos_gas')->set(array(
            'leitura_anterior' => $leitura_anterior,
            'leitura_atual' => $leitura_atual,
            'consumo' => $leitura_atual - $leitura_anterior,
        ))->where(array('id' => $data['id']))->update()) {
            $failure[] = $this->db->error();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return json_encode(array("status" => "error", "message" => $failure));
        } else {
            return json_encode(array("status" => "success", "message" => "Lançamento calculado com sucesso!", "id" => $data['id'], "entidade" => $entidade_id),);
        }
    }

    public function calculateGeral($data, $entidades)
    {
        $inicio = date_create_from_format('d/m/Y', $data["inicio"])->format('Y-m-d');
        $fim = date_create_from_format('d/m/Y', $data["fim"])->format('Y-m-d');

        $data["inicio"] = date_create_from_format('d/m/Y H:i', $data["inicio"] . ' 00:00')->format('U');
        $data["fim"] = date_create_from_format('d/m/Y H:i', $data["fim"] . ' 00:00')->format('U');

        // inicia transação
        $failure = array();
        $this->db->transStart();

        foreach ($entidades as $entidade) {

            $data['entidade_id'] = $entidade->id;
            $data['ramal_id'] = $entidade->ramal->id;

            if (!$this->db->table('esm_fechamentos_gas')->insert($data)) {
                // insere novo registro
                $failure[] = $this->db->error();
            }

            // retorna fechamento id
            $data['id'] = $this->db->insertID();

            $query = $this->db->query("
                SELECT
                    {$data['id']} AS fechamento_id,
                    esm_medidores.nome AS device,
                    esm_medidores.id AS medidor_id,
                    a.leitura_anterior,
                    a.leitura_atual,
                    a.consumo
                FROM esm_medidores
                LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                LEFT JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
                LEFT JOIN (
                    SELECT 
                        medidor_id,
                        MIN(leitura) AS leitura_anterior,
                        MAX(leitura) AS leitura_atual,
                        MAX(leitura) - MIN(leitura) AS consumo
                    FROM esm_leituras_" . $entidade->tabela . "_gas
                    WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                    GROUP BY medidor_id
                ) a ON a.medidor_id = esm_medidores.id
                WHERE 
                    esm_agrupamentos.entidade_id = {$data['entidade_id']}
            ");

            $unidades = $query->getResult();
            if (!$unidades) {
                $failure[] = "Não há unidades cadastradas para o cliente $entidade->nome";
                return json_encode(array("status" => "error", "message" => $failure));
            }

            $leitura_anterior = 0;
            $leitura_atual = 0;

            foreach ($unidades as $c) {
                $leitura_anterior += $c->leitura_anterior;
                $leitura_atual += $c->leitura_atual;
            }

            if (!$this->db->table('esm_fechamentos_gas_entradas')->insertBatch($unidades)) {
                $failure[] = $this->db->error();
            }

            if (!$this->db->table('esm_fechamentos_gas')->set(array(
                'leitura_anterior' => $leitura_anterior,
                'leitura_atual' => $leitura_atual,
                'consumo' => $leitura_atual - $leitura_anterior,
            ))->where(array('id' => $data['id']))->update()) {
                $failure[] = $this->db->error();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return json_encode(array("status" => "error", "message" => $failure));
        } else {
            return json_encode(array("status" => "success", "message" => "Fechamentos calculados com sucesso!", "entidades" => $entidades),);
        }
    }

    public function get_battery_consumption($device, $start, $end, $group = null)
    {
        if ($start == $end) {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $query = "
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    esm_leituras_detalhes.bateria,
                    esm_leituras_detalhes.bateria1
                FROM esm_hours
                LEFT JOIN esm_medidores ON esm_medidores.id = '$device'
                LEFT JOIN esm_leituras_detalhes ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    timestamp <= UNIX_TIMESTAMP('$end 23:59:59') + 600 AND 
                       esm_leituras_detalhes.device = esm_medidores.nome
                $group_by
                ORDER BY esm_hours.num
            ";
        } else {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_calendar.dt";

            $query = "
                SELECT
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label,
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    esm_leituras_detalhes.bateria AS bateria1,
                    esm_leituras_detalhes.bateria1 AS bateria2
                FROM esm_calendar
                LEFT JOIN esm_medidores ON esm_medidores.id = '$device'
                LEFT JOIN esm_leituras_detalhes ON
                    timestamp > esm_calendar.ts_start AND
                    timestamp <= (esm_calendar.ts_end + 600) AND
                      esm_leituras_detalhes.device = esm_medidores.nome
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end'
                $group_by
                ORDER BY esm_calendar.dt
            ";
        }

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function get_sensor_consumption($device, $start, $end, $group = null)
    {
        if ($start == $end) {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $query = "
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                    esm_leituras_detalhes.gas
                FROM esm_hours
                LEFT JOIN esm_medidores ON esm_medidores.id = '$device'
                LEFT JOIN esm_leituras_detalhes ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    timestamp <= UNIX_TIMESTAMP('$end 23:59:59') + 600 AND 
                       esm_leituras_detalhes.device = esm_medidores.nome
                $group_by
                ORDER BY esm_hours.num
            ";
        } else {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_calendar.dt";

            $query = "
                SELECT
                    CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label,
                    esm_calendar.dt AS date,
                    esm_calendar.dw AS dw,
                    esm_leituras_detalhes.gas AS value
                FROM esm_calendar
                LEFT JOIN esm_medidores ON esm_medidores.id = '$device'
                LEFT JOIN esm_leituras_detalhes ON
                    timestamp > esm_calendar.ts_start AND
                    timestamp <= (esm_calendar.ts_end + 600) AND
                      esm_leituras_detalhes.device = esm_medidores.nome
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end'
                $group_by
                ORDER BY esm_calendar.dt
            ";
        }

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function get_alertas_by_user($user_id, $tipo = null, $op = null)
    {
        $t = "";
        if (!is_null($tipo)) {
            if ($tipo === 'vazamento')
                $t = " AND esm_alertas.tipo = '$tipo' ";
            elseif ($tipo === 'informativo')
                $t = " AND esm_alertas.tipo = '$tipo' ";
        }

        $query = "SELECT * FROM esm_alertas 
            JOIN esm_alertas_envios ON esm_alertas_envios.alerta_id = esm_alertas.id AND esm_alertas_envios.user_id = $user_id 
            WHERE 1 $t";

        if ($op === 'count')
            return ($this->db->query($query)->getNumRows());

        return $this->db->query($query)->getResult();
    }
}