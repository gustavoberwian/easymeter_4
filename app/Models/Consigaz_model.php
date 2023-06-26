<?php

namespace App\Models;

use App\Models\Gas_model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;
use Config\Database;

class Consigaz_model extends Base_model
{
    /**
     * @var Gas_model
     */
    private Gas_model $gas_model;

    public function __construct(?ConnectionInterface $db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->gas_model = new Gas_model();
    }

    public function edit_valve_stats($medidor, $state)
    {
        $status_valve = $this->db->table('esm_valves_stats')->where('medidor_id', $medidor)->select('status')->get();

        if ($status_valve->getRow()->status === 'vermelho') {
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
            ->join('esm_medidores', 'esm_medidores.unidade_id = esm_unidades.id', 'left')
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
        $result = $this->db->query("SELECT leitura FROM esm_leituras_bancada_gas
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_bancada_gas.medidor_id
            JOIN esm_unidades ON esm_unidades.id = $uid
            ORDER BY timestamp DESC LIMIT 1");

        return $result->getRow()->leitura;
    }

    public function get_ramal($entidade_id, $monitoriamento)
    {
        $result = $this->db->query("SELECT * FROM esm_ramais WHERE entidade_id = $entidade_id AND tipo = '$monitoriamento'");

        if ($result->getNumRows())
            return $result->getRow();

        return false;
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

    public function get_valvulas($medidor_id = null, $status = null, $op = null)
    {
        $s = "";
        if (!is_null($status)) {
            if ($status === 'open')
                $s = " AND esm_valves_stats.state = 1 ";
            elseif ($status === 'close')
                $s = " AND esm_valves_stats.state = 0 ";
            elseif ($status === 'vermelho')
                $s = " AND esm_valves_stats.status = '$status' ";
            elseif ($status === 'amarelo')
                $s = " AND esm_valves_stats.status = '$status' ";
            elseif ($status === 'verde')
                $s = " AND esm_valves_stats.status = '$status' ";
        }

        $m = "";
        if (!is_null($medidor_id)) {
            $m = " AND medidor_id = $medidor_id ";
        }

        $query = "SELECT esm_valves_stats.* FROM esm_valves_stats WHERE 1 $m $s";

        if ($op === 'count')
            return ($this->db->query($query)->getNumRows());

        return $this->db->query($query)->getResult();
    }

    public function get_last_fechamento($entidade_id)
    {
        $query = "SELECT * FROM esm_fechamentos_gas WHERE entidade_id = $entidade_id ORDER BY cadastro DESC LIMIT 1";

        $result = $this->db->query($query);

        if ($result->getNumRows())
            return $result->getRow();

        return false;
    }

    public function download_clientes($user_id)
    {
        $response = array();

        $entidades = $this->db->query('SELECT 
                esm_entidades.id, esm_entidades.nome,
                CONCAT(esm_entidades.logradouro, ", ", esm_entidades.numero, " - ", esm_entidades.bairro, ", ", esm_entidades.cidade, " - ", esm_entidades.uf, ", ", esm_entidades.cep) as endereco
            FROM auth_user_relation
            JOIN esm_entidades ON esm_entidades.id = auth_user_relation.entidade_id
            WHERE auth_user_relation.user_id = ' . $user_id)->getResult();

        foreach ($entidades as $i => $entidade) {
            $response[$i]['nome'] = $entidade->nome;

            $medidores = $this->get_medidores_by_entidade($entidade->id, 'gas');

            $abertos = 0;
            $fechados = 0;
            $erros = 0;
            $alertas = 0;
            $corretas = 0;
            $ultimo_mes = 0;
            $mes_atual = 0;
            $previsao = 0;
            $t_ultimo_mes = 0;
            $t_mes_atual = 0;
            $t_previsao = 0;
            foreach ($medidores as $medidor) {
                $abertos += $this->get_valvulas($medidor->id, 'open', 'count');
                $fechados += $this->get_valvulas($medidor->id, 'close', 'count');
                $erros += $this->get_valvulas($medidor->id, 'vermelho', 'count');
                $alertas += $this->get_valvulas($medidor->id, 'amarelo', 'count');
                $corretas += $this->get_valvulas($medidor->id, 'verde', 'count');

                $ultimo_mes = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);

                foreach ($ultimo_mes as $c) {
                    if (!is_null($c->value)) {
                        $t_ultimo_mes += $c->value;
                    } else {
                        $t_ultimo_mes += 0;
                    }
                }


                $response[$i]['ultimo_mes'] = number_format($t_ultimo_mes, 0, '', '');;

                $mes_atual = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
                foreach ($mes_atual as $c) {
                    if (!is_null($c->value)) {
                        $t_mes_atual += $c->value;
                    } else {
                        $t_mes_atual += 0;
                    }
                }

                $response[$i]['mes_atual'] = number_format($t_mes_atual, 0, '', '');;

                $previsao = $this->gas_model->GetConsumption($medidor->id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
                foreach ($previsao as $c) {
                    if (!is_null($c->value)) {
                        $t_previsao += $c->value;
                    } else {
                        $t_previsao += 0;
                    }
                }

                $days = (strtotime(date('Y-m-d')) - strtotime(date('Y-m-01'))) / 86400 + 1;
                $days_month = date('t', strtotime('this month'));

                $response[$i]['previsao'] = number_format($t_previsao / $days * $days_month, 0, '', '');
            };
        }

        $response[$i]['abertos'] = number_format($abertos, 0, '', '');
        $response[$i]['fechados'] = number_format($fechados, 0, '', '');
        $response[$i]['erros'] = number_format($erros, 0, '', '');
        $response[$i]['alertas'] = number_format($alertas, 0, '', '');
        $response[$i]['corretas'] = number_format($corretas, 0, '', '');

        return $response;
    }

    public function get_medidores_by_user($user_id)
    {
        $q = "SELECT esm_medidores.* 
            FROM esm_medidores
            JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
            JOIN esm_entidades ON esm_entidades.id = esm_entradas.entidade_id
            JOIN auth_user_relation ON auth_user_relation.entidade_id = esm_entidades.id AND auth_user_relation.user_id = $user_id";

        return $this->db->query($q)->getResult();
    }

    public function get_entidades($user_id)
    {
        $query = "SELECT esm_entidades.* 
            FROM esm_entidades
            JOIN auth_user_relation ON auth_user_relation.entidade_id = esm_entidades.id AND auth_user_relation.user_id = $user_id";

        return $this->db->query($query)->getResult();
    }

    public function save_secret_key($secret, $user_id)
    {
        $this->db->transStart();

        $this->db->table('auth_identities')
            ->where('user_id', $user_id)
            ->set(array('secret_key' => $secret))
            ->update();

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function get_secret_key($user_id)
    {
        $query = "SELECT auth_identities.secret_key 
            FROM auth_users
            JOIN auth_identities ON auth_identities.user_id = auth_users.id AND auth_users.id = $user_id";

        if ($this->db->query($query)->getNumRows()) {
            return $this->db->query($query)->getRow()->secret_key;
        }

        return false;
    }

    public function get_medidor($medidor_id)
    {
        $query = "SELECT esm_medidores.*, esm_unidades.nome AS unidade_nome, esm_agrupamentos.nome AS agrupamento_nome
            FROM esm_medidores
            LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            LEFT JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            WHERE esm_medidores.id = $medidor_id";

        return $this->db->query($query)->getRow();
    }

    public function download_unidades($entidade)
    {
        $response = array();

        $unidades = $this->db->query("SELECT 
                esm_medidores.id as m_id, 
                esm_unidades.id as u_id, 
                esm_medidores.nome AS device, 
                esm_medidores.device AS medidor, 
                esm_unidades.nome as unidade, 
                esm_agrupamentos.nome as bloco, 
                esm_valves_stats.state, 
                esm_valves_stats.status
            FROM esm_medidores
            JOIN esm_unidades on esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
            JOIN esm_valves_stats ON esm_valves_stats.medidor_id = esm_medidores.id
            WHERE esm_agrupamentos.entidade_id = $entidade")->getResult();

        foreach ($unidades as $i => $unidade) {
            $response[$i]['medidor'] = $unidade->medidor;
            $response[$i]['device'] = $unidade->device;
            $response[$i]['bloco'] = $unidade->bloco;
            $response[$i]['unidade'] = $unidade->unidade;

            $t_ultimo_mes = 0;
            $t_mes_atual = 0;
            $t_previsao = 0;

            $ultimo_mes = $this->gas_model->GetConsumption($unidade->m_id, date('Y-m-d H:i:s', strtotime('first day of last month')), date('Y-m-d H:i:s', strtotime('last day of last month')), array(), false);

            foreach ($ultimo_mes as $c) {
                if (!is_null($c->value)) {
                    $t_ultimo_mes += $c->value;
                } else {
                    $t_ultimo_mes += 0;
                }
            }

            $response[$i]['ultimo_mes'] = number_format($t_ultimo_mes, 0, '', '');

            $mes_atual = $this->gas_model->GetConsumption($unidade->m_id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
            foreach ($mes_atual as $c) {
                if (!is_null($c->value)) {
                    $t_mes_atual += $c->value;
                } else {
                    $t_mes_atual += 0;
                }
            }

            $response[$i]['mes_atual'] = number_format($t_mes_atual, 0, '', '');

            $previsao = $this->gas_model->GetConsumption($unidade->m_id, date('Y-m-d H:i:s', strtotime('first day of this month')), date('Y-m-d H:i:s'), array(), false);
            foreach ($previsao as $c) {
                if (!is_null($c->value)) {
                    $t_previsao += $c->value;
                } else {
                    $t_previsao += 0;
                }
            }

            $days = (strtotime(date('Y-m-d')) - strtotime(date('Y-m-01'))) / 86400 + 1;
            $days_month = date('t', strtotime('this month'));

            $response[$i]['previsao'] = number_format($t_previsao / $days * $days_month, 0, '', '');

            if ($unidade->state) {
                $response[$i]['state'] = "Aberto";
            } else {
                $response[$i]['state'] = "Fechado";
            }
        }

        return $response;
    }

    public function get_clientes_by_user($user_id)
    {
        $query = "
            SELECT esm_entidades.id, esm_entidades.nome
            FROM auth_user_relation
            JOIN esm_entidades ON esm_entidades.id = auth_user_relation.entidade_id
            WHERE auth_user_relation.user_id = $user_id
        ";

        return $this->db->query($query)->getResult();
    }

    public function edit_cliente($entidade, $data)
    {
        $this->db->transStart();

        $this->db->table('esm_entidades')
            ->where('id', $entidade)
            ->update($data);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array('status' => 'success', 'message' => 'Cliente atualizado com sucesso!'));
    }

    public function get_ranking_unidades($entidade)
    {
        $query = "SELECT 
                esm_unidades.nome AS unidade,
                SUM(esm_leituras_bancada_gas.consumo) AS value
            FROM esm_leituras_bancada_gas
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_bancada_gas.medidor_id
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id AND esm_agrupamentos.entidade_id = $entidade
            GROUP BY esm_unidades.id 
            ORDER BY value DESC LIMIT 3";

        $result = $this->db->query($query);

        if ($result->getNumRows())
            return $result->getResult();

        return false;
    }

    public function edit_medidor($medidor, $data)
    {
        $this->db->transStart();

        $this->db->table('esm_medidores')
            ->where('id', $medidor)
            ->update(array('device' => $data['medidor']));

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return json_encode(array("status" => "error", "message" => $this->db->error()));
        }

        return json_encode(array('status' => 'success', 'message' => 'Unidade atualizada com sucesso!'));
    }

    public function get_agrupamentos_by_entidade($entidade_id, $count = false)
    {
        $query = "SELECT esm_agrupamentos.* FROM esm_agrupamentos WHERE esm_agrupamentos.entidade_id = $entidade_id";

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            if ($count) {
                return $result->getNumRows();
            } else {
                return $result->getResult();
            }
        }

        return false;
    }

    public function get_unidades_by_entidade($entidade_id, $count = false)
    {
        $query = "SELECT esm_unidades.* FROM esm_unidades JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
                      AND esm_agrupamentos.entidade_id = $entidade_id";

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            if ($count) {
                return $result->getNumRows();
            } else {
                return $result->getResult();
            }
        }

        return false;
    }

    /*public function get_vazamentos_by_entidade($entidade_id, $count = false)
    {
        $query = "SELECT esm_unidades.* FROM esm_unidades JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
                      AND esm_agrupamentos.entidade_id = $entidade_id";

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            if ($count) {
                return $result->getNumRows();
            } else {
                return $result->getResult();
            }
        }

        return false;
    }*/

    public function get_valvulas_by_entidade($entidade_id, $count = false)
    {
        $query = "SELECT esm_valves_stats.* FROM esm_valves_stats
                          JOIN esm_medidores ON esm_medidores.id = esm_valves_stats.medidor_id
                          JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                          JOIN esm_agrupamentos ON esm_agrupamentos.id = esm_unidades.agrupamento_id
                          AND esm_agrupamentos.entidade_id = $entidade_id";

        $result = $this->db->query($query);

        if ($result->getNumRows()) {
            if ($count) {
                return $result->getNumRows();
            } else {
                return $result->getResult();
            }
        }

        return false;
    }
}