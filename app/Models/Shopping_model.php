<?php

namespace App\Models;

class Shopping_model extends Base_model
{
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
        echo "SELECT 
                *
            FROM esm_client_config
            WHERE 
                group_id = $gid"; return;
        $result = $this->db->query("
            SELECT 
                *
            FROM esm_client_config
            WHERE 
                group_id = $gid
        ");

        if ($result->getnumRows()) {
            return $result->getRow();
        }

        return false;
    }
}