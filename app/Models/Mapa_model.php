<?php

namespace App\Models;

class Mapa_model extends Base_model {
    public function get_buildings(): array
    {
        $query = $this->db->query("
            SELECT *
            FROM
                teste_reservatorio
        ");
        return $query->getResult();
    }
}