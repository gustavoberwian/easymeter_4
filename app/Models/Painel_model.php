<?php

namespace App\Models;

class Painel_model extends Base_model
{

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
        if (!$this->db->insert('esm_alertas', $data))
            echo json_encode(array("status"  => "error", "message" => $failure[0]['message']));
        else
            echo json_encode(array("status"  => "success", "message" => "Alerta cadastrado com sucesso!", "id"));
    }

    public function delete_alerta($id, $box, $adm)
    {
        if ($box == 'in') {
            if ($adm) {
                if (!$this->db->update('esm_alertas', array('visibility' => 'delbyadmin'), array('id' => $id))) {
                    echo json_encode(array("status"  => "error", "message" => $this->db->error()));
                    return;
                }
            } else {
                if (!$this->db->update('esm_alertas_envios', array('visibility' => 'delbyuser'), array('id' => $id))) {
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
                if (!$this->db->update('esm_alertas', array('visibility' => 'delbyuser'), array('id' => $id))) {
                    echo json_encode(array("status"  => "error", "message" => $this->db->error()));
                    return;
                }
                echo json_encode(array("status"  => "success", "message" => "Alerta excluído com sucesso"));
            }
        } else {
            echo json_encode(array("status"  => "error", "message" => 'Parâmetro incorreto.'));
        }
    }

}


/*

busca hora 0
SELECT DAY(FROM_UNIXTIME(timestamp-86400)) as dia, MONTH(FROM_UNIXTIME(timestamp-86400)) as mes, YEAR(FROM_UNIXTIME(timestamp-86400)) as ano, SUM(esm_leituras.consumo) as consumo FROM esm_leituras JOIN esm_medidores ON esm_medidores.id = esm_leituras.medidor_id JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id WHERE timestamp >= UNIX_TIMESTAMP('2018-10-28 00:00') AND timestamp <= UNIX_TIMESTAMP('2018-11-3 23:00') AND MD5(CONCAT('easymeter', esm_unidades.user_id, '123456')) = '77282aba8371f3b4245b121c7482b06b' AND esm_medidores.prumada_id = 1 AND HOUR(FROM_UNIXTIME(timestamp)) = 0 GROUP BY dia, mes, ano

busca total hora 1 a 23
SELECT DAY(FROM_UNIXTIME(timestamp)) as dia, MONTH(FROM_UNIXTIME(timestamp)) as mes, YEAR(FROM_UNIXTIME(timestamp)) as ano, SUM(esm_leituras.consumo) as consumo FROM esm_leituras JOIN esm_medidores ON esm_medidores.id = esm_leituras.medidor_id JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id WHERE timestamp >= UNIX_TIMESTAMP('2018-10-28 00:00') AND timestamp <= UNIX_TIMESTAMP('2018-11-3 23:00') AND MD5(CONCAT('easymeter', esm_unidades.user_id, '123456')) = '77282aba8371f3b4245b121c7482b06b' AND esm_medidores.prumada_id = 1 AND HOUR(FROM_UNIXTIME(timestamp)) > 0 GROUP BY dia, mes, ano

lista leituras
SELECT *,FROM_UNIXTIME(timestamp), DATE_FORMAT(FROM_UNIXTIME(esm_leituras.timestamp), 'D%d-%m-%y-%H') AS ts FROM `esm_leituras` WHERE medidor_id = 1 ORDER BY `esm_leituras`.`timestamp` DESC



*/