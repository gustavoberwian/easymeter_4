<?php

namespace App\Models;

class Shopping_model extends Base_model
{
    public function get_unidades($group_id)
    {
        $query = "
            SELECT
                esm_unidades.id AS unidade_id,
                esm_medidores.id as medidor_id,
                esm_unidades.nome AS unidade_nome,
            CONCAT(
                    esm_agrupamentos_config.logradouro,
                    ', ',
                    esm_agrupamentos_config.numero,
                    ' - ',
                    esm_agrupamentos_config.bairro,
                    ', ',
                    esm_agrupamentos_config.cidade,
                    ' - ',
                    esm_agrupamentos_config.uf 
                ) as endereco
            FROM
                esm_unidades
                JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
                JOIN esm_agrupamentos_config ON esm_agrupamentos_config.agrupamento_id = esm_agrupamentos.id
                JOIN esm_medidores ON esm_unidades.id = esm_medidores.unidade_id
            WHERE
                esm_unidades.agrupamento_id = $group_id
        ";
        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0) {

            return false;
        }

        return $result->getResult();
    }

    public function get_unidade($id)
    {
        $query = "
            SELECT
                esm_unidades.id as id,
                esm_unidades.nome,
                esm_medidores.id as medidor_id,
                esm_medidores.nome as device
            FROM
                esm_unidades
            JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades.id
            WHERE
                esm_unidades.id = $id
        ";
        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0) {

            return false;
        }

        return $result->getRow();
    }

    public function get_user_relation($user)
    {
        $query = $this->db->query("
            SELECT 
                * 
            FROM 
                auth_user_relation
            WHERE 
                user_id = $user
        ");

        if ($query->getNumRows() <= 0)
            return false;

        if (is_null($query->getRow()->entidade_id) && is_null($query->getRow()->agrupamento_id))
            return (object) array("type" => "unity", "unity_id" => $query->getRow()->unidade_id);

        if (is_null($query->getRow()->agrupamento_id) && is_null($query->getRow()->unidade_id))
            return (object) array("type" => "entity", "entity_id" => $query->getRow()->entidade_id);

        if (is_null($query->getRow()->entidade_id) && is_null($query->getRow()->unidade_id))
            return (object) array("type" => "group", "group_id" => $query->getRow()->agrupamento_id);

        return $query->getRow();
    }

    public function get_groups_by_entity($entity)
    {
        $query = "
            SELECT 
                esm_agrupamentos.nome as nome,
                esm_entidades.tipo as tipo,
                CONCAT(esm_agrupamentos_config.logradouro, ', ', esm_agrupamentos_config.numero) as endereco,
                CONCAT(esm_agrupamentos_config.cidade, ', ', esm_agrupamentos_config.uf) as municipio,
                esm_agrupamentos_config.* 
            FROM 
                esm_agrupamentos_config
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_agrupamentos_config.agrupamento_id
            JOIN esm_entidades ON esm_entidades.id = esm_agrupamentos.entidade_id
            WHERE 
                esm_agrupamentos.entidade_id = $entity
        ";

        if ($this->db->query($query)->getNumRows() <= 0)
            return array();

        return $this->db->query($query)->getResult();
    }

    public function get_client_config($gid)
    {
        $result = $this->db->query("
            SELECT 
                *
            FROM esm_client_config
            WHERE 
                agrupamento_id = '$gid'
        ");

        if ($result->getnumRows()) {
            return $result->getRow();
        }

        return false;
    }

    public function get_group_info($group_id)
    {
        $query = $this->db->query(
            "SELECT 
                    esm_entidades.id as entity_id, 
                    esm_entidades.nome as entity_name, 
                    esm_agrupamentos.id as group_id, 
                    esm_agrupamentos.nome as group_name,
                    esm_agrupamentos.logo as logo,
                    esm_agrupamentos_config.m_agua,
                    esm_agrupamentos_config.m_energia,
                    esm_agrupamentos_config.m_gas,
                    esm_agrupamentos_config.m_nivel
                FROM esm_agrupamentos_config
                JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_agrupamentos_config.agrupamento_id
                JOIN esm_entidades ON esm_entidades.id = esm_agrupamentos.entidade_id
                WHERE esm_agrupamentos.id = $group_id
        "
        );

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        // Retorna result
        return $query->getRow();
    }

    public function get_units($eid, $tipo = null)
    {
        $t = "";
        if (!is_null($tipo)) {
            $t = " AND esm_medidores.tipo = '$tipo' ";
        }
        $result = $this->db->query("
            SELECT
                esm_unidades.id AS id,
                esm_medidores.nome as medidor_id,
                esm_unidades.nome AS unidade_nome,
                esm_unidades.tipo AS unidade_tipo,
                esm_unidades.andar AS unidade_localizacao
            FROM
                esm_unidades
            JOIN 
                esm_medidores ON esm_unidades.id = esm_medidores.unidade_id
            JOIN 
                esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
            WHERE 
                esm_unidades.agrupamento_id = $eid
                $t
            ORDER BY 
                esm_unidades.nome
        ");

        return $result->getResultArray();
    }

    public function get_device_groups($group_id, $m)
    {
        $result = $this->db->query("
            SELECT
                * 
            FROM
                esm_device_groups 
            JOIN esm_medidores ON esm_medidores.entrada_id = esm_device_groups.entrada_id
            JOIN esm_unidades ON esm_medidores.unidade_id = esm_unidades.id
            WHERE
                esm_unidades.agrupamento_id = $group_id AND esm_medidores.tipo = '$m'
            GROUP BY NAME
            ORDER BY NAME
        ");

        if ($result->getRow()) {
            return $result->getResultArray();
        }

        return array();
    }

    public function get_user_permission($uid)
    {
        //$this->setHistory("Requisição das permissões do usuário $uid", 'requisição');

        return $this->db->query("
            SELECT
                acessar_lancamentos,
                acessar_engenharia,
                baixar_planilhas
            FROM
                auth_users
            WHERE
                id = $uid")->getRow();
    }

    public function GetGroup($gid)
    {
        $result = $this->db->query("
            SELECT 
                esm_agrupamentos.nome, 
                esm_agrupamentos_config.*
            FROM 
                esm_agrupamentos_config
            JOIN 
                esm_agrupamentos ON esm_agrupamentos.id = esm_agrupamentos_config.agrupamento_id
            WHERE 
                agrupamento_id = $gid
        ");

        if ($result->getNumRows()) {
            return $result->getRow();
        }

        return false;
    }

    public function GetFechamento($type, $fid)
    {
        $result = $this->db->query("
            SELECT 
                *
            FROM 
                esm_fechamentos_{$type}
            WHERE 
                id = $fid
        ");

        if ($result->getNumRows()) {
            return $result->getRow();
        }

        return false;
    }

    public function GetFechamentoUnidade($type, $rid)
    {
        $result = $this->db->query("
            SELECT 
                esm_unidades.nome, 
                esm_unidades_config.tipo,
                esm_fechamentos_{$type}_entradas.* 
            FROM 
                esm_fechamentos_{$type}_entradas
            JOIN
                esm_medidores ON esm_medidores.nome = esm_fechamentos_{$type}_entradas.device
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            LEFT JOIN 
                esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            WHERE 
                esm_fechamentos_{$type}_entradas.id = $rid
        ");

        if ($result->getNumRows()) {
            return $result->getRow();
        }

        return false;
    }

    public function GetFechamentoHistoricoUnidade($type, $device, $date)
    {
        $result = $this->db->query("
            SELECT 
                esm_unidades.nome, 
                esm_fechamentos_{$type}_entradas.*,
                esm_fechamentos_{$type}.competencia
            FROM 
                esm_fechamentos_{$type}_entradas
            JOIN 
                esm_fechamentos_{$type} ON esm_fechamentos_{$type}.id = esm_fechamentos_{$type}_entradas.fechamento_id
            JOIN
                esm_medidores ON esm_medidores.nome = esm_fechamentos_{$type}_entradas.device
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE 
                esm_fechamentos_{$type}_entradas.device = '$device' AND esm_fechamentos_{$type}.cadastro < '$date'
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
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

    public function get_alert_config($group, $grp = false)
    {
        $gr = "";
        if ($grp) {
            $gr = " GROUP BY type ";
        }
        $result = $this->db->query(
            "
            SELECT
                esm_alertas_cfg.*
            FROM
                esm_alertas_cfg
            WHERE
                esm_alertas_cfg.agrupamento_id = $group
            $gr"
        );

        if ($result->getNumRows())
            return $result->getResult();

        return array();
    }

    public function get_devices($group, $type)
    {
        $result = $this->db->query(
            "
            SELECT
                esm_alertas_cfg_devices.device
            FROM
                esm_alertas_cfg
	    JOIN esm_alertas_cfg_devices ON esm_alertas_cfg_devices.config_id = esm_alertas_cfg.id
            WHERE
                esm_alertas_cfg.agrupamento_id = $group AND esm_alertas_cfg.type = $type"
        );

        if ($result->getNumRows()) {
            $list = array();
            foreach ($result->getResult() as $d) {
                $list[] = $d->device;
            }

            return $list;
        }

        return false;
    }

    public function getToken($group_id)
    {
        // seleciona todos os campos
        $query = $this->db->query("
            SELECT token
            FROM esm_api_keys
            WHERE agrupamento_id = $group_id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow()->token;
    }

    public function GetUserAlert($id, $monitoramento = null, $readed = false)
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

    public function DeleteAlert($id, $monitoramento = null)
    {
        if (!$this->db->table('esm_alertas_' . $monitoramento . '_envios')->where(array('id' => $id))->set(array('visibility' => 'delbyuser'))->update()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Alerta excluído com sucesso.", "id" => $id));
    }

    public function ReadAllAlert($user_id, $monitoramento = null)
    {
        // atualiza data
        $this->db->transStart();

        $this->db->table('esm_alertas_' . $monitoramento . '_envios')
            ->where('user_id', $user_id)
            ->where('lida', NULL)
            ->set(array('lida' => date("Y-m-d H:i:s")))
            ->update();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Alertas marcados com sucesso."));
    }

    public function get_devices_agrupamento($id)
    {
        $result = $this->db->query(
            "
            SELECT
                esm_device_groups_entries.device as dvc,
                esm_unidades.nome
            FROM 
                esm_device_groups_entries
            JOIN esm_medidores ON esm_medidores.nome = esm_device_groups_entries.device
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE esm_device_groups_entries.agrupamento_id = $id"
        );

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function get_devices_alert($group, $type)
    {
        $result = $this->db->query(
            "
            SELECT
                esm_alertas_cfg_devices.device as dvc,
                esm_unidades.nome as nome
            FROM
                esm_alertas_cfg
                JOIN esm_alertas_cfg_devices ON esm_alertas_cfg_devices.config_id = esm_alertas_cfg.id
                JOIN esm_medidores ON esm_medidores.nome = esm_alertas_cfg_devices.device
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE
                esm_alertas_cfg.agrupamento_id = $group AND esm_alertas_cfg_devices.config_id = $type"
        );

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function edit_client_conf($dados)
    {
        $this->db->transStart();

        foreach ($dados['tabela'] as $tabela => $campos) {

            if (
                $this->db->table($tabela)

                    ->where('agrupamento_id', $dados['group_id'])
                    ->get()->getNumRows()
            ) {
                $this->db->table($tabela)
                    ->where('agrupamento_id', $dados['group_id'])
                    ->set($campos)
                    ->update();
            } else {

                $campos['agrupamento_id'] = $dados['group_id'];
                $this->db->table($tabela)
                    ->set($campos)
                    ->insert();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false)
            return false;

        return true;
    }

    public function get_shoppings_by_user($user_id)
    {
        $query = "
            SELECT
                esm_agrupamentos_config.*,
                esm_agrupamentos.nome AS nome 
            FROM
                esm_agrupamentos_config
                JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_agrupamentos_config.agrupamento_id
                JOIN esm_entidades ON esm_entidades.id = esm_agrupamentos.entidade_id
                JOIN auth_user_relation ON auth_user_relation.entidade_id = esm_entidades.id 
            WHERE
                auth_user_relation.user_id = $user_id";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getResult();
    }

    public function get_user_info($user_id)
    {
        $users = model('UserModel');

        $user = $users->findById($user_id);

        if ($user->inGroup("unity", "shopping")) {

            return $this->db->table('auth_users')
                ->select("auth_users.*, esm_unidades.id as unidade_id, esm_unidades.nome as unidade_nome, esm_agrupamentos.id as agrupamento_id, esm_agrupamentos.nome as bloco_nome")
                ->join("auth_user_relation", "auth_user_relation.user_id = auth_users.id")
                ->join("esm_unidades", "esm_unidades.id = auth_user_relation.unity_id")
                ->join("esm_agrupamentos", "esm_agrupamentos.id = esm_unidades.agrupamento_id")
                ->where("auth_users.id", $user_id)
                ->get();
        } elseif ($user->inGroup("shopping", "shopping")) {

            return $this->db->table('auth_users')
                ->select("auth_users.*, esm_agrupamentos.id as agrupamento_id, esm_agrupamentos.nome as bloco_nome")
                ->join("auth_user_relation", "auth_user_relation.user_id = auth_users.id")
                ->join("esm_agrupamentos", "esm_agrupamentos.id = auth_user_relation.agrupamento_id")
                ->where("auth_users.id", $user_id)
                ->get();
        } else {

            return $this->db->table('auth_users')
                ->select("auth_users.*")
                ->join("auth_user_relation", "auth_user_relation.user_id = auth_users.id")
                ->join("esm_entidades", "esm_entidades.id = auth_user_relation.entidade_id")
                ->where("auth_users.id", $user_id)
                ->get();
        }
    }

    public function insertToken($token, $group_id)
    {
        $this->db->transStart();

        $q = $this->db->table('esm_api_keys')
            ->where('agrupamento_id', $group_id)
            ->get();

        if ($q->getNumRows() > 0) {
            $this->db->table('esm_api_keys')
                ->where('agrupamento_id', $group_id)
                ->set(array("token" => $token))
                ->update();
        } else {
            $this->db->table('esm_api_keys')
                ->set(array("agrupamento_id" => $group_id, "token" => $token))
                ->insert();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return true;
    }

    public function edit_agrupamento($dados)
    {
        $this->db->transStart();

        $this->db->table('esm_device_groups_entries')
            ->where("agrupamento_id", $dados['id'])
            ->delete();

        if ($dados['devices']) {
            foreach ($dados['devices'] as $dvc) {
                $this->db->table("esm_device_groups_entries")
                    ->set(array('agrupamento_id' => $dados['id'], 'device' => $dvc))
                    ->insert();
            }
        }

        $this->db->table('esm_device_groups')
            ->where("id", $dados['id'])
            ->set(array('name' => $dados['name']))
            ->update();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return true;
    }

    public function delete_agrupamento($id)
    {
        $this->db->transStart();

        $this->db->table("esm_device_groups_entries")
            ->where("agrupamento_id", $id)
            ->delete();

        $this->db->table("esm_device_groups")
            ->where("id", $id)
            ->delete();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return true;
    }

    public function add_agrupamento($dados)
    {
        $this->db->transStart();

        $this->db->table("esm_device_groups")
            ->set(array("entrada_id" => $dados['entrada_id'], "name" => $dados['name']))
            ->insert();

        $inserted = $this->db->insertID();

        if ($dados['devices']) {
            foreach ($dados['devices'] as $dvc) {
                $this->db->table("esm_device_groups_entries")
                    ->set(array('agrupamento_id' => $inserted, 'device' => $dvc))
                    ->insert();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return true;
    }

    public function get_subtipo_cliente_config($grp, $type = 'energia')
    {
        $query = "
            SELECT
                IF(unc.type <= 1,(
                    SELECT esm_client_config.area_comum 
                    FROM esm_client_config 
                    WHERE esm_client_config.agrupamento_id = $grp
                ),'Unidades') as subtipo
            FROM esm_medidores me
            JOIN esm_unidades un ON un.id = me.unidade_id
            JOIN esm_unidades_config unc ON unc.unidade_id = un.id
            WHERE un.agrupamento_id = $grp AND me.tipo = '$type'
        ";

        return $this->db->query($query)->getRow()->subtipo;
    }

    public function edit_unidade($dados)
    {
        $this->db->transStart();

        foreach ($dados['tabela'] as $tabela => $campos) {
            if ($tabela === 'esm_unidades') {
                $this->db->table($tabela)
                    ->where('id', $dados['unidade_id'])
                    ->set($campos)
                    ->update();
            } elseif ($tabela === 'esm_unidades_config') {
                $this->db->table($tabela)
                    ->where('unidade_id', $dados['unidade_id'])
                    ->set($campos)
                    ->update();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false)
            return false;

        return true;
    }

    public function edit_alert_conf($dados)
    {
        $this->db->transStart();

        $this->db->table("esm_alertas_cfg_devices")
            ->where("config_id", $dados['config_id'])
            ->delete();

        if ($dados['esm_alertas_cfg_devices']) {
            foreach ($dados['esm_alertas_cfg_devices'] as $dvc) {
                $this->db->table('esm_alertas_cfg_devices')
                    ->set(array('config_id' => $dados['config_id'], 'device' => $dvc))
                    ->insert();
            }
        }

        $this->db->table('esm_alertas_cfg')
            ->where("agrupamento_id", $dados['group_id'])
            ->where("id", $dados['config_id'])
            ->set(
                array(
                    'when_type' => $dados['esm_alertas_cfg']['when_type'],
                    'notify_unity' => $dados['esm_alertas_cfg']['notify_unity'],
                    'notify_shopping' => $dados['esm_alertas_cfg']['notify_shopping'],
                    'active' => $dados['esm_alertas_cfg']['active'],
                )
            )
            ->update();

        $this->db->transComplete();

        if ($this->db->transStatus() === false)
            return false;

        return true;
    }

    public function delete_user($user)
    {
        $status = true;
        $users = model('UserModel');
        if (!$users->delete($user, true)) {
            $status = false;
        }

        return $status;
    }

    public function get_lojas_by_shopping($shopping_id)
    {
        $query = "
            SELECT esm_unidades.*
            FROM esm_unidades
            JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades.id
            WHERE esm_unidades.agrupamento_id = $shopping_id";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getResult();
    }

    public function get_condo($condo)
    {
        $query = "
            SELECT *
            FROM esm_entidades
            WHERE id = $condo";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_condo_by_group($group)
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

    public function get_condo_by_unity($unity)
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

    public function get_unity_by_user($user)
    {
        $query = "
            SELECT esm_unidades.*
            FROM esm_unidades
            JOIN auth_user_relation ON auth_user_relation.unidade_id = esm_unidades.id
            WHERE auth_user_relation.user_id = $user";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_group_by_unity($unity)
    {
        $query = "
            SELECT esm_agrupamentos_config.*
            FROM esm_agrupamentos_config
            JOIN esm_unidades ON esm_unidades.agrupamento_id = esm_agrupamentos_config.agrupamento_id
            WHERE esm_unidades.id = $unity";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_group_by_fechamento($id)
    {
        $query = "
        SELECT * 
        FROM `esm_fechamentos_energia` 
        WHERE id = $id";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }

    public function get_group_by_user($user)
    {
        $query = "
            SELECT 
                esm_agrupamentos_config.* 
            FROM 
                esm_agrupamentos_config
            JOIN auth_user_relation ON auth_user_relation.agrupamento_id = esm_agrupamentos_config.agrupamento_id
            WHERE 
                auth_user_relation.user_id = $user
        ";

        if ($this->db->query($query)->getNumRows() <= 0)
            return array();

        return $this->db->query($query)->getRow();
    }



    public function update_user($user_id, $password, $telefone, $celular, $emails)
    {
        $users = model('userModel');
        $user = $users->findById($user_id);


        $user->fill([
            'password' => $password
        ]);
        $users->save($user);


        if ($emails) {
            foreach ($emails as $e) {
                $query = $this->db->query("
                    SELECT
                        esm_user_emails.email
                    FROM
                        esm_user_emails
                    WHERE
                        esm_user_emails.email = '$e'
                ");
                if ($query->getNumRows() == 0) {
                    $this->db->table('esm_user_emails')
                        ->set('user_id', $user_id)
                        ->set('email', $e)
                        ->insert();
                }
            }
        }
        if (!$emails) {
            $this->db->table("esm_user_emails")
                ->where("user_id", $user_id)
                ->delete();

        }
        $query = $this->db->query("
            SELECT
	            auth_users.celular
            FROM
	            auth_users
        ");

        $this->db->table('auth_users')
            ->where('id', $user_id)
            ->set('celular', $celular)
            ->update();


        $query = $this->db->query("
        SELECT
            auth_users.telefone
        FROM
            auth_users
    ");

        $this->db->table('auth_users')
            ->where('id', $user_id)
            ->set('telefone', $telefone)
            ->update();

        return true;
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

    public function get_unidade_id($unidade)
    {
        $query = "
            SELECT
                esm_unidades.id 
            FROM
                esm_unidades
            WHERE
                esm_unidades.nome = '$unidade'
        ";
        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0) {

            return false;
        }

        return $result->getRow()->id;
    }
    public function GetUnidadeByDevice($device)
    {
        $query = "
            SELECT
                esm_unidades.nome
            FROM
                esm_unidades
            JOIN 
                esm_medidores on esm_medidores.unidade_id = esm_unidades.id
            WHERE
                esm_medidores.nome = '$device'
        ";
        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0) {

            return false;
        }

        return $result->getRow();
    }
    public function get_entrada_id($entidade, $type)
    {
        $query = "SELECT 
        esm_entradas.id
    FROM 
        esm_entradas
    WHERE
        esm_entradas.entidade_id = '$entidade' AND
        esm_entradas.tipo = '$type'";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0) {

            return false;
        }

        return $result->getRow()->id;
    }
}