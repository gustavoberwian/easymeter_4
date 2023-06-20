<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class Base_model extends Model
{
    public function __construct(?ConnectionInterface $db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->db = \Config\Database::connect();
    }

    public function get_entity_by_group($grp)
    {
        $query = "SELECT esm_entidades.* FROM esm_entidades JOIN esm_agrupamentos ON esm_agrupamentos.entidade_id = esm_entidades.id WHERE esm_agrupamentos.id = $grp";

        return $this->db->query($query)->getRow();
    }

    public function get_group_by_device($dvc)
    {
        $query = "
            SELECT esm_entidades.* FROM esm_entidades 
            JOIN esm_agrupamentos ON esm_agrupamentos.entidade_id = esm_entidades.id
            JOIN esm_unidades ON esm_unidades.agrupamento_id = esm_agrupamentos.id
            JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades.id
            WHERE esm_medidores.nome = '$dvc'";

        return $this->db->query($query)->getRow();
    }

    public function get_entidade($entidade_id)
    {
        $result = $this->db->table('esm_entidades')
            ->where('esm_entidades.id', $entidade_id)
            ->select('*')
            ->get();

        return $result->getRow();
    }
}