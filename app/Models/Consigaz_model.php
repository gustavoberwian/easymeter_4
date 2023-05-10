<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class Consigaz_model extends Base_model
{
    public function edit_valve_stats($medidor, $state)
    {
        $status_valve = $this->db->table('esm_valves_stats')->where('medidor_id', $medidor)->select('status')->get();

        if ($status_valve->getRow()->status !== 'verde') {
            return json_encode(array("status" => "warning", "message" => "Não foi possível alterar status da válvula"));
        }

        $this->db->transStart();

        $this->db->table('esm_valves_stats')
            ->where('medidor_id', $medidor)
            ->update(array('state' => $state, 'status' => 'amarelo'));

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array('status' => 'success', 'state' => $state, 'm' => $medidor));
    }

    public function edit_valve_leitura($medidor, $leitura)
    {
        $ultima_hora_cheia = strtotime(date('Y-m-d H:00:00'));
        $this->db->transStart();

        $this->db->table('esm_valves_stats')
            ->where('medidor_id', $medidor)
            ->update(array('leitura' => $leitura, 'timestamp' => $ultima_hora_cheia));

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return json_encode(array("status" => "error", "message" => $this->db->error(), 'leitura' => $leitura, 'ts' => $ultima_hora_cheia, 'm' => $medidor));
        }

        return json_encode(array('status' => 'success', 'message' => 'Leitura sincronizada com sucesso', 'leitura' => $leitura, 'ts' => $ultima_hora_cheia, 'm' => $medidor));
    }

    public function get_unidade($uid)
    {
        $result = $this->db->table('esm_unidades')
            ->join('esm_agrupamentos', 'esm_agrupamentos.id = esm_unidades.agrupamento_id')
            ->join('esm_medidores', 'esm_medidores.unidade_id = esm_unidades.id')
            ->where('esm_unidades.id', $uid)
            ->select('esm_unidades.nome AS unidade_nome, 
                esm_agrupamentos.nome AS agrupamento_nome, 
                esm_agrupamentos.entidade_id AS entidade_id,
                esm_medidores.id AS medidor_id')
            ->get();

        return $result->getRow();
    }

    public function get_ultima_leitura($uid)
    {
        $result = $this->db->query("SELECT leitura FROM esm_leituras_consigaz_gas
            JOIN esm_unidades ON esm_unidades.id = $uid
            ORDER BY timestamp DESC LIMIT 1");

        return $result->getRow()->leitura;
    }

    public function get_ramal($entidade_id, $monitoriamento)
    {
        $result = $this->db->query("SELECT * FROM esm_ramais WHERE entidade_id = $entidade_id AND tipo = '$monitoriamento'");

        return $result->getRow();
    }

    public function get_fechamento($fechamento_id)
    {
        $result = $this->db->query("SELECT * FROM esm_fechamentos_gas WHERE id = $fechamento_id");

        return $result->getRow();
    }

    public function get_fechamento_entrada($id)
    {
        $result = $this->db->query("SELECT * FROM esm_fechamentos_gas_entradas WHERE id = $id");

        return $result->getRow();
    }

    public function get_unidade_by_medidor($medidor)
    {
        $result = $this->db->query("SELECT esm_unidades.*, esm_medidores.nome as medidor, esm_medidores.tipo FROM esm_unidades JOIN esm_medidores ON esm_medidores.unidade_id = esm_unidades.id AND esm_medidores.id = $medidor");

        return $result->getRow();
    }

    public function read_all_alert($user_id)
    {
        // atualiza data
        $this->db->transStart();

        $this->db->table('esm_alertas_envios')
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

    public function delete_alert($id)
    {
        if (!$this->db->table('esm_alertas_envios')->where(array('id' => $id))->set(array('visibility' => 'delbyuser'))->update()) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array("status" => "success", "message" => "Alerta excluído com sucesso.", "id" => $id));
    }

    public function get_user_alert($id, $readed = false)
    {
        $query = $this->db->query("
            SELECT 
                esm_alertas.tipo, 
                esm_alertas.titulo, 
                esm_alertas.texto, 
                COALESCE(esm_alertas.enviada, 0) AS enviada,
                COALESCE(esm_alertas_envios.lida, '') AS lida
            FROM esm_alertas_envios
            JOIN esm_alertas ON esm_alertas.id = esm_alertas_envios.alerta_id
            WHERE esm_alertas_envios.id = $id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        $ret = $query->getRow();

        if ($readed) {
            // atualiza esm_alertas
            $this->db->table('esm_alertas_envios')
                ->where('id', $id)
                ->where('lida', NULL)
                ->set(array('lida' => date("Y-m-d H:i:s")))
                ->update();
        }

        return $ret;
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
                esm_medidores ON esm_medidores.id = esm_fechamentos_{$type}_entradas.medidor_id
            JOIN 
                esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE 
                esm_fechamentos_{$type}_entradas.medidor_id = '$device' AND esm_fechamentos_{$type}.cadastro < '$date'
        ");

        if ($result->getNumRows()) {
            return $result->getResultArray();
        }

        return false;
    }

    public function get_medidores_by_entidade($entidade_id, $monitoramento = null)
    {
        $m = "";
        if (!is_null($monitoramento))
            $m = " AND esm_medidores.tipo = '$monitoramento' ";

        $query = "SELECT esm_medidores.* FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id 
            WHERE esm_agrupamentos.entidade_id = $entidade_id $m";

        return $this->db->query($query)->getResult();
    }

    public function get_valvulas($status = null, $op = null)
    {
        $s = "";
        if (!is_null($status)) {
            if ($status === 'open')
                $s = " AND esm_valves_stats.state = 1 ";
            elseif ($status === 'close')
                $s = " AND esm_valves_stats.state = 0 ";
        }

        $query = "SELECT esm_valves_stats.* FROM esm_valves_stats WHERE 1 $s";

        if ($op === 'count')
            return $this->db->query($query)->getNumRows();

        return $this->db->query($query)->getResult();
    }

    public function get_last_fechamento($entidade_id)
    {
        $query = "SELECT * FROM esm_fechamentos_gas WHERE entidade_id = $entidade_id ORDER BY cadastro DESC LIMIT 1";

        return $this->db->query($query)->getRow();
    }
}