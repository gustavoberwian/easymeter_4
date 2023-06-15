<?php

namespace App\Models;

class Industria_model extends Base_model
{
    public function get_entidade_by_user($uid)
    {
        $query = "
            SELECT esm_entidades.*
            FROM esm_entidades
            JOIN auth_user_relation ON auth_user_relation.entidade_id = esm_entidades.id
            WHERE auth_user_relation.user_id = $uid";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_unidade_by_user($uid)
    {
        $query = "
            SELECT esm_unidades.*
            FROM esm_unidades
            JOIN auth_user_relation ON auth_user_relation.unidade_id = esm_unidades.id
            WHERE auth_user_relation.user_id = $uid";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_medidores_geral($id, $monitoramento = 'agua', $array = false)
    {
     $subtipo = " AND esm_medidores.sub_tipo = 'geral'";

     if ($monitoramento === 'nivel')
        $subtipo = '';
     
        $query = $this->db->query("
            SELECT 
                esm_medidores.*, 
                esm_unidades.nome AS unidade
            FROM esm_entidades_centrais 
            JOIN esm_medidores ON esm_medidores.central = esm_entidades_centrais.nome
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE 
                esm_entidades_centrais.entidade_id = $id AND 
                esm_medidores.tipo = '$monitoramento' $subtipo
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        if ($array)
            return $query->getResultArray();
        else
            return $query->getResult();
    }

    public function get_last_nivel($id, $tabela, $array = false)
    {

        $query = $this->db->query("
            SELECT esm_sensores_nivel.*, esm_leituras_" . $tabela . "_nivel.leitura, esm_leituras_" . $tabela . "_nivel.timestamp, d.*
            FROM esm_leituras_" . $tabela . "_nivel
            JOIN esm_sensores_nivel ON esm_sensores_nivel.medidor_id = esm_leituras_" . $tabela . "_nivel.medidor_id
            JOIN (
                SELECT $id AS id, MAX(leitura) AS estatico, MIN(leitura) AS minimo FROM esm_leituras_" . $tabela . "_nivel WHERE medidor_id = $id AND leitura > 0
                ) d ON d.id = esm_leituras_" . $tabela . "_nivel.medidor_id
            WHERE 
                esm_leituras_" . $tabela . "_nivel.medidor_id = $id AND
                timestamp <= UNIX_TIMESTAMP(NOW())
            ORDER BY timestamp DESC
            LIMIT 1                
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        if ($array)
            return $query->getRowArray();
        else
            return $query->getRow();
    }

    public function get_consumo_medidores_geral($id, $start, $end, $monitoramento = 'agua', $array = false)
    {
        $query = $this->db->query("
            SELECT 
                SUM(consumo) / 1000 AS value, 
                DATEDIFF('$end', '$start') + 1 AS days
            FROM esm_entidades_centrais 
            JOIN esm_medidores ON esm_medidores.central = esm_entidades_centrais.nome
            JOIN esm_leituras_bauducco_agua ON esm_leituras_bauducco_agua.medidor_id = esm_medidores.id
            WHERE 
                esm_entidades_centrais.entidade_id = $id AND 
                esm_medidores.tipo = '$monitoramento' AND
                esm_medidores.sub_tipo = 'geral' AND
                esm_leituras_bauducco_agua.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                esm_leituras_bauducco_agua.timestamp < (UNIX_TIMESTAMP('$end 23:59:59') + 3600 )
                
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        if ($array)
            return $query->getRowArray();
        else
            return $query->getRow();
    }

    public function get_leituras($mid, $start, $end, $monitoramento, $array = false)
    {
        if ($monitoramento == "nivel") {
            $query = "
                SELECT 
                    IF(MOD(timestamp, 3600) = 0, DATE_FORMAT(FROM_UNIXTIME(timestamp), '%H:%i'), '') AS label,
                    DATE_FORMAT(FROM_UNIXTIME(timestamp), '%H:%i') AS tooltip,
                    esm_sensores_nivel.profundidade_total - ((esm_leituras_bauducco_nivel.leitura - 1162) * esm_sensores_nivel.mca / 4649) as value
                FROM esm_leituras_bauducco_nivel
                JOIN esm_sensores_nivel ON esm_sensores_nivel.medidor_id = esm_leituras_bauducco_nivel.medidor_id
                WHERE
                    esm_leituras_bauducco_nivel.medidor_id = $mid
                    AND esm_leituras_bauducco_nivel.leitura > 0
                    AND timestamp >= UNIX_TIMESTAMP('$end 00:00:00') AND 
                    timestamp <= IF(DATE_FORMAT(NOW(), '%Y-%m-%d') = '$end', UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP('$end 23:59:59'))
                ORDER BY timestamp
            ";

        } else {

            if ($start == $end) {

                $query = "
                    SELECT 
                        CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                        CONCAT(LPAD(IF(esm_hours.num + 1 > 23, 0, esm_hours.num + 1), 2, '0'), ':00') AS next,
                        consumo / 1000 AS value
                    FROM esm_hours
                    LEFT JOIN esm_leituras_bauducco_agua d ON 
                        HOUR(FROM_UNIXTIME(d.timestamp - 3600)) = esm_hours.num AND 
                        d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                        d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') + 3600 AND
                        d.medidor_id = $mid
                    GROUP BY esm_hours.num
                    ORDER BY esm_hours.num
                ";

            } else {

                $query = "
                    SELECT 
                        CONCAT(LPAD(esm_calendar.d, 2, '0'), '/', LPAD(esm_calendar.m, 2, '0')) AS label, 
                        esm_calendar.dt AS date,
                        esm_calendar.dw AS dw,
                        SUM(consumo) / 1000 AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_bauducco_agua d ON 
                        d.timestamp > esm_calendar.ts_start AND 
                        d.timestamp <= esm_calendar.ts_end + 3600 AND
                        d.medidor_id = $mid
                    WHERE 
                        esm_calendar.dt >= '$start' AND 
                        esm_calendar.dt <= '$end' 
                    GROUP BY esm_calendar.dt
                    ORDER BY esm_calendar.dt
                ";
            }
        }

        if ($this->db->query($query)->getNumRows() == 0)
            return false;

        if ($array)
            return $this->db->query($query)->getResultArray();
        else
            return $this->db->query($query)->getResult();
    }

    public function get_report_by_id($id, $array = false)
    {
        // realiza a query via dt
        $query = $this->db->query("
            SELECT esm_condominios.nome, esm_relatorios.*
            FROM esm_relatorios
            JOIN esm_condominios ON esm_condominios.id = esm_relatorios.condo_id
            WHERE esm_relatorios.id = $id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        if ($array)
            return $query->getRowArray();
        else
            return $query->getRow();
    }

    public function get_report_data_by_id($id, $array = false)
    {
        // realiza a query via dt
        $query = $this->db->query("
                SELECT 
                    esm_medidores.nome, 
                    esm_medidores.tipo, 
                    esm_relatorios_dados.leitura_anterior, 
                    esm_relatorios_dados.leitura_atual, 
                    esm_relatorios_dados.consumo 
                FROM esm_relatorios_dados
                JOIN esm_medidores ON esm_medidores.id = esm_relatorios_dados.medidor_id
                WHERE esm_relatorios_dados.relatorio_id = $id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        if ($array)
            return $query->getResultArray();
        else
            return $query->getResult();
    }

    public function get_user_alert($id, $monitoramento = null, $readed = false)
    {
        $query = $this->db->query("
            SELECT 
                esm_alertas_" . $monitoramento . ".tipo, 
                esm_alertas_" . $monitoramento . ".titulo, 
                esm_alertas_" . $monitoramento . ".texto, 
                COALESCE(esm_alertas_" . $monitoramento . ".enviada, 0) AS enviada,
                COALESCE(esm_alertas_" . $monitoramento . "_envios.lida, '') AS lida
            FROM esm_alertas_" . $monitoramento . "_envios
            JOIN esm_alertas_" . $monitoramento . " ON esm_alertas_" . $monitoramento . ".id = esm_alertas_" . $monitoramento . "_envios.alerta_id
            WHERE esm_alertas_" . $monitoramento . "_envios.id = $id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        $ret = $query->getRow();

        if ($readed) {
            // atualiza esm_alertas
            $this->db->table('esm_alertas_' . $monitoramento . '_envios')
                ->where('id', $id)
                ->where('lida', NULL)
                ->set(array('lida' => date("Y-m-d H:i:s")))
                ->update();
        }

        return $ret;
    }

    public function get_user_emails($user_id)
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT
                esm_user_emails.email
            FROM
                esm_user_emails
            WHERE
                esm_user_emails.user_id = $user_id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return '';

        $emails = '';
        foreach ($query->getResult() as $e) {
            $emails .= $e->email . ',';
        }

        return trim($emails, ',');
    }
}