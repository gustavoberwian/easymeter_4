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
            return false;

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

        return false;
    }

    public function get_group_info($group_id)
    {
        $query = $this->db->query(
            "SELECT 
                    esm_condominios.id as entity_id, 
                    esm_condominios.nome as entity_name, 
                    esm_blocos.id as group_id, 
                    esm_blocos.nome as group_name
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

        return false;
    }

    public function get_device_groups($eid)
    {
        $result = $this->db->query("
            SELECT
                *
            FROM
                esm_device_groups
            WHERE 
                entrada_id = $eid
            ORDER BY 
                name
        ");

        if ($result->getRow()) {
            return $result->getResultArray();
        }

        return false;
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
}