<?php

namespace App\Models;

use Config\Database;

class Admin_model extends Base_model
{
    public function get_all_centrais()
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT esm_entidades_centrais.nome as DT_RowId, esm_entidades_centrais.nome, esm_entidades_centrais.modo, esm_entidades_centrais.board, 
                esm_entidades_centrais.firmware, esm_entidades_centrais.simcard, esm_entidades.nome AS entidade, esm_entidades.tabela, 
                esm_entidades_centrais.parent, esm_entidades_centrais.auto_ok, esm_entidades_centrais.ultimo_envio,
                esm_entidades_centrais.localizador, esm_entidades_centrais.tamanho, esm_central_data.fonte, esm_central_data.tensao, esm_central_data.fraude_hi, esm_central_data.fraude_low
            FROM esm_entidades_centrais 
            JOIN esm_entidades ON esm_entidades.id = esm_entidades_centrais.entidade_id
            LEFT JOIN esm_central_data ON esm_central_data.nome = esm_entidades_centrais.nome AND esm_central_data.timestamp = esm_entidades_centrais.ultimo_envio
            ORDER BY esm_entidades.ordem, esm_entidades_centrais.entidade_id, esm_entidades_centrais.nome
        ");

        return $query->getResult();
    }

    public function get_central_entidade($id)
    {
        // realiza a consulta
        $query = $this->db->query("
        SELECT
            esm_entidades.nome AS entidade_nome,
            esm_entidades_centrais.nome AS DT_RowId,
            esm_entidades_centrais.nome,
            esm_entidades_centrais.modo,
            esm_entidades_centrais.board,
            esm_entidades_centrais.firmware,
            esm_entidades_centrais.simcard,
            esm_entidades.nome AS entidade,
            esm_entidades.tabela,
            esm_entidades_centrais.parent,
            esm_entidades_centrais.auto_ok,
            esm_entidades_centrais.ultimo_envio,
            esm_entidades_centrais.localizador 
        FROM
            esm_entidades_centrais
            JOIN esm_entidades ON esm_entidades.id = esm_entidades_centrais.entidade_id 
        WHERE
            esm_entidades_centrais.nome = '$id'
        ");

        if ($query->getNumRows() == 0) {
            $db = Database::connect('easy_com_br');
            $query = $db->query("
            SELECT 
                esm_condominios.nome AS entidade_nome,
                esm_condominios_centrais.nome as DT_RowId,
                esm_condominios_centrais.nome,
                esm_condominios_centrais.modo,
                esm_condominios_centrais.board, 
                esm_condominios_centrais.firmware,
                esm_condominios_centrais.simcard,
                esm_condominios.nome AS condo,
                esm_condominios.tabela, 
                esm_condominios_centrais.parent,
                esm_condominios_centrais.auto_ok,
                esm_condominios_centrais.ultimo_envio,
                esm_condominios_centrais.localizador
            FROM 
                esm_condominios_centrais 
            JOIN
                esm_condominios ON esm_condominios.id = esm_condominios_centrais.condo_id
            WHERE 
                esm_condominios_centrais.nome = '$id'
            ");
        }


        return $query->getRow();
    }

    public function get_entity($id)
    {
        // seleciona todos os campos
        $query = $this->db->table('esm_entidades')
            ->select('esm_entidades.*, esm_administradoras.nome AS nome_adm, esm_pessoas.nome as nome_sindico')
            ->join('esm_administradoras', 'esm_entidades.admin_id = esm_administradoras.id', 'LEFT')
            ->join('esm_pessoas', 'esm_entidades.gestor_id = esm_pessoas.id', 'LEFT')
            ->where('esm_entidades.id', $id)
            ->where('esm_entidades.visibility', 'normal')
            ->get();

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }

    public function get_last_data($central, $stamp)
    {
        if (!$stamp) {
            $query = $this->db->query("
            SELECT *
            FROM esm_central_data
            WHERE nome = '$central'
        ");
        } else {
            $query = $this->db->query("
            SELECT *
            FROM esm_central_data
            WHERE nome = '$central' AND timestamp = $stamp
        ");
        }

        // verifica se retornou algo
        if (!$query) {
            $db = Database::connect('easy_com_br');
            if (!$stamp) {
                $query = $db->query("
                SELECT *
                FROM esm_central_data
                WHERE nome = '$central'
            ");
            } else {
                $query = $db->query("
                SELECT *
                FROM esm_central_data
                WHERE nome = '$central' AND timestamp = $stamp
            ");
            }
        }
        if (!$query)
            return 0;
            

        return $query->getRow();
    }

    public function get_error_leitura($central, $tabela)
    {
        $medidor = $this->db->query("
            SELECT id 
            FROM esm_medidores
            WHERE central = '$central'
            LIMIT 1
        ");

        // verifica se retornou algo
        if ($medidor->getNumRows() == 0)
            return false;

        $db = Database::connect('easy_com_br');

        $query = $this->db->query("SHOW TABLES LIKE 'esm_leituras_{$tabela}_agua'");

        // 604800 = ultimos 7 dias
        if ($query->getNumRows() != 0) {
            $query = $this->db->query("
            SELECT COUNT(timestamp) AS realizadas, ( UNIX_TIMESTAMP() - MOD(UNIX_TIMESTAMP(), 3600) - MIN(timestamp) ) / 3600 + 1 AS total
            FROM esm_leituras_{$tabela}_agua 
            WHERE medidor_id = {$medidor->getRow()->id}  AND timestamp > (UNIX_TIMESTAMP() - 604800)
        ");
        } else {
            $query = $db->query("
            SELECT COUNT(timestamp) AS realizadas, ( UNIX_TIMESTAMP() - MOD(UNIX_TIMESTAMP(), 3600) - MIN(timestamp) ) / 3600 + 1 AS total
            FROM esm_leituras_{$tabela}_agua 
            WHERE medidor_id = {$medidor->getRow()->id}  AND timestamp > (UNIX_TIMESTAMP() - 604800)
        ");
        }

        // verifica se retornou algo
        if (!$query)
            return 0;

        return $query->getRow();
    }

    public function get_entity_for_select($q = 0)
    {

        $query = $this->db->table('esm_entidades')->select('*');

        // se termo de busca
        if ($q) {
            // configura fitros
            $query->groupStart()
                ->like('nome', $q)
                ->groupEnd();
        }

        // ordena por nome
        $query->orderBy('nome');

        // realiza a consulta
        $result = $query->get();

        // retorna os resultados
        return $result->getResult();
    }

    public function get_groups($entidade)
    {
        $query = $this->db->table('esm_agrupamentos')
            ->where('esm_agrupamentos.entidade_id', $entidade)
            ->orderBy('nome', 'ASC')
            ->get();

        return $query->getResult();
    }

    public function get_entities()
    {
        $query = $this->db->query("
        SELECT
            esm_entidades.nome
        FROM
            esm_entidades
        ");
        $values = array();
        if ($query->getNumRows() == 0)
            return false;

        foreach ($query->getResult() as $q) {
            $values[$q->nome] = $q->nome;
        }

        return $values;
    }

    public function get_groups_for_select()
    {
        $query = $this->db->query("
        SELECT
            esm_agrupamentos.nome
        FROM
            esm_agrupamentos
        ");
        $values = array();
        if ($query->getNumRows() == 0)
            return false;

        foreach ($query->getResult() as $q) {
            $values[$q->nome] = $q->nome;
        }

        return $values;
    }

    public function get_ramais($entidade, $as_string = false, $tipo = '')
    {
        $query = $this->db->table('esm_ramais')->orderBy('id')->where('entidade_id', $entidade);

        if ($tipo != '')
            $query->where('tipo', $tipo);

        $query = $query->get();

        if ($as_string) {
            $ramais = $query->getResult();
            $ret = '';
            foreach ($ramais as $r) {
                $ret = $ret . ',' . $r->nome;
            }

            return substr($ret, 1);
        } else {
            return $query->getResult();
        }
    }

    public function get_centrais($entidade, $as_string = false)
    {
        $query = $this->db->table('esm_entidades_centrais')
            ->where('entidade_id', $entidade)
            ->orderBy('id')
            ->get();

        if ($as_string) {
            $centrais = $query->getResult();
            $ret = '';
            foreach ($centrais as $c) {
                $ret = $ret . ',' . $c->nome;
            }

            return substr($ret, 1);
        } else {
            return $query->getResult();
        }
    }

    public function get_centrais_count()
    {
        return $this->db->query("SELECT COUNT(*) AS total FROM esm_condominios_centrais")->getRow()->total;
    }

    public function get_entradas($entidade_id)
    {
        $query = $this->db->query("
            SELECT id AS eid, nome AS entrada, tipo
            FROM esm_entradas
            WHERE entidade_id = $entidade_id
            ORDER BY FIELD(tipo, 'agua', 'gas', 'energia'), ordem, id
        ");

        return $query->getResult();
    }

    public function get_central_leituras($central, $tabela)
    {
        $db = Database::connect('easy_com_br');
        $query = $this->db->query("
            SELECT DATE_FORMAT(FROM_UNIXTIME(timestamp),'%d/%m/%Y') AS label, COUNT(*) AS leituras
            FROM esm_leituras_{$tabela}_agua
            WHERE timestamp >= UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 40 DAY)) AND medidor_id = (SELECT id FROM esm_medidores WHERE central = '$central' LIMIT 1)
            GROUP BY year(FROM_UNIXTIME(`timestamp`)), month(FROM_UNIXTIME(`timestamp`)), day(FROM_UNIXTIME(`timestamp`))
        ");
        if ($query) {
            $query = $db->query("
            SELECT DATE_FORMAT(FROM_UNIXTIME(timestamp),'%d/%m/%Y') AS label, COUNT(*) AS leituras
            FROM esm_leituras_{$tabela}_agua
            WHERE timestamp >= UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 40 DAY)) AND medidor_id = (SELECT id FROM esm_medidores WHERE central = '$central' LIMIT 1)
            GROUP BY year(FROM_UNIXTIME(`timestamp`)), month(FROM_UNIXTIME(`timestamp`)), day(FROM_UNIXTIME(`timestamp`))
        ");
        }

        $values = array();
        if ($query->getNumRows() == 0)
            return false;

        foreach ($query->getResult() as $r) {
            $values[$r->label] = $r->leituras;
        }

        return $values;
    }

    public function get_competencias($entity_id, $ramal_id)
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT *
            FROM esm_fechamentos
            WHERE entidade_id = $entity_id AND ramal_id = $ramal_id
            ORDER BY id DESC
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }

    public function get_chamado($id)
    {
        $db = Database::connect('easy_com_br');
        $query = $db->query("
            SELECT 
                esm_tickets.*,
                auth_users.nome AS user_name,
                esm_condominios.nome AS entidade,
                esm_blocos.nome AS agrupamento, 
                esm_unidades.nome AS unidade, 
                user.telefone
            FROM 
                esm_tickets
            LEFT JOIN 
                auth_users ON auth_users.id = esm_tickets.fechado_por
            LEFT JOIN 
                auth_users user ON user.id = esm_tickets.user_id
            LEFT JOIN 
                esm_unidades ON esm_unidades.id = esm_tickets.unidade_id
            LEFT JOIN 
                esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
            LEFT JOIN 
                esm_condominios ON esm_condominios.id = esm_blocos.condo_id
            WHERE 
                esm_tickets.id = $id
        ");
        
        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }

    public function get_chamado_reply($id)
    {
        // realiza a consulta
        $query = $this->db->query("
        SELECT esm_tickets_reply.*, auth_users.username 
        FROM esm_tickets_reply
        LEFT JOIN auth_users ON auth_users.id = esm_tickets_reply.user_id
        WHERE esm_tickets_reply.ticket_id = $id    
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }
  
    public function chamado_close($id, $user)
    {
        return $this->db->table('esm_tickets')->where('id', $id)->set(array('status' => 'fechado', 'fechado_em' => date("Y/m/d H:i:s"),'fechado_por' => $user)->update());
    }

    public function get_medidores_unidade($unidade_id)
    {
        $query = $this->db->query("
            SELECT esm_medidores.id, esm_medidores.sensor_id, esm_medidores.nome, esm_medidores.central, esm_medidores.posicao, 
            esm_medidores.fator, esm_entradas.id AS eid, esm_entradas.nome AS entrada, esm_entradas.tipo
            FROM esm_medidores
            JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
            WHERE unidade_id = $unidade_id
            ORDER BY FIELD(esm_entradas.tipo, 'agua', 'gas', 'energia'), esm_entradas.ordem, id
        ");

        return $query->getResult();
    }

    public function get_fechamentos()
    {
        $entity_id = $this->input->getGet('entity');
        $this->user = auth()->user();
        if (is_null($entity_id))
            $entity_id = $this->user->entity->id;
        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT esm_fechamentos.id AS DT_RowId, esm_fechamentos.competencia,
			DATE_FORMAT(FROM_UNIXTIME(esm_fechamentos.data_inicio),'%d/%m/%Y') AS data_inicio,
			DATE_FORMAT(FROM_UNIXTIME(esm_fechamentos.data_fim),'%d/%m/%Y') AS data_fim, 
            LPAD(esm_fechamentos.leitura_anterior, 6, '0') AS leitura_anterior, LPAD(esm_fechamentos.leitura_atual, 6, '0') AS leitura_atual, 
            CONCAT(esm_fechamentos.leitura_atual - esm_fechamentos.leitura_anterior, ' m<sup>3</sup>') AS consumo,
            CONCAT('<span class=\"float-left\">R$</span> ', FORMAT(esm_fechamentos.v_concessionaria, 2, 'de_DE')) AS v_concessionaria, 
            DATE_FORMAT(esm_fechamentos.cadastro,'%d/%m/%Y') AS cadastro, esm_ramais.nome AS ramal,
            (SELECT IFNULL(GROUP_CONCAT(DATE_FORMAT(data, '%d/%m/%Y') SEPARATOR '<br/>'), 'Não Enviados') FROM esm_fechamentos_envios WHERE fechamento_id = esm_fechamentos.id) AS envios
            FROM esm_fechamentos
			LEFT JOIN esm_ramais ON esm_fechamentos.ramal_id = esm_ramais.id
            LEFT JOIN esm_entidades ON esm_ramais.entidade_id = esm_entidades.id
            WHERE esm_entidades.id = $entity_id AND esm_ramais.tipo = 'agua' ORDER BY esm_fechamentos.id DESC
        ");

        $dt->edit('envios', function ($data) {
            if ($data['envios'] == 'Não Enviados')
                return '<span class="badge badge-warning">Não Enviados</span>';
            else
                return '<span class="badge badge-success" title="' . $data['envios'] . '" data-toggle="tooltip" data-html="true">Enviados</span>';
        });

        $dt->edit('competencia', function ($data) {
            return competencia_nice($data['competencia']);
        });

        // inclui actions
        $dt->add('action', function ($data) {
            $dis = "";
            if ($this->user->inGroup('demo')) {
                $dis = " disabled";
            }

            return '<a href="#" class="action-download-agua ' . $dis . '" data-id="' . $data['DT_RowId'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
				<a href="#" class="action-delete ' . $dis . '" data-id="' . $data['DT_RowId'] . '"><i class="fas fa-trash" title="Excluir"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function get_leituras()
    {
        $entity_id = $this->input->getGet('condo');
        $this->user = auth()->user();
        if (is_null($entity_id))
            $entity_id = $this->user->entity->id;

        // realiza a query via dt
        $dt = $this->datatables->query("
            SELECT 
                UNIX_TIMESTAMP(STR_TO_DATE(CONCAT('01/', competencia), '%d/%m/%Y')) AS competencia,
                esm_fechamentos.data_inicio,
                esm_fechamentos.data_fim, 
                esm_fechamentos.leitura_atual - esm_fechamentos.leitura_anterior AS consumo,
                esm_fechamentos.cadastro AS leitura,
                esm_fechamentos.id AS DT_RowId
            FROM esm_fechamentos
            LEFT JOIN esm_ramais ON esm_fechamentos.ramal_id = esm_ramais.id
            LEFT JOIN esm_entidades ON esm_ramais.entidade_id = esm_entidades.id
            WHERE esm_entidades.id = $entity_id AND esm_ramais.nome LIKE \"G%\" ORDER BY esm_fechamentos.id DESC
        ");

        $dt->edit('competencia', function ($data) {
            return competencia_nice(date("m/Y", $data['competencia']));
        });

        $dt->edit('data_inicio', function ($data) {
            return date("d/m/Y", $data['data_inicio']);
        });

        $dt->edit('data_fim', function ($data) {
            return date("d/m/Y", $data['data_fim']);
        });

        $dt->edit('leitura', function ($data) {
            return date_format(date_create($data['leitura']), "d/m/Y");
        });

        $dt->edit('consumo', function ($data) {
            return number_format($data['consumo'] / 1000, 3, ',', '.') . ' m<sup>3</sup>';
        });

        // inclui actions
        $dt->add('action', function ($data) {
            return '<a href="#" class="action-download-gas" data-id="' . $data['DT_RowId'] . '" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>';
        });

        // gera resultados
        echo $dt->generate();
    }

    public function delete_entity($id)
    {
        $v = 'delbyuser';
        if (auth()->user()->inGroup('superadmin'))
            $v = 'delbyadmin';

        if (!$this->db->table('esm_entidades')->where('id', $id)->set(array('visibility' => $v))->update()) {
            echo json_encode(array("status" => "error", "message" => $this->db->error()));
            return;
        }
        echo json_encode(array("status" => "success", "message" => ""));
    }

    public function get_administradoras($q = 0)
    {
        $query = $this->db->table('esm_administradoras')->select('*');

        // se termo de busca
        if ($q) {
            // configura fitros
            $query->groupStart()
                ->like('nome', $q)
                ->orLike('cnpj', preg_replace('/[^0-9]/', '', $q))
                ->groupEnd();
        }

        // se ativo
        $query->where('status', 'ativo');

        // se não removido
        $query->where('visibility', 'normal');

        // ordena por nome
        $query->orderBy('nome');

        // realiza a consulta
        $result = $query->get();

        // retorna os resultados
        return $result->getResult();
    }

    public function get_pessoas($q = 0)
    {
        $query = $this->db->table('esm_pessoas')->select('*');

        // se termo de busca
        if ($q) {
            // configura filtros
            $query->groupStart()
                ->like('nome', $q)
                ->orLike('cpf', $q)
                ->groupEnd();
        }

        // se ativo
        $query->where('status', 'ativo');

        // se não removido
        $query->where('visibility', 'normal');

        // ordena por nome
        $query->orderBy('nome');

        // realiza a consulta
        $result = $query->get();

        // retorna os resultados
        return $result->getResult();
    }

    public function add_adm($dados)
    {
        if (!$this->db->table('esm_administradoras')->set($dados)->insert()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Administradora criada com sucesso.", "id" => $this->db->insertID()));
    }

    public function add_gestor($dados)
    {
        if (!$this->db->table('esm_pessoas')->set($dados)->insert()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Gestor criado com sucesso.", "id" => $this->db->insertID()));
    }

    public function add_ramal($dados)
    {
        if (!$this->db->table('esm_ramais')->set($dados)->insert()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Ramal criado   com sucesso.", "id" => $this->db->insertID()));
    }

    public function add_entity($dados)
    {
        if (!$this->db->table('esm_entidades')->set($dados)->insert()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Entidade criada com sucesso.", "id" => $this->db->insertID()));
    }

    public function edit_entity($dados)
    {
        if (!$this->db->table('esm_entidades')->set($dados)->where('esm_entidades.id', $dados['id'])->update()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Entidade editada com sucesso."));
    }

    public function get_bloco($id)
    {
        $query = $this->db->table('esm_agrupamentos')
            ->select('esm_agrupamentos.*, COUNT(esm_unidades.id) AS unidades')
            ->join('esm_unidades', 'esm_unidades.agrupamento_id = esm_agrupamentos.id', 'LEFT')
            ->where('esm_agrupamentos.id', $id)
            ->get();

        return $query->getRow();
    }

    public function get_entity_config_by_bloco($b, $extra = '')
    {
        $query = $this->db->table('esm_entidades')
            ->select('esm_entidades.fracao_ideal, esm_entidades.m_agua, esm_entidades.m_gas, esm_entidades.m_energia' . $extra)
            ->join('esm_agrupamentos', 'esm_agrupamentos.entidade_id = esm_entidades.id')
            ->where('esm_agrupamentos.id', $b);

        // realiza a consulta
        $result = $query->get();

        return $result->getRow();
    }

    public function get_fracoes_entidade($cid)
    {
        $query = $this->db->query("
            SELECT DISTINCT fracao 
            FROM esm_unidades
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            JOIN esm_entidades ON esm_entidades.id = esm_agrupamentos.entidade_id
            WHERE esm_entidades.id = $cid
            ORDER BY fracao
        ");

        return $query->getResult();
    }

    public function get_unidade($id, $completo = false)
    {
        $query = $this->db->table('esm_unidades')
            ->where('esm_unidades.id', $id);

        if ($completo) {
            $query->select('esm_unidades.nome AS apto, esm_unidades.andar, esm_unidades.leitura_anterior, esm_unidades.leitura_atual, esm_agrupamentos.nome AS bloco, esm_entidades.*');
        }

        if ($completo) {
            $query->join('esm_agrupamentos', 'esm_agrupamentos.id = esm_unidades.agrupamento_id', 'LEFT');
            $query->join('esm_entidades', 'esm_entidades.id = esm_agrupamentos.entidade_id', 'LEFT');
        }
        // realiza a consulta
        $result = $query->get();

        return $result->getRow();
    }

    public function get_portas($central)
    {
        $query = $this->db->table('esm_medidores')
            ->select('posicao')
            ->where('central', $central)
            ->orderBy('posicao');

        $result = $query->get();

        return $result->getResultArray();
    }

    public function update_agrupamento($bid, $nome, $rid)
    {
        // atualiza bloco
        if (!$this->db->table('esm_agrupamentos')->where('id', $bid)->set(array('nome' => $nome, 'ramal_id' => $rid))->update())
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        else
            return json_encode(array("status" => "success", "message" => "Bloco atualizado com sucesso!", "text" => $nome, "value" => $bid));
    }

    public function get_medidor($id)
    {
        // aplica filtro pelo id
        $query = $this->db->query("
            SELECT esm_medidores.*, esm_sensores_agua.calibracao, esm_sensores_agua.utilizacao, esm_sensores_agua.instalacao
            FROM esm_medidores
            LEFT JOIN esm_sensores_agua ON esm_sensores_agua.id = esm_medidores.sensor_id
            WHERE esm_medidores.id = $id
		");

        return $query->getRow();
    }

    public function delete_bloco($id)
    {
        if (!$this->db->table('esm_agrupamentos')->where('id', $id)->delete()) {
            return json_encode(array("status" => "error", "message" => ($this->db->error()['code'] == 1451) ? 'Não é possível excluir o bloco pois ele já possui unidades cadastradas.' : $this->db->error()['message']));
        }
        return json_encode(array("status" => "success", "message" => "Bloco excluído com sucesso"));
    }

    public function add_agrupamento($cid, $nome, $rid)
    {
        // insere bloco
        if (!$this->db->table('esm_agrupamentos')->set(array('entidade_id' => $cid, 'nome' => $nome, 'ramal_id' => $rid))->insert()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        // pega id da nova unidade
        $bid = $this->db->insertID();

        return json_encode(array("status" => "success", "message" => "Bloco inserido com sucesso!", "data" => array("value" => $bid, "text" => $nome)));
    }

    public function add_unidade($unidade, $dados)
    {
        // inicia transaction
        $failure = array();
        $this->db->transStart();

        // insere unidade
        if (!$this->db->table('esm_unidades')->set($unidade)->insert())
            $failure[] = $this->db->error();

        // pega id da nova unidade
        $dados['unidade_id'] = $this->db->insertID();

        if (!is_null($dados['nome']) or !is_null($dados['email'])) {
            // insere medidor
            if (!$this->db->table('esm_unidades_dados')->set($dados)->insert())
                $failure[] = $this->db->error();
        }

        // finaliza transação
        $this->db->transComplete();

        // verifica status e retorna de acordo
        if ($this->db->transStatus() === FALSE) {
            if ($failure[0]['code'] == 1062)
                return json_encode(array("status" => "error", "message" => 'Já existe uma unidade cadastrada com este identificador!'));
            else
                return json_encode(array("status" => "error", "message" => $failure[0]['message']));
        } else {
            return json_encode(array("status" => "success", "message" => "Unidade inserida com sucesso!"));
        }
    }

    public function delete_unidade($id)
    {
        if (!$this->db->table('esm_unidades')->where('id', $id)->delete()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }
        return json_encode(array("status" => "success", "message" => "Unidade excluída com sucesso"));
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

    public function get_entidade($entidade)
    {
        $query = "
            SELECT *
            FROM esm_entidades
            WHERE id = $entidade";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_entidade_by_group($group)
    {
        $query = "
            SELECT *
            FROM esm_entidades
            JOIN esm_agrupamentos ON esm_agrupamentos.entidade_id = esm_entidades.id
            WHERE esm_agrupamentos.id = $group";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_entidade_by_unity($unity)
    {
        $query = "
            SELECT *
            FROM esm_entidades
            JOIN esm_agrupamentos ON esm_agrupamentos.entidade_id = esm_entidades.id
            JOIN esm_unidades ON esm_unidades.agrupamento_id = esm_agrupamentos.id
            WHERE esm_unidades.id = $unity";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function update_avatar($user_id, $img)
    {
        $query = $this->db->query("
            SELECT
	            auth_users.avatar
            FROM
	            auth_users
        ");

        $this->db->table('auth_users')
            ->where('id', $user_id)
            ->set('avatar', $img)
            ->update();
    }

    public function get_id_by_name($name)
    {
        $query = $this->db->query("
        SELECT
            esm_pessoas.id
        FROM
            esm_pessoas
        WHERE
            esm_pessoas.nome = '$name'
        ");

        if ($query->getNumRows() === 0)
            return false;

        return $query->getRow()->id;
    }


    public function get_id_by_adm_name($name)
    {
        $query = $this->db->query("
        SELECT
            esm_administradoras.id
        FROM
            esm_administradoras
        WHERE
            esm_administradoras.nome = '$name'
        ");

        if ($query->getNumRows() === 0)
            return false;

        return $query->getRow()->id;
    }

    public function group_ramais($data)
    {
        if (!$this->db->table('esm_agrupamentos_ramais')->set($data)->insert()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Agrupamento de ramais criado com sucesso.", "id" => $this->db->insertID()));
    }

    public function get_groups_by_user($user_id)
    {
        $query = $this->db->query("SELECT auth_groups_users.group FROM auth_groups_users WHERE auth_groups_users.user_id = $user_id");

        return $query->getResult();
    }

    public function add_user($dados)
    {
        // Insere grupos de monitoramento
        foreach ($dados['group'] as $m) {
            if (!$this->db->table('auth_groups_users')->set(array('group' => $m, 'user_id' => $dados['user-id']))->insert()) {
                return json_encode(array("status" => "error", "message" => $this->db->error()));
            }
        }

        // Insere infos nas tabelas de usuario
        if (!$this->db->table('auth_users')->where('id', $dados['user-id'])->set(array('page' => $dados['page']))->update()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        if (!$this->db->table('auth_groups_users')->set(array('group' => $dados['page'], 'user_id' => $dados['user-id']))->insert()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        //Inserção com base na classificação do usuário
        if ($dados['classificacao'] === 'unidade') {
            if (!$this->db->table('auth_user_relation')->set(array('user_id' => $dados['user-id'], 'unidade_id' => $this->get_unity_by_code($dados['unity-user'])->id))->insert()) {
                return json_encode(array("status" => "error", "message" => $this->db->error()));
            }
            if (!$this->db->table('auth_groups_users')->set(array('group' => 'unity', 'user_id' => $dados['user-id']))->insert()) {
                return json_encode(array("status" => "error", "message" => $this->db->error()));
            }
        } else {

            if ($dados['classificacao'] === 'entidades') {
                if (!$this->db->table('auth_user_relation')->set(array('user_id' => $dados['user-id'], 'entidade_id' => $this->get_table_by_name($dados['classificacao'], $dados['entity-user'])->id))->insert()) {
                    return json_encode(array("status" => "error", "message" => $this->db->error()));
                }

                if (!$this->db->table('auth_groups_users')->set(array('group' => 'admin', 'user_id' => $dados['user-id']))->insert()) {
                    return json_encode(array("status" => "error", "message" => $this->db->error()));
                }
            } else {
                if (!$this->db->table('auth_user_relation')->set(array('user_id' => $dados['user-id'], 'agrupamento_id' => $this->get_table_by_name($dados['classificacao'], $dados['group-user'])->id))->insert()) {
                    return json_encode(array("status" => "error", "message" => $this->db->error()));
                }

                if (!$this->db->table('auth_groups_users')->set(array('group' => 'group', 'user_id' => $dados['user-id']))->insert()) {
                    return json_encode(array("status" => "error", "message" => $this->db->error()));
                }
            }
        }
        //Insere grupos adicionais
        foreach ($dados['groups-user'] as $groups) {
            if (!$this->db->table('auth_groups_users')->set(array('group' => $groups, 'user_id' => $dados['user-id']))->insert()) {
                return json_encode(array("status" => "error", "message" => $this->db->error()));
            }
        }
        return json_encode(array("status" => "success", "message" => "Usuário criado com sucesso."));
    }

    public function get_table_by_name($type, $name)
    {
        // seleciona todos os campos
        $query = $this->db->query("
        SELECT
            esm_{$type}.id
        FROM
            esm_{$type}
        WHERE
            esm_{$type}.nome = '$name'
        ");
        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }

    public function get_unity_by_code($code)
    {
        // seleciona todos os campos
        $query = $this->db->table('esm_unidades')
            ->select('esm_unidades.id')
            ->where('esm_unidades.codigo', $code)
            ->get();

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }

    public function update_active($uid)
    {
        $query = $this->db->table('auth_users')
            ->select('auth_users.active')
            ->where('auth_users.id', $uid)
            ->get();

        if ($query->getNumRows() == 0)
            return false;

        //edita coluna active do usuario
        if ($query->getRow()->active == 0) {
            if (!$this->db->table('auth_users')->where('id', $uid)->set(array('active' => 1))->update()) {
                return json_encode(array("status" => "error", "message" => $this->db->error()));
            }
        } else {
            if (!$this->db->table('auth_users')->where('id', $uid)->set(array('active' => 0))->update()) {
                return json_encode(array("status" => "error", "message" => $this->db->error()));
            }
        }
    }

    public function get_user_info($user_id)
    {
        $users = model('UserModel');

        // Seleciona usuário
        $user = $users->findById($user_id);
        $data = [];

        //set da informação
        if ($user->inGroup("admin")) {
            $data['classificacao'] = 'entidades';

        } elseif ($user->inGroup("group")) {
            $data['classificacao'] = 'agrupamentos';

        } elseif ($user->inGroup("unity")) {
            $data['classificacao'] = 'unidade';

        } else {
            $data['classificacao'] = '';
        }
        //Recebe email do usuário
        $query = $this->db->table('auth_identities')
            ->select('auth_identities.secret')
            ->where('auth_identities.user_id', $user_id)
            ->where('auth_identities.type', 'email_password')
            ->get();

        if ($query->getNumRows() == 0)
            return false;

        $data['email'] = $query->getRow()->secret;

        return $data;
    }

    public function edit_user($dados)
    {
        $user_id = $dados['user_id'];

        //Edição das informações de usuário
        $filter = '';

        //Edita infos dos grupos de monitoramento
        foreach ($dados['group'] as $key => $m) {
            if ($m == '') {
                if (!$this->db->table('auth_groups_users')->where('user_id', $dados['user_id'])->where('group', $key)->delete()) {
                    return json_encode(array("status" => "error", "message" => $this->db->error()));
                }
            } else {
                $query = $this->db->query("
                SELECT
                    auth_groups_users.group
                FROM
                    auth_groups_users
                WHERE
                    auth_groups_users.user_id = $user_id AND
                    auth_groups_users.group = '$key'
            ");
                if ($query->getNumRows() == 0) {
                    if (!$this->db->table('auth_groups_users')->set(array('group' => $m, 'user_id' => $dados['user_id']))->insert()) {
                        return json_encode(array("status" => "error", "message" => $this->db->error()));
                    }
                }
            }
            //filtro para a exibição de grupos adicionais
            $filter .= "AND auth_groups_users.group != '$m' ";
        }
        //Edição de infos de usuário
        if (!$this->db->table('auth_users')->where('id', $dados['user_id'])->set('page', $dados['page'])->update()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        if (!$this->db->table('auth_groups_users')->where('user_id', $dados['user_id'])->where('group', 'shopping')->orWhere('group', 'condominio')->orWhere('group', 'unity')->set('group', $dados['page'])->update()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        //Set infos grupos adicionais
        foreach ($dados['groups-user'] as $groups) {

            $query = $this->db->query("
            SELECT
                auth_groups_users.group
            FROM
                auth_groups_users
            WHERE
                auth_groups_users.user_id = $user_id AND
                auth_groups_users.group = '$groups'
            ");

            if ($query->getNumRows() == 0) {
                if (!$this->db->table('auth_groups_users')->set(array('group' => $groups, 'user_id' => $dados['user_id']))->insert()) {
                    return json_encode(array("status" => "error", "message" => $this->db->error()));
                }
            }
            //atualiza o filtro
            $filter .= "AND auth_groups_users.group != '$groups' ";
        }
        //realiza edição grupos adicionais
        $query = $this->db->query(
            "
            SELECT
                auth_groups_users.group
            FROM
                auth_groups_users
            WHERE
                auth_groups_users.user_id = $user_id
            AND 
                auth_groups_users.group != 'shopping'
            AND 
                auth_groups_users.group != 'condominio'
            AND 
                auth_groups_users.group != 'unity'
            AND 
                auth_groups_users.group != 'admin' " . $filter
        );

        if ($query->getNumRows() == 0) {
            return json_encode(array("status" => "success", "message" => "Usuário editado com sucesso."));
        } else {
            foreach ($query->getResultArray() as $q) {
                if (!$this->db->table('auth_groups_users')->where('user_id', $dados['user_id'])->where('group', $q)->delete()) {
                    return json_encode(array("status" => "error", "message" => $this->db->error()));
                }
            }
        }

        return json_encode(array("status" => "success", "message" => "Usuário editado com sucesso."));
    }

    public function get_name_by_id($type, $id)
    {
        // seleciona todos os campos
        $query = $this->db->query("
        SELECT
            esm_{$type}.nome
        FROM
            esm_{$type}
        WHERE
            esm_{$type}.id = $id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow()->nome;
    }

    public function get_code_by_unity_id($unity_id)
    {
        // seleciona todos os campos
        $query = $this->db->table('esm_unidades')
            ->select('esm_unidades.codigo')
            ->where('esm_unidades.id', $unity_id)
            ->get();

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow()->codigo;
    }

    public function get_user_relations($user_id, $type)
    {

        $query = $this->db->query("
            SELECT
                auth_user_relation.{$type}_id
            FROM
                auth_user_relation
            WHERE
                auth_user_relation.user_id = $user_id
            ");

        return $query->getRowArray()[$type . '_id'];

    }

    public function get_groups_for_user($user_id)
    {
        //Query de grupos adicionais
        $query = $this->db->query("
            SELECT
                auth_groups_users.group 
            FROM
                auth_groups_users 
            WHERE
                auth_groups_users.user_id = $user_id 
            AND 
                auth_groups_users.group != 'agua' 
            AND 
                auth_groups_users.group != 'energia'
            AND 
                auth_groups_users.group != 'gas'
            AND 
                auth_groups_users.group != 'nivel'
            AND 
                auth_groups_users.group != 'shopping'
            AND 
                auth_groups_users.group != 'condominio'
            AND 
                auth_groups_users.group != 'industria'
            AND 
                auth_groups_users.group != 'unity'
            AND 
                auth_groups_users.group != 'admin'
            AND 
                auth_groups_users.group != 'group'
        ");

        if ($query->getNumRows() == 0)
            return false;

        foreach ($query->getResult() as $q) {
            $values[] = $q->group;
        }
        return $values;
    }
    public function get_class_by_entity($id)
    {
        $query = $this->db->query("
        SELECT
            esm_entidades.classificacao
        FROM
            esm_entidades
        WHERE
            esm_entidades.id = $id
        ");
        return $query->getRow()->classificacao;
    }
    public function get_entity_by_id($type, $id)
    {
        // seleciona todos os campos
        if ($type == 'agrupamentos') {
            $query = $this->db->query("
        SELECT
            esm_entidades.id
        FROM
            esm_entidades
        LEFT JOIN
            esm_agrupamentos ON esm_agrupamentos.entidade_id = esm_entidades.id
        WHERE
            esm_agrupamentos.id = $id
        ");

            // verifica se retornou algo
            if ($query->getNumRows() == 0)
                return false;

            return $query->getRow()->id;

        } else {
            $query = $this->db->query("
                SELECT
                    esm_entidades.id
                FROM
                    esm_entidades
                    LEFT JOIN
                    esm_agrupamentos
                    ON 
                        esm_agrupamentos.entidade_id = esm_entidades.id
                    INNER JOIN
                    esm_unidades
                    ON 
                        esm_agrupamentos.id = esm_unidades.agrupamento_id
                WHERE
                    esm_unidades.id = $id
                ");

            if ($query->getNumRows() == 0)
                return false;
          
          return $query->getRow()->id;
        }
    }

    public function get_chamados($status = false, $limit = 0)
    {
        $db = Database::connect('easy_com_br');
        $query = $db->table('esm_tickets');
        // aplica filtro pelo status
        if ($status)
            $query->where('status', $status);

        // aplica limite
        if ($limit > 0)
            $query->limit($limit);

        // ordena por data
        $query->orderBy('cadastro', 'DESC');

        // realiza a consulta
        $result = $query->get();

        // verifica se retornou algo
        if ($result->getNumRows() == 0)
            return false;

        return $result->getResult();
    }

    public function get_chamados_novos(){
        // realiza a query via dt
        $dt = $this->datatables->query("
        SELECT
            esm_tickets.id,
            esm_tickets.unidade_id,
            esm_tickets.nome,
            esm_tickets.email,
            esm_tickets.mensagem,
            esm_tickets.STATUS,
            DATE_FORMAT( esm_tickets.cadastro, '%d/%m/%Y' ) AS cadastro,
            esm_departamentos.nome AS departamento,
            MAX(
            DATE_FORMAT( COALESCE ( esm_tickets.fechado_em ), '%d/%m/%Y' )) AS movimento,
            DATE_FORMAT( esm_tickets.fechado_em, '%d/%m/%Y' ) AS fechado_em,
            esm_entidades.tabela AS entidade,
            esm_entidades.nome AS agrupamento,
            esm_unidades.nome AS apto 
        FROM
            esm_tickets
            JOIN esm_departamentos ON esm_tickets.departamento = esm_departamentos.id
            LEFT JOIN esm_unidades ON esm_unidades.id = esm_tickets.unidade_id
            LEFT JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            LEFT JOIN esm_entidades ON esm_entidades.id = esm_agrupamentos.entidade_id 
        GROUP BY
            esm_tickets.id 
        ORDER BY
            COALESCE ( esm_tickets.cadastro ) DESC
        ");

        $dt->add('DT_RowId', function ($data) {
            return $data['id'];
        });

        $dt->add('unidade', function ($data) {
            if (is_null($data['agrupamento'])) {
                return $data['apto'];
            } else {
                return $data['agrupamento'] . "/" . $data['apto'];
            }
        });

        $dt->edit('id', function ($data) {
            return str_pad($data['id'], 5, "0", STR_PAD_LEFT);
        });

        $dt->edit('entidade', function ($data) {
            return ucfirst($data['entidade']);
        });

        $dt->edit('movimento', function ($data) {
            if ($data['status'] == 'fechado')
                return $data['fechado_em'];
            else
                return $data['movimento'];
        });

        $dt->edit('status', function ($data) {
            if ($data['status'] == 'aberto')
                return "<span class=\"badge badge-danger\" style=\"width: 70px;\">Aberto</span>";
            elseif ($data['status'] == 'fechado')
                return "<span class=\"badge badge-success\" style=\"width: 70px;\">Fechado</span>";
            else
                return "<span class=\"badge badge-warning\" style=\"width: 70px;\">" . ucfirst($data['status']) . "</span>";
        });

        // gera resultados
        echo $dt->generate();
    }

    public function get_suporte_now()
    {
     $query = $this->db->query
        ("
        SELECT
            Entidade.classificacao, 
            Entidade.nome AS Entidade, 
            esm_agrupamentos.entidade_id, 
            esm_unidades.agrupamento_id, 
            esm_tickets.id, 
            esm_tickets.nome, 
            esm_tickets.email, 
            esm_tickets.departamento, 
            esm_tickets.assunto, 
            esm_tickets.mensagem, 
            esm_tickets.status, 
            esm_tickets.cadastro, 
            esm_tickets.fechado_por
        FROM
            esm_tickets,
            esm_entidades AS Entidade
            INNER JOIN
            esm_agrupamentos
            ON 
                Entidade.id = esm_agrupamentos.entidade_id
            INNER JOIN
            esm_unidades
            ON 
                esm_agrupamentos.id = esm_unidades.agrupamento_id
    ");


    $result = $query->get();

        // verifica se retornou algo
        return $result->getNumRows();
    }

    

    // **
    // Busca Bloco para Visualizaçao em Modal
    // [post] id do bloco OU 0 para incluir
    // [out] Conteúdo HTML para modal
    // **

    public function new_chamado($user, $assunto, $message)
    {
        $a = '';
        if ($assunto == 's')     $a = 'Sugestão';
        elseif ($assunto == 'd') $a = 'Dúvida';
        elseif ($assunto == 'r') $a = 'Revisão';
        elseif ($assunto == 'v') $a = 'Visita Técnica';
    
        if (empty($user->unidade))
        {
            $uid = 0;
        } else
        {
            $uid = $user->unidade->id;
        }
    
        $data = [
            'user_id' => $user->id,
            'unidade_id' => $uid,
            'departamento' => 2,
            'assunto' => $a,
            'mensagem' => $message
        ];
    
        $this->db->table('esm_tickets')->insert($data);
    
        if ($this->db->affectedRows() > 0)
        {
            $insertId = $this->db->insertID();
            return [
                "status"  => "success",
                "id"      => $insertId,
                "assunto"  => $a,
                "message" => "<strong>Chamado criado com sucesso.</strong><br/>Em até 48hrs entraremos em contato pelo e-mail."
            ];
        } else {
            return [
                "status"  => "error",
                "message" => $this->db->error()
            ];
        }
    }

    public function count_chamados($status = false)
    {
        $db = Database::connect('easy_com_br');
        $query = $db->table('esm_tickets');
        // aplica filtro pelo status
        if ($status)
            $query->where('status', $status);

        // realiza a consulta
        $result = $query->get();

        // verifica se retornou algo
        return $result->getNumRows();
    }

    public function get_log($status = false, $limit = 0)
    {

        $db = Database::connect('easy_com_br');
        $query = $db->table('esm_log');
        // aplica filtro pelo status
        if ($status)
            $query->where('lido', $status);

        // aplica limite
        if ($limit > 0)
            $query->limit($limit);

        // ordena por data
        $query->orderBy('cadastro', 'DESC');

        $result = $query->get();
        // verifica se retornou algo
        if ($result->getNumRows() == 0)
            return false;

        return $result->getNumRows();
    }

    public function count_log($status = -1)
    {
        $db = Database::connect('easy_com_br');
        $query = $db->table('esm_log');
        // aplica filtro pelo status
        if ($status != -1)
            $query->where('lido', $status);

        // realiza a consulta
        $result = $query->get();

        // verifica se retornou algo
        return $result->getNumRows();
    }
    

    public function change_log_state($id)
    {
        $db = Database::connect('easy_com_br');

        if ($id == 0) {
            if ($this->db->query("UPDATE esm_log SET lido = 1"))
                return array("status"  => "success", "message" => "Entradas marcadas com sucesso");
            else
                return array("status"  => "error", "message" => $this->db->error()['message']);
        } else {
            if ($this->db->query("UPDATE esm_log SET lido = IF(lido = 1, 0, 1) WHERE id = $id"))
                return array("status"  => "success", "message" => "Entrada marcada com sucesso");
            else
                return array("status"  => "error", "message" => $this->db->error()['message']);
        }
    }

    public function change_contact_state($id)
    {

        $db = Database::connect('easy_com_br');

        $res = $db->table('esm_contatos')->where('esm_contatos.id', $id)->select('esm_contatos.status')->get()->getRow()->status;
        $result = (intval($res) == 1 ? 0 : 1);

        if (!$db->table('esm_contatos')->set(array('status' => $result))->where('id', $id)->update())
            return array("status" => "error", "message" => $db->error());
        else
            return array("status" => "success", "message" => "Entrada marcada com sucesso");
    }


    public function count_contato($status = -1)
    {

        $db = Database::connect('easy_com_br');
        $query = $db->table('esm_contatos');
        // aplica filtro pelo status
        if ($status != -1)
            $query->where('status', $status);

        // realiza a consulta
        $result = $query->get();

        // verifica se retornou algo
        return $result->getNumRows();
    }
    public function get_ultima_leitura($central, $tabela)
    {
        $db = Database::connect('easy_com_br');

        $query = $db->query("
            SELECT IFNULL(DATE_FORMAT(FROM_UNIXTIME(MAX(timestamp)), '%d/%m/%Y %H:%i:%s'), '-') AS ultima_leitura
            FROM esm_leituras_{$tabela}_agua
            WHERE medidor_id = (SELECT id FROM esm_medidores WHERE central = '$central' LIMIT 1)
        ");

        return $query->getRow()->ultima_leitura;
    }
    public function get_last_leitura($central, $tabela)
    {
        $db = Database::connect('easy_com_br');
        $query = $db->query("
            SELECT MAX(timestamp) AS leitura 
            FROM esm_leituras_{$tabela}_agua 
            WHERE medidor_id = (SELECT id 
                FROM esm_medidores
                WHERE central = '$central'
                LIMIT 1)
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow()->leitura;
    }
    public function get_bateria($id)
    {
        $query = $this->db->query("
            SELECT tensao
            FROM esm_bateria
            WHERE medidor_id = $id
            ORDER BY timestamp
            LIMIT 10
        ");

        if ($query->getNumRows() == 0)
            return "";

        foreach (($query->getResult()) as $r) {
            $res[] = number_format($r->tensao * 4 / 1023, 2);
        }

        return implode(",", $res);
    }
    public function get_post($id)
    {
        $db = Database::connect('easy_com_br');
        $query = $db->query("
            SELECT text, header
            FROM post
            WHERE id = $id
        ");

        return $query->getRow();
    }
    public function get_data_raw($id)
    {
        $db = Database::connect('easy_com_br');
        $query = $db->query("
            SELECT 
                payload AS text, 
                header
            FROM post_raw
            WHERE id = $id
        ");

        return $query->getRow();
    }
    public function get_medidores_central($central)
    {
        $query = $this->db->query("
            SELECT LPAD(id, 6, '0') AS id, posicao
            FROM esm_medidores
            WHERE central = '$central'
            ORDER BY posicao
        ");

        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }
    public function add_calibracao_fase($processo, $fase, $data)
    {
        $failure = array();
        $this->db->transStart();

        foreach ($data as $k => $d) {
            if (!$this->db->table('esm_calibracao')->set(array('leitura' . $fase => $d))->where('processo', $processo)->where('porta', $k)->update()) {
                $failure[] = $this->db->error();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "message" => "Fase cadastrada com sucesso!"));
        }
    }

    public function save_calibracao($processo, $sensores)
    {
        $failure = array();
        $this->db->transStart();

        $sensor = $this->db->query("SELECT MAX(id) AS id FROM esm_sensores_agua")->getRow()->id + 1;

        foreach ($sensores as $s) {

            $res = $this->db->query("
                UPDATE esm_calibracao
                SET serial = $sensor, fator = (leitura1a + leitura1b + leitura1c + leitura2a + leitura2b + leitura2c + leitura3a + leitura3b + leitura3c + leitura4a + leitura4b + leitura4c + leitura5a + leitura5b + leitura5c) / 15, cadastro = " . date("Y-m-d H:i:s", strtotime()) . "
                WHERE processo = $processo AND porta = $s
            ");

            if (!$res) {
                $failure[] = $this->db->error();
            }
            $sensor++;
        }

        // coloca 0 no sensor que não foi serializado
        if (!$this->db->table('esm_calibracao')->set(array('serial' => 0)->where(array('processo', $processo)->where('serial', null)))->update())
            $failure[] = $this->db->error();

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "message" => "mmmm"));
        }
    }

    public function get_calibracao_processo($processo)
    {
        $query = $this->db->query("
            SELECT *
            FROM esm_calibracao
            WHERE processo = $processo
            LIMIT 1
        ");

        return $query->getRow();
    }

    public function get_calibracao_fase($processo)
    {
        $fases = array('1a', '1b', '1c', '2a', '2b', '2c', '3a', '3b', '3c', '4a', '4b', '4c', '5a', '5b', '5c');
        $query = $this->db->query("
            SELECT *
            FROM esm_calibracao
            WHERE processo = $processo
            LIMIT 1
        ")->getRowArray();

        foreach ($fases as $f) {
            if (is_null($query['leitura' . $f]))
                return $f;
        }

        // acabou ciclo
        return false;
    }

    public function get_calibracao($processo)
    {
        $query = $this->db->query("
            SELECT *
            FROM esm_calibracao
            WHERE processo = $processo
            ORDER BY porta
        ");

        return $query->getResult();
    }

    public function start_calibracao()
    {
        $processo = $this->db->query("SELECT MAX(processo) AS processo FROM esm_calibracao WHERE cadastro IS NOT NULL")->getRow()->processo;
        $processo++;

        $failure = array();
        $this->db->transStart();

        for ($i = 1; $i < 65; $i++) {
            if (!$this->db->table('esm_calibracao')->set(array('processo' => $processo, 'porta' => $i))->insert()) {
                $failure[] = $this->db->error();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "message" => "Calibração Iniciada", "processo" => $processo));
        }
    }

    public function count_alerta_nao_lido($user_id)
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT COUNT(id) AS counter 
            FROM esm_alertas_envios 
            WHERE 
                user_id = $user_id AND 
                lida IS NULL AND
                visibility = 'normal'
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return 0;

        return $query->getRow()->counter;
    }

    public function _count_alerta_nao_lido($user_id)
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT COUNT(id) AS counter 
            FROM esm_alertas_envios 
            WHERE 
                MD5(CONCAT('easymeter', user_id, '123456')) = '$user_id' AND 
                lida IS NULL AND
                visibility = 'normal'
            ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return 0;

        return $query->getRow()->counter;
    }

    public function add_aviso($titulo, $texto, $email)
    {
        // user id
        $user_id = $this->ion_auth->user()->row()->id;
        // busca condo id
        $condo_id =  $this->get_user_entidade($user_id)->id;

        // cria registro do aviso
        $data = array(
            "tipo"        => 'aviso',
            "titulo"      => $titulo,
            "texto"          => $texto,
            "enviado_por" => $user_id,
            "email"       => ($email == 'true') ? 1 : 0
        );

        // insere mensagem em esm_alertas
        if (!$this->db->table('esm_alertas')->set($data)->insert())
            echo json_encode(array("status"  => "error", "message" => 'error'));
        else
            echo json_encode(array("status"  => "success", "message" => "Alerta cadastrado com sucesso!", "id"));
    }

    public function delete_alerta($id, $box, $adm)
    {
        if ($box == 'in') {
            if ($adm) {
                if (!$this->db->table('esm_alertas')->set(array('visibility' => 'delbyadmin'))->where('id', $id)->update()) {
                    echo json_encode(array("status"  => "error", "message" => $this->db->error()));
                    return;
                }
            } else {
                if (!$this->db->table('esm_alertas_envios')->set(array('visibility' => 'delbyuser'))->where('id', $id)->update()) {
                    echo json_encode(array("status"  => "error", "message" => $this->db->error()));
                    return;
                }
            }
            echo json_encode(array("status"  => "success", "message" => "Alerta excluído com sucesso."));
        } else if ($box == 'out') {
            //verifica se é para apagar ou marcar registro
            $query = $this->db->query("SELECT id FROM esm_alertas WHERE id = $id AND enviada IS NULL")->getNumRows();

            if ($query > 0) {
                // apagar
                if (!$this->db->delete('esm_alertas', array('id' => $id, 'enviada' => NULL))) {
                    echo json_encode(array("status"  => "error", "message" => $this->db->error()));
                    return;
                }
                echo json_encode(array("status"  => "success", "message" => "Alerta excluído com sucesso"));
            } else {
                // marcar como excluido
                if (!$this->db->table('esm_alertas')->set(array('visibility' => 'delbyuser'))->where('id', $id)->update()) {
                    echo json_encode(array("status"  => "error", "message" => $this->db->error()));
                    return;
                }
                echo json_encode(array("status"  => "success", "message" => "Alerta excluído com sucesso"));
            }
        } else {
            echo json_encode(array("status"  => "error", "message" => 'Parâmetro incorreto.'));
        }
    }

    // public function finish_calibracao($processo)
    // {
    //     $failure = array();
    //     $this->db->transStart();

    //     $sensores = $this->db->query("
    //         SELECT serial AS id, fator, NOW() AS calibracao 
    //         FROM esm_calibracao
    //         WHERE processo = $processo AND serial != 0
    //         ORDER BY serial
    //     ")->getResult();

    //     if (!$this->db->insert_batch('esm_sensores_agua', $sensores)) {
    //         $failure[] = $this->db->error();
    //     }

    //     $this->db->transComplete();

    //     if ($this->db->transStatus() === FALSE) {
    //         return json_encode(array("status"  => "error", "message" => $failure[0]));
    //     } else {
    //         return json_encode(array("status"  => "success", "message" => "Sensores salvos", "data" => $sensores));
    //     }
    // }

}