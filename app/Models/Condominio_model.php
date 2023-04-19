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

    public function new_chamado($user, $assunto, $message)
    {
        $a = '';
        if ($assunto == 's')     $a = 'Sugestão';
        elseif ($assunto == 'd') $a = 'Dúvida';
        elseif ($assunto == 'r') $a = 'Revisão';
        elseif ($assunto == 'v') $a = 'Visita Técnica';

        if (!$this->db->table('esm_tickets')->set(array(
            'user_id' => $user->id,
            'unidade_id' => $user->unidade->id,
            'nome' => $user->username,
            'email' => $user->email,
            'departamento' => 2,
            'assunto' => $a,
            'mensagem' => $message
        ))->insert()) {
            return array("status" => "error", "message" => $this->db->error());
        } else {
            return array(
                "status" => "success",
                "id" => $this->db->insertID(),
                "assunto" => $a,
                "message" => "<strong>Chamado criado com sucesso.</strong><br/>Em até 48hrs entraremos em contato pelo e-mail."
            );
        }
    }

    public function get_user_email($uid)
    {
        return $this->db->query("SELECT secret AS email FROM auth_identities WHERE user_id = $uid AND type = 'email_password'")->getRow()->email;
    }

    public function get_leitura_unidade($tabela, $id)
    {
        return array(
            'leitura_agua'    => $this->get_consumo_unidade($tabela, 'agua', $id),
            'leitura_gas'     => $this->get_consumo_unidade($tabela, 'gas', $id),
            'leitura_energia' => $this->get_consumo_unidade($tabela, 'energia', $id)
        );
    }

    public function get_unidade($id)
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT 
                esm_unidades.*, 
                esm_agrupamentos.nome AS bloco, 
                esm_entidades.id as cid, 
                esm_entidades.tabela,
                esm_entidades.m_agua,
                esm_entidades.m_gas,
                esm_entidades.m_energia
            FROM esm_unidades
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            JOIN esm_entidades ON esm_entidades.id = esm_agrupamentos.entidade_id
            WHERE esm_unidades.id = $id
        ");


        // verifica se retornou algo
        if ($query->getNumRows() == 0) {
            return false;
        }

        return $query->getRow();
    }

    public function get_entradas($cid, $monitoramento, $uid = false, $geral = false)
    {
        $q = "";
        if ($uid) {
            $q = "
                SELECT 
                    esm_entradas.id, 
                    esm_entradas.nome, 
                    esm_entradas.color, 
                    esm_entradas.fatura
                FROM esm_medidores
                JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
                WHERE 
                    esm_medidores.unidade_id = $uid AND 
                    esm_medidores.tipo = '$monitoramento' AND 
                    esm_entradas.entidade_id = $cid AND 
                    esm_medidores.central != '43544C58' AND 
                    esm_medidores.central != '43544C59a'
            ";
        } else {
            if ($geral) {
                $q = "
	                SELECT 
	                    esm_entradas.id, 
	                    esm_entradas.nome, 
	                    esm_entradas.color, 
	                    esm_entradas.fatura,
	                    esm_entradas.ordem
	                FROM esm_entradas
						JOIN esm_medidores ON esm_medidores.entrada_id = esm_entradas.id
	                WHERE 
	                    esm_entradas.entidade_id = $cid AND 
	                    esm_entradas.tipo = '$monitoramento' AND
						esm_medidores.sub_tipo = 'geral'
	                ORDER BY ordem
	            ";
            } else {
                $q = "
	                SELECT 
	                    id, 
	                    nome, 
	                    color, 
	                    fatura,
	                    esm_entradas.ordem
	                FROM esm_entradas
	                WHERE 
	                    entidade_id = $cid AND 
	                    tipo = '$monitoramento'
	                ORDER BY ordem
	            ";
            }
        }

        $query = $this->db->query($q);

        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }

    public function get_alertas($user_id, $filter = '', $monitoramento = false, $registros = false, $finalizado = false)
    {
        // trata filtros
        $f = '';
        if ($filter == 'unread') $f = " AND esm_alertas_envios.lida IS NULL";
        $m = '';
        if ($monitoramento) $m = " AND esm_alertas.monitoramento = '$monitoramento'";
        $e = '';
        if ($finalizado) $e = " AND ISNULL(finalizado)";

        $r = '';
        if ($registros) $r = " LIMIT $registros";


        // realiza a consulta
        $query = $this->db->query("
            SELECT esm_alertas_envios.id, esm_alertas.tipo, esm_alertas.titulo, esm_alertas.texto, esm_alertas.monitoramento, 
            esm_alertas.enviada, esm_alertas_envios.lida
            FROM esm_alertas_envios 
            JOIN esm_alertas ON esm_alertas.id = esm_alertas_envios.alerta_id 
            JOIN auth_users ON auth_users.id = esm_alertas.enviado_por 
            WHERE esm_alertas_envios.user_id = $user_id AND esm_alertas_envios.visibility = 'normal' $f $m $e
            ORDER BY esm_alertas.enviada DESC $r
        ");

        return $query->getResult();
    }

    public function get_ultimo_faturamento($condo_id)
    {
        $query = $this->db->query("
            SELECT *
            FROM esm_fechamentos
            WHERE entidade_id = $condo_id
            ORDER BY data_fim DESC
            LIMIT 1
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }

    public function get_primeira_leitura($tabela, $monitoramento, $uid)
    {
        $data = $this->db->query("
            SELECT MIN(timestamp) AS data
            FROM esm_leituras_{$tabela}_{$monitoramento}
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id           
            WHERE esm_medidores.unidade_id = $uid
        ")->getRow()->data;

        return $data;
    }

    public function get_consumo_desde($unidade_id, $tabela, $monitoramento, $data)
    {
        if (!$unidade_id) {
            $query = "SELECT SUM(consumo) AS consumo
	            FROM esm_leituras_{$tabela}_{$monitoramento}
	            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id           
	            WHERE timestamp BETWEEN $data AND UNIX_TIMESTAMP(NOW()) AND esm_medidores.id != 2229
            ";
        } else {
            if (is_array($unidade_id)) {
                $query = "SELECT SUM(consumo) AS consumo
                    FROM esm_leituras_{$tabela}_{$monitoramento}
                    JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id           
                    WHERE timestamp BETWEEN $data AND UNIX_TIMESTAMP(NOW()) 
                    AND esm_medidores.unidade_id = $unidade_id[0] OR esm_medidores.unidade_id = $unidade_id[1]
                ";
            } else {
                $query = "
                    SELECT IFNULL(SUM(consumo), 0) AS consumo
                    FROM esm_leituras_{$tabela}_{$monitoramento}
                    JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id           
                    WHERE esm_medidores.unidade_id = $unidade_id AND timestamp BETWEEN $data AND UNIX_TIMESTAMP(NOW())
                ";
            }
        }

        return $this->db->query($query)->getRow()->consumo;
    }

    public function get_consumo_vizinhos_desde($unidade_id, $tabela, $monitoramento, $data)
    {
        return $this->db->query("
            SELECT IFNULL(SUM(consumo), 0) AS consumo
            FROM esm_leituras_{$tabela}_{$monitoramento}
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$monitoramento}.medidor_id 
            WHERE esm_medidores.unidade_id != $unidade_id AND timestamp > $data
        ")->getRow();
    }

    public function get_medidores_count($unidade_id, $condo_id, $monitoramento)
    {
        return $this->db->query("
            SELECT COUNT( distinct esm_unidades.id) AS total
            FROM esm_unidades
            JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades.id
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id 
            WHERE esm_unidades.id != $unidade_id AND esm_agrupamentos.entidade_id = $condo_id and esm_medidores.tipo = '$monitoramento'
        ")->getRow()->total;
    }

    public function get_faturamento_unidade($faturamento, $unidade)
    {
        $query = $this->db->query("
            SELECT *
            FROM esm_fechamentos_unidades
            WHERE fechamento_id = $faturamento AND unidade_id = $unidade
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return 0;

        return $query->getRow();
    }

    public function get_admin_condos($user_id)
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT 
                esm_entidades.id, 
                esm_entidades.nome, 
                esm_entidades.tabela, 
                esm_entidades.tipo,
                esm_entidades.logradouro,
                esm_entidades.numero,
                esm_entidades.cidade,
                esm_entidades.uf,   
                esm_entidades.m_agua, 
                esm_entidades.m_gas, 
                esm_entidades.m_energia,
                esm_entidades.m_nivel,
                esm_entidades.timezone,
                esm_administradoras.nome AS adm
            FROM esm_entidades
            JOIN auth_user_relation ON auth_user_relation.entidade_id = esm_entidades.id
            JOIN esm_administradoras_users ON esm_administradoras_users.user_id = auth_user_relation.user_id
            JOIN esm_administradoras ON esm_administradoras.id = esm_administradoras_users.admin_id
            WHERE auth_user_relation.user_id = $user_id
            ORDER BY esm_entidades.nome");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }

    public function get_condo($id)
    {
        // seleciona todos os campos
        $query = $this->db->table('esm_entidades')
            ->select('esm_entidades.*, esm_administradoras.nome AS nome_adm')
            ->join('auth_user_relation', 'auth_user_relation.entidade_id = esm_entidades.id')
            ->join('esm_administradoras_users', 'esm_administradoras_users.user_id = auth_user_relation.user_id')
            ->join('esm_administradoras', 'esm_administradoras.id = esm_administradoras_users.admin_id')
            ->where('esm_entidades.id', $id)
            ->where('esm_entidades.visibility', 'normal');

        // realiza a consulta
        $result = $query->get();

        // verifica se retornou algo
        if ($result->getNumRows() == 0)
            return false;

        return $result->getRow();
    }
}