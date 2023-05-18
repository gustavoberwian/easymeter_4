<?php

namespace App\Models;

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
        $query = $this->db->query("
            SELECT DATE_FORMAT(FROM_UNIXTIME(timestamp),'%d/%m/%Y') AS label, COUNT(*) AS leituras
            FROM esm_leituras_{$tabela}_agua
            WHERE timestamp >= UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 40 DAY)) AND medidor_id = (SELECT id FROM esm_medidores WHERE central = '$central' LIMIT 1)
            GROUP BY year(FROM_UNIXTIME(`timestamp`)), month(FROM_UNIXTIME(`timestamp`)), day(FROM_UNIXTIME(`timestamp`))
        ");

        $values = array();
        if ($query->getNumRows() == 0)
            return false;

        foreach ($query->getResult() as $r) {
            $values[$r->label] = $r->leituras;
        }

        return $values;
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

        if ($query->getNumRows() == 0) 
        {
            return json_encode(array("status" => "success", "message" => "Usuário editado com sucesso."));
        } else 
        {
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
    public function get_class_by_entity($id) {
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
}