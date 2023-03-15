<?php

namespace App\Models;

class Shopping_model extends Base_model
{
    public function get_entity_by_user($user): bool
    {
        $builder = $this->db->table('auth_users_entity');
        // Aplica filtro na query
        $builder->select('*')->where('user_id', $user);

        // realiza a consulta
        $query = $builder->get();

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow()->entity_id;
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
}