<?php

namespace App\Models;

use CodeIgniter\Model;

class Energy_model extends Base_model
{
    public function GetOverallConsumption($type)
    {
        $result = $this->db->query("
            SELECT 
                SUM(activePositiveConsumption) AS value,
                SUM(activePositiveConsumption) / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())) AS prevision,
                SUM(activePositiveConsumption) / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) AS average                
            FROM 
                esm_leituras_ancar_energia
            WHERE 
                timestamp > DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND
                esm_leituras_ancar_energia.device IN (  SELECT esm_medidores.nome
                                                        FROM esm_unidades_config 
                                                        JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades_config.unidade_id
                                                        WHERE esm_unidades_config.type = $type)
        ");

        if ($result->getNumRows()) {
            return array (
                "consum"    => number_format(round($result->getRow()->value, 0), 0, ",", "."),
                "prevision" => number_format(round($result->getRow()->prevision, 0), 0, ",", "."),
                "average"   => number_format(round($result->getRow()->average, 0), 0, ",", ".")
            );
        }

        return array ("consum"    => "-","prevision" => "-","average"   => "-");
    }

    public function GetOverallConsumption_($type, $grp)
    {
        $result = $this->db->query("
            SELECT
                esm_unidades.bloco_id,
                SUM( activePositiveConsumption ) AS value,
                SUM( activePositiveConsumption ) / ( DATEDIFF( CURDATE(), DATE_FORMAT( CURDATE(), '%Y-%m-01' )) + 1 ) * DAY (LAST_DAY(CURDATE())) AS prevision,
                SUM( activePositiveConsumption ) / ( DATEDIFF( CURDATE(), DATE_FORMAT( CURDATE(), '%Y-%m-01' )) + 1 ) AS average 
            FROM
                esm_leituras_ancar_energia 
            JOIN 
                esm_medidores ON esm_medidores.id = esm_leituras_ancar_energia.device
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id 
            WHERE
                TIMESTAMP > DATE_FORMAT( CURDATE(), '%Y-%m-01' ) 
                AND esm_leituras_ancar_energia.device IN (
                    SELECT
                        esm_medidores.nome 
                    FROM
                        esm_unidades_config
                        JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades_config.unidade_id
                        JOIN esm_unidades ON esm_unidades.id = esm_unidades_config.unidade_id 
                    WHERE esm_unidades.bloco_id = $grp AND esm_unidades_config.type = $type
                )
        ");

        if ($result->getNumRows()) {
            return array (
                "bloco"    => number_format(round($result->getRow()->bloco_id, 0), 0, ",", "."),
                "consum"    => number_format(round($result->getRow()->value, 0), 0, ",", "."),
                "prevision" => number_format(round($result->getRow()->prevision, 0), 0, ",", "."),
                "average"   => number_format(round($result->getRow()->average, 0), 0, ",", ".")
            );
        }

        return array ("consum"    => "-","prevision" => "-","average"   => "-");
    }
}