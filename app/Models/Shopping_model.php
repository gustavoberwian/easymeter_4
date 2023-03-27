<?php

namespace App\Models;

class Shopping_model extends Base_model
{
    public function get_unidades($group_id)
    {        $query = "
            SELECT
                esm_unidades.id AS unidade_id,
                esm_medidores.id as medidor_id,
                esm_unidades.nome AS unidade_nome,
            CONCAT(
                    esm_shoppings.logradouro,
                    ', ',
                    esm_shoppings.numero,
                    ' - ',
                    esm_shoppings.bairro,
                    ', ',
                    esm_shoppings.cidade,
                    ' - ',
                    esm_shoppings.uf 
                ) as endereco
            FROM
                esm_unidades
                JOIN esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
                JOIN esm_shoppings ON esm_shoppings.bloco_id = esm_blocos.id
                JOIN esm_medidores ON esm_unidades.id = esm_medidores.unidade_id
            WHERE
                esm_unidades.bloco_id = $group_id
        ";
        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0) {

            return false;
        }

        return $result->getResult();
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

        if (is_null($query->getRow()->entity_id) && is_null($query->getRow()->group_id))
            return (object) array("type" => "unity", "unity_id" => $query->getRow()->unity_id);

        if (is_null($query->getRow()->group_id) && is_null($query->getRow()->unity_id))
            return (object) array("type" => "entity", "entity_id" => $query->getRow()->entity_id);

        if (is_null($query->getRow()->entity_id) && is_null($query->getRow()->unity_id))
            return (object) array("type" => "group", "group_id" => $query->getRow()->group_id);

        return $query->getRow();
    }

    public function get_groups_by_entity($entity)
    {
        $query = "
            SELECT 
                esm_blocos.nome as nome, esm_shoppings.* 
            FROM 
                esm_shoppings
            JOIN esm_blocos ON esm_blocos.id = esm_shoppings.bloco_id
            WHERE 
                esm_blocos.condo_id = $entity
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
                group_id = '$gid'
        ");

        if ($result->getnumRows()) {
            return $result->getRow();
        }

        return array();
    }

    public function get_group_info($group_id)
    {
        $query = $this->db->query(
            "SELECT 
                    esm_condominios.id as entity_id, 
                    esm_condominios.nome as entity_name, 
                    esm_blocos.id as group_id, 
                    esm_blocos.nome as group_name,
                    esm_shoppings.m_agua,
                    esm_shoppings.m_energia,
                    esm_shoppings.m_gas,
                    esm_shoppings.m_nivel
                FROM esm_shoppings
                JOIN esm_blocos ON esm_blocos.id = esm_shoppings.bloco_id
                JOIN esm_condominios ON esm_condominios.id = esm_blocos.condo_id
                WHERE esm_blocos.id = $group_id
        ");

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
            $t = " AND esm_entradas.tipo = '$tipo' ";
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
                esm_unidades.bloco_id = $eid
                $t
            ORDER BY 
                esm_unidades.nome
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return array();
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
                esm_unidades.bloco_id = $group_id AND esm_medidores.tipo = '$m'
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
                users
            WHERE
                id = $uid")->getRow();
    }

    public function GetGroup($gid)
    {
        $result = $this->db->query("
            SELECT 
                esm_blocos.nome, 
                esm_shoppings.*
            FROM 
                esm_shoppings
            JOIN 
                esm_blocos ON esm_blocos.id = esm_shoppings.bloco_id
            WHERE 
                bloco_id = $gid
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
        $result = $this->db->query("
            SELECT
                esm_alertas_cfg.*
            FROM
                esm_alertas_cfg
            WHERE
                esm_alertas_cfg.group_id = $group
            $gr"
        );

        if ($result->getNumRows())
            return $result->getResult();

        return array();
    }

    public function get_devices($group, $type)
    {
        $result = $this->db->query("
            SELECT
                esm_alertas_cfg_devices.device
            FROM
                esm_alertas_cfg
	    JOIN esm_alertas_cfg_devices ON esm_alertas_cfg_devices.config_id = esm_alertas_cfg.id
            WHERE
                esm_alertas_cfg.group_id = $group AND esm_alertas_cfg.type = $type"
        );

        if ($result->getNumRows()) {
            $list = array();
            foreach($result->getResult() as $d) {
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
            WHERE group_id = $group_id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow()->token;
    }

    public function GetUserAlert($id, $monitoramento = null, $readed = false)
    {
        $m = "";
        if (!is_null($monitoramento)) {
            if ($monitoramento === 'energia')
                $m = "_" . $monitoramento;
            elseif ($monitoramento === 'agua')
                $m = $monitoramento;
        }
        $query = $this->db->query("
            SELECT 
                esm_alertas" . $m . ".tipo, 
                esm_alertas" . $m . ".titulo, 
                esm_alertas" . $m . ".texto, 
                COALESCE(esm_alertas" . $m . ".enviada, 0) AS enviada,
                COALESCE(esm_alertas" . $m . "_envios.lida, '') AS lida
            FROM esm_alertas" . $m . "_envios
            JOIN esm_alertas" . $m . " ON esm_alertas" . $m . ".id = esm_alertas" . $m . "_envios.alerta_id
            WHERE esm_alertas" . $m . "_envios.id = $id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        $ret = $query->getRow();

        if ($readed) {
            // atualiza esm_alertas
            $this->db->table('esm_alertas' . $m . '_envios')
                ->where('id', $id)
                ->where('lida', NULL)
                ->set(array('lida' => date("Y-m-d H:i:s")))
                ->update();
        }

        return $ret;
    }

    public function DeleteAlert($id, $monitoramento = null)
    {
        $m = "";
        if (!is_null($monitoramento)) {
            if ($monitoramento === 'energia')
                $m = "_" . $monitoramento;
            elseif ($monitoramento === 'agua')
                $m = $monitoramento;
        }

        if (!$this->db->table('esm_alertas' . $m . '_envios')->where(array('id' => $id))->set(array('visibility' => 'delbyuser'))->update()) {
            echo json_encode(array("status" => "error", "message" => $this->db->error()));
            return;
        }

        echo json_encode(array("status" => "success", "message" => "Alerta excluído com sucesso.", "id" => $id));
    }
    public function ReadAllAlert($user_id, $monitoramento = null)
    {
        $m = "";
        if (!is_null($monitoramento)) {
            if ($monitoramento === 'energia')
                $m = "_" . $monitoramento;
            elseif ($monitoramento === 'agua')
                $m = $monitoramento;
        }

        // atualiza data
        $this->db->transStart();

        $this->db->table('esm_alertas' . $m . '_envios')
            ->where('user_id', $user_id)
            ->where('lida', NULL)
            ->set(array('lida' => date("Y-m-d H:i:s")))
            ->update();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            echo json_encode(array("status" => "error", "message" => $this->db->error()));
            return;
        }
        
        echo json_encode(array("status" => "success", "message" => "Alertas marcados com sucesso."));
    }

    public function get_devices_agrupamento($id)
    {
        $result = $this->db->query("
            SELECT
                esm_device_groups_entries.device as dvc,
                esm_unidades.nome
            FROM 
                esm_device_groups_entries
            JOIN esm_medidores ON esm_medidores.nome = esm_device_groups_entries.device
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE esm_device_groups_entries.group_id = $id"
        );

        if ($result->getNumRows()) {
            return $result->getResult();
        }

        return false;
    }

    public function get_devices_alert($group, $type)
    {
        $result = $this->db->query("
            SELECT
                esm_alertas_cfg_devices.device as dvc,
                esm_unidades.nome as nome
            FROM
                esm_alertas_cfg
                JOIN esm_alertas_cfg_devices ON esm_alertas_cfg_devices.config_id = esm_alertas_cfg.id
                JOIN esm_medidores ON esm_medidores.nome = esm_alertas_cfg_devices.device
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE
                esm_alertas_cfg.group_id = $group AND esm_alertas_cfg_devices.config_id = $type"
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

            $this->db->table($tabela)
                ->where('group_id', $dados['group_id'])
                ->set($campos)
                ->update();
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
                esm_shoppings.*,
                esm_blocos.nome AS nome 
            FROM
                esm_shoppings
                JOIN esm_blocos ON esm_blocos.id = esm_shoppings.bloco_id
                JOIN esm_condominios ON esm_condominios.id = esm_blocos.condo_id
                JOIN auth_user_relation ON auth_user_relation.entity_id = esm_condominios.id 
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

            return $this->db->table('users')
                ->select("users.*, esm_unidades.id as unidade_id, esm_unidades.nome as unidade_nome, esm_blocos.id as bloco_id, esm_blocos.nome as bloco_nome")
                ->join("auth_user_relation", "auth_user_relation.user_id = users.id")
                ->join("esm_unidades", "esm_unidades.id = auth_user_relation.unity_id")
                ->join("esm_blocos", "esm_blocos.id = esm_unidades.bloco_id")
                ->where("users.id", $user_id)
                ->get();
        } elseif ($user->inGroup("shopping", "shopping")) {

            return $this->db->table('users')
                ->select("users.*, esm_blocos.id as bloco_id, esm_blocos.nome as bloco_nome")
                ->join("auth_user_relation", "auth_user_relation.user_id = users.id")
                ->join("esm_blocos", "esm_blocos.id = auth_user_relation.group_id")
                ->where("users.id", $user_id)
                ->get();
        } else {

            return $this->db->table('users')
                ->select("users.*")
                ->join("auth_user_relation", "auth_user_relation.user_id = users.id")
                ->join("esm_condominios", "esm_condominios.id = auth_user_relation.entity_id")
                ->where("users.id", $user_id)
                ->get();
        }
    }

    public function insertToken($token, $group_id)
    {
        $this->db->transStart();

        $q = $this->db->table('esm_api_keys')
            ->where('group_id', $group_id)
            ->get();

        if ( $q->getNumRows() > 0 ) {
            $this->db->table('esm_api_keys')
                ->where('group_id', $group_id)
                ->set(array("token" => $token))
                ->update();
        } else {
            $this->db->table('esm_api_keys')
                ->set(array("group_id" => $group_id, "token" => $token))
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
            ->where("group_id", $dados['id'])
            ->delete();

        if ($dados['devices']) {
            foreach ($dados['devices'] as $dvc) {
                $this->db->table("esm_device_groups_entries")
                    ->set(array('group_id' => $dados['id'], 'device' => $dvc))
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
            ->where("group_id", $id)
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
                    ->set(array('group_id' => $inserted, 'device' => $dvc))
                    ->insert();
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return true;
    }

    public function get_subtipo_cliente_config($grp)
    {
        $this->db->query("
            SELECT
                IF(unc.type <= 1,(
                    SELECT esm_client_config.area_comum 
                    FROM esm_client_config 
                    WHERE esm_client_config.group_id = $grp
                ),'Unidades') as subtipo
            FROM esm_medidores me
            JOIN esm_unidades un ON un.id = me.unidade_id
            JOIN esm_unidades_config unc ON unc.unidade_id = un.id
            WHERE un.bloco_id = $grp AND me.tipo = 'energia'
        ")->getRow()->subtipo;
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
            ->where("group_id", $dados['group_id'])
            ->where("id", $dados['config_id'])
            ->set(array(
                'when_type' => $dados['esm_alertas_cfg']['when_type'],
                'notify_unity' => $dados['esm_alertas_cfg']['notify_unity'],
                'notify_shopping' => $dados['esm_alertas_cfg']['notify_shopping'],
                'active' => $dados['esm_alertas_cfg']['active'],
            ))
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
            WHERE esm_unidades.bloco_id = $shopping_id";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getResult();
    }

    public function get_condo($condo)
    {
        $query = "
            SELECT *
            FROM esm_condominios
            WHERE id = $condo";

        $result = $this->db->query($query);

        if ($result->getNumRows() <= 0)
            return false;

        return $result->getRow();
    }
}