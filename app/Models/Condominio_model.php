<?php

namespace App\Models;

class Condominio_model extends Base_model
{
    public function get_last_aviso($user_id)
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT esm_alertas.id, esm_alertas.titulo, esm_alertas_envios.lida 
            FROM esm_alertas_envios 
            JOIN esm_alertas ON esm_alertas.id = esm_alertas_envios.alerta_id
            WHERE esm_alertas_envios.user_id = $user_id AND esm_alertas.tipo = 'aviso'
            ORDER BY esm_alertas.enviada DESC
            LIMIT 1
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }

    public function get_consumo_unidade($tabela, $monitoramento, $id)
    {
        if ($monitoramento == "agua") {
            $query = $this->db->query("
                SELECT SUM(ultima_leitura) AS consumo 
                FROM esm_medidores
                WHERE esm_medidores.unidade_id = $id
            ");
        } else if ($monitoramento == "gas" || $monitoramento == "energia") {
            $query = $this->db->query("
                SELECT SUM(t.leitura) AS consumo 
                FROM (
                    SELECT MAX(leitura) AS leitura
                    FROM esm_leituras_{$tabela}_{$monitoramento}
                    JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id
                    WHERE esm_medidores.unidade_id = $id
                    GROUP BY esm_medidores.id) t
            ");
        }


        // verifica se retornou algo
        if (!$query)
            return '-------';

        $consumo = $query->getRow()->consumo;

        if ($monitoramento == "gas") {
            return is_null($consumo) ? '-------' : str_pad(number_format($consumo / 1000, 3, ',', '.'), 9, '0', STR_PAD_LEFT);
        }

        return is_null($consumo) ? '-------' : str_pad(round($consumo), 7, '0', STR_PAD_LEFT);
    }

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

    public function get_consumo_ultima_hora($unidade_id, $tabela, $monitoramento)
    {
        return $this->db->query("
            SELECT SUM(consumo) AS consumo
            FROM esm_leituras_{$tabela}_{$monitoramento}
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id           
            WHERE esm_medidores.unidade_id = $unidade_id AND timestamp > UNIX_TIMESTAMP() - 3600
        ")->getRow()->consumo;
    }

    public function get_consumo_hoje($unidade_id, $tabela, $monitoramento)
    {
        return $this->db->query(
            "
            SELECT SUM(consumo) AS consumo
            FROM esm_leituras_{$tabela}_{$monitoramento}
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id           
            WHERE esm_medidores.unidade_id = $unidade_id AND timestamp > " . strtotime('today midnight')
        )->getRow()->consumo;
    }

    public function get_consumo_last_24($unidade_id, $tabela, $monitoramento)
    {
        return $this->db->query(
            "
            SELECT SUM(consumo) AS consumo
            FROM esm_leituras_{$tabela}_{$monitoramento}
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id
            WHERE esm_medidores.unidade_id = $unidade_id AND timestamp >= " . strtotime("-24 hours")
        )->getRow()->consumo;
    }

    public function get_consumo_last_fechamento($unidade_id, $tabela, $monitoramento)
    {
        // busca último fechamento da unidade
        $data = $this->db->query("
            SELECT MAX(esm_fechamentos.data_fim) AS data_fim
            FROM esm_fechamentos_unidades 
            JOIN esm_fechamentos ON esm_fechamentos.id = esm_fechamentos_unidades.fechamento_id
            WHERE esm_fechamentos_unidades.unidade_id = $unidade_id
        ")->getRow();

        // se nenhum fechamento ainda retorna null
        if (is_null($data))
            return null;
        else {
            // calcula timestamp: 1h do dia seguinte do fim do ultimo faturamente
            // esse calculo resulta na 00h, mas no query busca a hora maior, q é a 1h
            $ts = strtotime('midnight', $data->data_fim + 86400);
            // retorna o consumo desde a data do último fechamento
            return $this->db->query("
                SELECT SUM(consumo) AS consumo
                FROM esm_leituras_{$tabela}_{$monitoramento}
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id           
                WHERE esm_medidores.unidade_id = $unidade_id AND timestamp > $ts
            ")->getRow()->consumo;
        }
    }

    public function get_last_leitura($uid, $tabela, $monitoramento)
    {
        $query = $this->db->query("
            SELECT MAX(timestamp) AS leitura 
            FROM esm_leituras_{$tabela}_{$monitoramento} 
            WHERE medidor_id = (SELECT id 
                FROM esm_medidores
                WHERE unidade_id = $uid
                LIMIT 1)
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow()->leitura;
    }

    public function get_central_by_unidade($uid)
    {
        $query = $this->db->query("
            SELECT esm_medidores.central, esm_entidades_centrais.localizador
            FROM esm_medidores
            JOIN esm_entidades_centrais ON esm_entidades_centrais.nome = esm_medidores.central
            WHERE unidade_id = $uid
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }
}