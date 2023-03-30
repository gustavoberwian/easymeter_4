<?php  if ( ! defined('BASEPATH') ) exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function inclui_leituras($data, $header)
    {
        //$this->db->insert('post', array('text' => $data, 'header' => implode(",", $header), 'stamp' => date('Y-m-d H:i:s')));
        $this->db->insert('post', array('text' => $data, 'header' => json_encode($header), 'stamp' => date('Y-m-d H:i:s')));

        return $this->db->insert_id();
    }

    public function atualiza_ultimo_envio($central, $timestamp, $tamanho)
    {
        $this->db->update('esm_condominios_centrais', array('ultimo_envio' => $timestamp, 'tamanho' => $tamanho), array('nome' => $central));
    }

    public function atualiza_bateria($medidor, $battery, $timestamp)
    {
        $this->db->update('esm_medidores', array('battery' => $battery), array('id' => $medidor));
        $this->db->insert('esm_bateria', array('medidor_id' => $medidor, 'tensao' => $battery, 'timestamp' => $timestamp));
    }

    public function set_return($id, $text)
    {
        $this->db->update('post', array('returned' => $text), array('id' => $id));
    }

    public function inclui_data($central, $headers_str, $timestamp)
    {
        $headers = explode(',', $headers_str);

        if ($headers[4] == 'SIMCOM_MODULE')
            return;

        $data = explode(';', substr($headers[4], 0, -1));
        $x = [];
        foreach ($data as $k => $d) {
            $aux = explode(':', $d);
            $x[$k] = $aux[1];
        }

        if ($x[0] < 200) {
            $this->db->insert('esm_central_data', array('nome' => $central, 'fonte' => null, 'tensao' => null, 'hardware' => $x[0],
                'software' => $x[1], 'fraude_hi' => null, 'fraude_low' => null, 'timestamp' => $timestamp));
        } else {
            $this->db->insert('esm_central_data', array('nome' => $central, 'fonte' => $x[4], 'tensao' => $x[5], 'hardware' => $x[0],
                'software' => $x[1], 'fraude_hi' => $x[6], 'fraude_low' => $x[7], 'timestamp' => $timestamp));
        }
    }

    public function get_post()
    {
        $this->db->select('*');
        $this->db->from('post');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(50);

        $query = $this->db->get();

        return $query->result();
    }

    public function get_last_post($id)
    {
//        $query = $this->db->select("*")->limit(1)->order_by('id','DESC')->get('post');
        return $this->db->select("*")-> where('id', $id)->get('post')->row();
//        return $query->row();
    }

    public function verifica_stamp($tabela, $timestamp)
    {
        $query = $this->db->query("
            SELECT timestamp FROM esm_leituras_".$tabela."_agua WHERE timestamp = $timestamp LIMIT 1
        ");

        return ($query->num_rows() > 0);
    }

    public function get_central_count($id)
    {
        $query = $this->db->query("
            SELECT COUNT(id) AS total FROM esm_medidores WHERE central = '$id'
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return 0;

        return $query->row()->total;
    }

    public function get_medidores_central($id)
    {
        $query = $this->db->query("
            SELECT id, fator, tipo, offset, posicao FROM esm_medidores WHERE central = '$id' ORDER BY posicao
        ");

        return $query->result_array();
    }

    public function get_condo_central($id)
    {
        $query = $this->db->query("
            SELECT esm_condominios.id, esm_condominios.tabela FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
            JOIN esm_condominios ON esm_condominios.id = esm_blocos.condo_id
            WHERE esm_medidores.central = '$id' LIMIT 1
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row_array();
    }

    public function get_central_pos($id)
    {
        $query = $this->db->query("
            SELECT id, fator FROM esm_medidores WHERE central = '$id' ORDER BY posicao
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return 0;

        return $query->result_array();
    }

    public function salva_leitura($id, $data)
    {
        $this->db->trans_start();

        $this->db->insert('esm_envios', array( 'central_id' => $id,'dados' => substr($data, 4) ));

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function salva_leituras($tabela, $leituras_agua, $leituras_gas, $leituras_energia)
    {
        $error = 0;

        // inicia transação
        $this->db->trans_start();

        // insere registros de agua se possui registros
        if (count($leituras_agua))
            $this->db->insert_batch_ignore('esm_leituras_'.$tabela.'_agua', $leituras_agua);

        // insere registros de gas se possui registros
        if (count($leituras_gas))
            $this->db->insert_batch_ignore('esm_leituras_'.$tabela.'_gas', $leituras_gas);

        // insere registros de energia se possui registros
        if (count($leituras_energia))
            $this->db->insert_batch_ignore('esm_leituras_'.$tabela.'_energia', $leituras_energia);

        // salva codigo de erro
        $error = $this->db->error()['code'];

        // finaliza transação
        $this->db->trans_complete();

        // retorna status da transação
        return array('status' => $this->db->trans_status(), 'code' => $error);
    }

    public function verifica_timestamp($tabela, $tipo, $central, $timestamp)
    {
        $query = $this->db->query("
            SELECT IFNULL(MAX(timestamp), 0) AS ts FROM esm_leituras_{$tabela}_{$tipo} 
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_{$tipo}.medidor_id
            WHERE esm_medidores.central = '{$central}'
        ");

        return ($timestamp > intval($query->row()->ts));
    }

    public function get_posts($central, $start)
    {
        $query = $this->db->query("
            SELECT id
            FROM post
            WHERE LEFT(text, 4) = UNHEX('$central') and id >= $start and LENGTH(text) > 0
            ORDER BY stamp asc
        ");

        return $query->result();
    }

    public function get_central($central)
    {
        $query = $this->db->query("
            SELECT esm_condominios_centrais.nome, esm_condominios_centrais.auto_ok, esm_condominios.tabela, 
            esm_central_retorno.codigo, esm_central_retorno.timestamp
            FROM esm_condominios_centrais
            JOIN esm_condominios ON esm_condominios.id = esm_condominios_centrais.condo_id
            LEFT JOIN esm_central_retorno ON esm_central_retorno.central = esm_condominios_centrais.nome
            WHERE esm_condominios_centrais.nome = '$central'
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row_array();
    }

    public function get_medidor($field, $id)
    {
        $query = $this->db->query("
            SELECT 
                esm_condominios.id AS cid, 
                esm_condominios.nome AS condo, 
                esm_condominios.tabela AS tabela, 
                esm_unidades.nome AS unidade, 
                esm_blocos.nome AS bloco,
                esm_medidores.id AS mid,  
                esm_medidores.sensor_id AS sensor_id,  
                esm_medidores.nome AS medidor, 
                MAX(esm_fechamentos_entradas.leitura_atual) AS leitura
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
            JOIN esm_condominios ON esm_condominios.id = esm_blocos.condo_id
            JOIN esm_fechamentos ON esm_fechamentos.condo_id = esm_blocos.condo_id
            JOIN esm_ramais ON esm_ramais.id = esm_fechamentos.ramal_id
            JOIN esm_fechamentos_entradas ON esm_fechamentos_entradas.fechamento_id = esm_fechamentos.id AND esm_fechamentos_entradas.medidor_id = esm_medidores.id
            WHERE esm_medidores.$field = $id AND esm_ramais.tipo = 'gas'        
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0) {
            return json_encode(array("status"  => "error", "message" => "Unidade não encontrada!"));
        } else {
            $row = $query->row();

            return json_encode(
                array(
                    'status'    => 'success',
                    'mid'       => $row->mid,
                    'medidor'   => $row->medidor,
                    'sensor_id' => $row->sensor_id,
                    'cid'       => $row->cid,
                    'condo'     => $row->condo,
                    'tabela'    => $row->tabela,
                    'bloco'     => $row->bloco,
                    'unidade'   => $row->unidade,
                    'leitura'   => $row->leitura
                )
            );
        }
    }

    public function insere_leitura_gas($tabela, $mid, $leitura)
    {
        $this->db->insert("esm_leituras_{$tabela}_gas", array('medidor_id' => $mid, "leitura" => $leitura, 'timestamp' => time()));
    }

    public function inclui_entrevistas($user_id, $data) {
        $failure = array();
        $this->db->trans_start();

        // salva cada medidor de cada registro
        foreach ($data as $d) {
            if (!$this->db->insert('index_entrevistas',
                array (
                    'user_id' => $d['user_id'],
                    'pesq_id' => $d['pesq_id'],
                    'respostas' => $d['respostas'],
                    'local' => $d['local'],
                    'excluida' => $d['excluida'],
                    'inicio' => $d['inicio'],
                    'timestamp' => $d['timestamp'],
                    'cadastro' => time()
                ))) {

                $failure[] = $this->db->error();
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "message" => "OK"));
        }
    }

    public function inclui_entrevista($user, $pesq, $entrevista, $local, $inicio, $stamp)
    {
//        if ($this->db->insert('index_entrevistas', array('user_id' => $user, 'pesq_id' => $pesq, 'respostas' => $entrevista, 'local' => $local, 'inicio' => $inicio, 'timestamp' => $stamp))) {
        if ($this->db->insert('index_entrevistas', array('user_id' => $user, 'pesq_id' => $pesq, 'respostas' => $entrevista, 'local' => $local, 'timestamp' => $stamp, 'cadastro' => time()))) {
            return array("status"  => "success");
        } else {
            return array("status"  => "error");
        }
    }

    private function get_sindico_by_entrada($eid) {
        $query = $this->db->query("
            SELECT esm_pessoas.user_id
            FROM esm_entradas
            JOIN esm_condominios ON esm_condominios.id = esm_entradas.condo_id
            JOIN esm_pessoas ON esm_pessoas.id = esm_condominios.sindico_id
            WHERE esm_entradas.id = $eid
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row()->user_id;
    }

    private function get_administrador_by_entrada($eid) {
        $query = $this->db->query("
            SELECT esm_pessoas.user_id
            FROM esm_entradas
            JOIN esm_condominios ON esm_condominios.id = esm_entradas.condo_id
            JOIN esm_administradoras ON esm_administradoras.id = esm_condominios.admin_id
            JOIN esm_pessoas ON esm_pessoas.admin_id = esm_administradoras.id
            WHERE esm_entradas.id = $eid
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row()->user_id;
    }

    private function get_users_by_unidade($uid) {
        $query = $this->db->query("
            SELECT user_id
            FROM auth_users_unidades
            WHERE unidade_id = $uid
        ");

        return $query->result();
    }

    public function verifica_alertas($central)
    {
        // resolve os vazamentos finalizados
        $query = $this->db->query("
            UPDATE esm_alertas
            JOIN esm_medidores ON esm_medidores.id = esm_alertas.medidor_id AND esm_medidores.unidade_id = esm_alertas.unidade_id
            SET finalizado = NOW()
            WHERE 
                esm_alertas.enviada > '2021-03-05 00:00:00' AND 
                ISNULL(esm_alertas.finalizado) AND 
                esm_medidores.central = '$central' AND 
                esm_medidores.horas_consumo = 0        
        ");

        // busca medidores com consumo constante em 24 horas
        $query = $this->db->query("
            SELECT 
                esm_blocos.nome AS bloco,
                esm_unidades.id AS unidade_id,
                esm_unidades.nome AS unidade,
                esm_entradas.id AS entrada_id,
                esm_entradas.nome AS entrada,
                esm_medidores.id AS medidor_id,
                esm_medidores.consumo_horas AS consumo,
                esm_alertas.id AS alerta_id, 
                esm_alertas.finalizado
            FROM esm_medidores
            JOIN esm_unidades ON esm_medidores.unidade_id = esm_unidades.id
            JOIN esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
            JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
            LEFT JOIN esm_alertas ON esm_alertas.unidade_id = esm_unidades.id AND esm_alertas.medidor_id = esm_medidores.id AND ISNULL(esm_alertas.finalizado)
            WHERE esm_medidores.central = '$central' and esm_medidores.horas_consumo > esm_medidores.alerta_horas
        ");

        $vazamentos = $query->result();

        $this->db->trans_start();

        foreach ($vazamentos as $v) {
            // para cada medidor, cria um alerta

            if (is_null($v->alerta_id)) {

                if ($this->db->insert('esm_alertas', array(
                    'tipo' => 'vazamento',
                    'titulo' => "Vazamento Unidade ".(!is_null($v->bloco) ? $v->bloco.'/' : '').$v->unidade,
                    'texto' => "Foi detectado um vazamento na ".(($v->entrada == "Única") ? "" : "entrada ".$v->entrada." na ")."unidade ".(!is_null($v->bloco) ? $v->bloco.'/' : '').$v->unidade,
                    'enviada' => date('Y-m-d H:i:s'),
                    'enviado_por' => 0,
                    'email' => 0,
                    'monitoramento' => 'agua',
                    'unidade_id' => $v->unidade_id,
                    'medidor_id' => $v->medidor_id,
                    'consumo_horas' => $v->consumo,
                ))) {

                    $id = $this->db->insert_id();

                    // insere mensagem para o sindico
                    $sindico = $this->get_sindico_by_entrada($v->entrada_id);
                    if ($sindico) {
                        $this->db->insert('esm_alertas_envios', array(
                            'user_id' => $sindico,
                            'alerta_id' => $id
                        ));
                    }

                    // insere mensagem para a administradora
                    $adm = $this->get_administrador_by_entrada($v->entrada_id);
                    if ($adm) {
                        $this->db->insert('esm_alertas_envios', array(
                            'user_id' => $adm,
                            'alerta_id' => $id
                        ));
                    }

                    // insere para os usuários cadastrados na unidade
                    $users = $this->get_users_by_unidade($v->unidade_id);
                    foreach ($users as $u) {
                        $this->db->insert('esm_alertas_envios', array(
                            'user_id' => $u->user_id,
                            'alerta_id' => $id
                        ));
                    }
                }
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function verifica_consumos($central, $tabela, $dia = false)
    {
        if ($dia) {
            $valor = 90000;
            $field = "esm_medidores.alerta_consumo_dia";
            $where = " AND NOT ISNULL(esm_medidores.alerta_consumo_dia)";
            $msg   = "nas últimas 24 horas";
        } else {
            $valor = 2000;
            $field = "esm_medidores.alerta_consumo_hora";
            $where = "";
            $msg   = "na última hora";
        }

        // busca medidores com consumo excessivo no dia anterior
        $excesso = $this->db->query("
            SELECT 
                '$msg' AS texto,
                esm_leituras_baviera_agua.medidor_id, 
                esm_entradas.id AS entrada_id,
                esm_unidades.id AS unidade_id,
                esm_entradas.nome AS entrada,
                esm_blocos.nome AS bloco,
                esm_unidades.nome AS unidade,
                sum(esm_leituras_baviera_agua.consumo) AS consumo,
                $field
            FROM esm_leituras_{$tabela}_agua
            JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$tabela}_agua.medidor_id 
            JOIN esm_unidades ON esm_medidores.unidade_id = esm_unidades.id
            JOIN esm_blocos ON esm_blocos.id = esm_unidades.bloco_id
            JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
            WHERE 
                timestamp > UNIX_TIMESTAMP() - $valor AND
                esm_medidores.central = '$central'
                $where
            GROUP BY esm_medidores.id
            HAVING SUM(esm_leituras_{$tabela}_agua.consumo) > $field
        ")->result();

        $this->db->trans_start();

        foreach ($excesso as $v) {
            // para cada medidor, cria um alerta
            if ($this->db->insert('esm_alertas', array(
                'tipo' => 'consumo',
                'titulo' => "Aviso de Consumo Unidade ".(!is_null($v->bloco) ? $v->bloco.'/' : '').$v->unidade,
                'texto' => "Foi detectado um consumo excessivo $msg na ".((in_array($v->entrada, array("Única", "Geral"))) ? "" : "entrada ".$v->entrada." da ")."unidade ".(!is_null($v->bloco) ? $v->bloco.'/' : '').$v->unidade." (".number_format($v->consumo, 0, ",", ".")."L)",
                'enviada' => date('Y-m-d H:i:s'),
                'enviado_por' => 0,
                'email' => 0,
                'monitoramento' => 'agua',
                'unidade_id' => $v->unidade_id,
                'medidor_id' => $v->medidor_id,
                'consumo_horas' => 0,
            ))) {
                /*
                                $id = $this->db->insert_id();

                                // insere mensagem para o sindico
                                $sindico = $this->get_sindico_by_entrada($v->entrada_id);
                                if ($sindico) {
                                    $this->db->insert('esm_alertas_envios', array(
                                        'user_id' => $sindico,
                                        'alerta_id' => $id
                                    ));
                                }

                                // insere mensagem para a administradora
                                $adm = $this->get_administrador_by_entrada($v->entrada_id);
                                if ($adm) {
                                    $this->db->insert('esm_alertas_envios', array(
                                        'user_id' => $adm,
                                        'alerta_id' => $id
                                    ));
                                }

                                // insere para os usuários cadastrados na unidade
                                $users = $this->get_users_by_unidade($v->unidade_id);
                                foreach ($users as $u) {
                                    $this->db->insert('esm_alertas_envios', array(
                                        'user_id' => $u->user_id,
                                        'alerta_id' => $id
                                    ));
                                }
                */
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function agrometer($data)
    {
        //return $this->db->insert('agrometer', array('an1' => $data->an1, 'an2' => $data->an2, 'an3' => $data->an3, 'an4' => $data->an4));
        return $this->db->insert('agrometer', array('an1' => 0, 'an2' => 0, 'an3' => 0, 'an4' => $data));
    }

    public function inclui_clima($central)
    {
        $code = 4350;

        if(in_array(strtolower($central), array('43000701'))) {

            $temp = false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://apiadvisor.climatempo.com.br/api/v1/weather/locale/$code/current?token=d2d2526c982d5fa58328d3428d59a312");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $content    = curl_exec($ch);
            $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode != 200) {
                return false;
            }

            $temp = json_decode($content);
            $temp->data->precipitation = null;

            if ($temp) {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://apiadvisor.climatempo.com.br/api/v2/forecast/precipitation/locale/$code/hours/168?token=d2d2526c982d5fa58328d3428d59a312");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $content    = curl_exec($ch);
                $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpCode == 200) {
                    $rain = json_decode($content);
                    $precipitation = $rain->precipitations[0]->value;
                }

                // insere dados de clima
                $this->db->insert('esm_condominios_clima', array(
                    'condo_id' => 9,
                    'timestamp' => time() - (time() % 3600),
                    'condicao' => $temp->data->condition,
                    'icone' => $temp->data->icon,
                    'temperatura' => $temp->data->temperature,
                    'humidade' => $temp->data->humidity,
                    'pressao' => $temp->data->pressure,
                    'precipitation' => $precipitation
                ));

                return $rain->precipitations[0]->value;
            }
        }

        return false;
    }

    public function insert_energy($data)
    {
        if ($this->db->insert('esm_leituras_energia', $data)) {
            return $this->db->insert_id();
        } else {
            return $this->db->error()['code'];
        }
    }

    public function insert_multilaser($data)
    {
        if ($this->db->insert('esm_leituras_multilaser', $data)) {
            return $this->db->insert_id();
        } else {
            return $this->db->error()['code'];
        }
    }

    public function insert_alive($data)
    {
        if ($this->db->insert('esm_central_post', $data)) {
            return $this->db->insert_id();
        } else {
            return $this->db->error()['code'];
        }
    }

    public function insert_raw($device, $origin, $data, $headers)
    {
        if ($this->db->insert('post_raw', array('device' => $device, 'origin' => $origin, 'payload' => $data, 'header' => json_encode($headers), 'stamp' => date('Y-m-d H:i:s')))) {
            return $this->db->insert_id();
        } else {
            return $this->db->error()['code'];
        }
    }

    public function inclui_ambev($data, $header)
    {
        $this->db->insert('post_ambev', array('text' => $data, 'header' => json_encode($header), 'stamp' => date('Y-m-d H:i:s')));

        return $this->db->insert_id();
    }

    public function inclui_bancada($data, $header)
    {
        $this->db->insert('post_bancada', array('text' => $data, 'header' => json_encode($header), 'stamp' => date('Y-m-d H:i:s')));

        return $this->db->insert_id();
    }

    public function inclui_log($data, $header, $tipo, $time, $devi, $mess)
    {
        $this->db->insert('post_log', array(
            'text' => $data,
            'header' => json_encode($header),
            'device' => $devi,
            'tipo' => $tipo,
            'time' => $time,
            'mensagem' => $mess,
            'stamp' => date('Y-m-d H:i:s')
        ));

        return $this->db->insert_id();
    }

    public function inclui_ambev_data($data)
    {
        if ($this->db->insert('esm_leituras_ambev', $data)) {
            return $this->db->insert_id();
        } else {
            return $this->db->error()['code'] + 500;
        }
    }

    public function get_last_post_ambev($id)
    {
        if ($id == 0) {
            $query = $this->db->query("SELECT MAX(id) AS id FROM post_ambev")->row();

            $id = $query->id;
        }

        return $this->db->select("*")-> where('id', $id)->get('post_ambev')->row();
    }

    public function get_last_post_bancada($id)
    {
        if ($id == 0) {
            $query = $this->db->query("SELECT MAX(id) AS id FROM post_bancada")->row();

            $id = $query->id;
        }

        return $this->db->select("*")-> where('id', $id)->get('post_bancada')->row();
    }

    public function get_data_raw($id)
    {
        if ($id == 0) {
            $query = $this->db->query("SELECT MAX(id) AS id FROM post_raw")->row();

            $id = $query->id;
        }

        return $this->db->select("*")-> where('id', $id)->get('post_raw')->row();
    }

    public function get_device($device, $port, $type)
    {
        $query = $this->db->query("
            SELECT esm_medidores.id, 
                   esm_medidores.fator, 
                   esm_condominios.tabela, 
                   esm_medidores.tipo
            FROM esm_medidores 
            INNER JOIN esm_condominios_centrais ON esm_condominios_centrais.nome = esm_medidores.central
            INNER JOIN esm_condominios ON esm_condominios.id = esm_condominios_centrais.condo_id
            WHERE 
                esm_medidores.sensor_id = CONV('$device', 16, 10) AND 
                esm_medidores.posicao = $port AND
                esm_medidores.tipo = '$type'
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return 0;

        return $query->row_array();
    }

    public function get_medidor_ambev($id, $port, $type)
    {
        $query = $this->db->query("
            SELECT esm_medidores.id, esm_medidores.fator, esm_condominios.tabela, esm_medidores.tipo
            FROM esm_medidores 
            INNER JOIN esm_condominios_centrais ON esm_condominios_centrais.nome = esm_medidores.central
            INNER JOIN esm_condominios ON esm_condominios.id = esm_condominios_centrais.condo_id
            WHERE 
                esm_medidores.sensor_id = CONV('$id', 16, 10) AND 
                esm_medidores.posicao = $port AND
                esm_medidores.tipo $type 'nivel'
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return 0;

        return $query->row_array();
    }

    public function salva_leituras_detalhes($device)
    {
        return $this->db->insert("esm_leituras_detalhes", array(
            "device" => $device['device'],
            "timestamp" => $device['timestamp'],
            "bateria" => $device['battery'],
            "status" => $device['status'],
            "rssi" => $device['rssi']
        ));
    }

    public function salva_central_detalhes($central, $versao)
    {
        return $this->db->insert("esm_central_data", array(
            "nome" => $central[0],
            "imei" => $central[1],
            "sim" => $central[2],
            "operadora" => $central[3],
            "sinal" => $central[4],
            "software" => $versao,
            "timestamp" => gmdate('U')
        ));
    }

    public function salva_leituras_ambev($medidor, $pulse, $level, $timestamp)
    {
        $error = 0;

        // inicia transação
//        $this->db->trans_start();

        // insere registros de agua se possui registros
        if (count($pulse)) {
            $keys = array_keys($pulse);
            for ($i = 0; $i < count($pulse); $i++) {
                $m = $this->get_medidor_ambev($medidor, $keys[$i], "!=");

                $this->db->insert("esm_leituras_".$m['tabela']."_".$m['tipo'], array("medidor_id" => $m['id'], "timestamp" => $timestamp, "leitura" => $pulse[$keys[$i]]));
                //$this->db->insert("esm_leituras_".$m['tabela']."_agua", array("medidor_id" => $m['id'], "timestamp" => $timestamp, "leitura" => round($pulse[$keys[$i]] / $m['fator'], 3)));
            }
        }

        if (count($level)) {

            $keys = array_keys($level);

            for ($i = 0; $i < count($level); $i++) {

                $m = $this->get_medidor_ambev($medidor, $keys[$i], "=");

                $this->db->insert("esm_leituras_".$m['tabela']."_nivel", array("medidor_id" => $m['id'], "timestamp" => $timestamp, "leitura" => $level[$keys[$i]][1]));

                $ts = $timestamp - 600;

                for ($j = 6; $j > 1; $j--) {
//                    $m = $this->get_medidor_ambev($medidor, $keys[$i], "=");
                    $this->db->insert("esm_leituras_".$m['tabela']."_nivel", array("medidor_id" => $m['id'], "timestamp" => $ts, "leitura" => $level[$keys[$i]][$j]));

                    $ts -= 600;
                }
            }
        }

        // salva codigo de erro
        $error = $this->db->error()['code'];

        // finaliza transação
//        $this->db->trans_complete();

        // retorna status da transação
        return $error;
    }

    public function salva_leituras_ambev_bancada($medidor, $pulse, $level, $timestamp)
    {
        $error = 0;

        // inicia transação
        $this->db->trans_start();

        // insere registros de agua se possui registros
        if (count($pulse)) {
            $keys = array_keys($pulse);
            for ($i = 0; $i < count($pulse); $i++) {
                $this->db->insert('esm_leituras_ambev_agua', array("medidor_id" => $medidor, "port" => $keys[$i], "timestamp" => $timestamp, "leitura" => $pulse[$keys[$i]]));
            }
        }

        // insere registros de gas se possui registros
        if (count($level)) {
            $j = 0;
            for ($i = 1; $i <= count($level); $i += 6) {
                $this->db->insert('esm_leituras_ambev_nivel', array("medidor_id" => $medidor, "port" => $j, "timestamp" => $timestamp, "t0" => $level[$i], "t10" => $level[$i + 1], "t20" => $level[$i + 2], "t30" => $level[$i + 3], "t40" => $level[$i + 4], "t50" => $level[$i + 5]));
                $j++;
            }
        }

        // salva codigo de erro
        $error = $this->db->error()['code'];

        // finaliza transação
        $this->db->trans_complete();

        // retorna status da transação
        return $error;
    }

    public function get_central_cfg($central)
    {
        $query = $this->db->query("
            SELECT * FROM esm_condominios_centrais WHERE nome = '$central'
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row();
    }

    public function set_central_cfg($central, $cfg)
    {
        $this->db->update('esm_condominios_centrais', array('config' => $cfg, 'device' => NULL), array('nome' => $central));
    }

    public function get_central_device_cfg($central)
    {
        $query = $this->db->query("
            SELECT *
            FROM esm_medidores
            where central = '$central'
            ORDER BY sensor_id, tipo, posicao
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->result_array();
    }

    public function get_central_device_count($central)
    {
        $query = $this->db->query("
            SELECT *
            FROM esm_medidores
            where central = '$central'
            GROUP BY sensor_id
        ");

        return $query->num_rows();
    }

    public function insert_config($data)
    {
        if ($this->db->insert('esm_central_config', $data)) {
            return $this->db->insert_id();
        } else {
            return $this->db->error()['code'];
        }
    }


    public function save_data($device, $timestamp, $pulse, $level)
    {
        if (count($pulse)) {

            $ports = array_keys($pulse);

            for ($i = 0; $i < count($pulse); $i++) {

                $m = $this->get_device($device, $ports[$i], "agua");

                if ($m)
                    $this->db->insert("esm_leituras_".$m['tabela']."_".$m['tipo'], array("medidor_id" => $m['id'], "timestamp" => $timestamp, "leitura" => round($pulse[$ports[$i]] / $m['fator'], 3)));
            }
        }

        if (count($level)) {

            $ports = array_keys($level);

            for ($i = 0; $i < count($level); $i++) {

                $m = $this->get_medidor_ambev($device, $ports[$i], "=");

                $this->db->insert("esm_leituras_".$m['tabela']."_nivel", array("medidor_id" => $m['id'], "timestamp" => $timestamp, "leitura" => $level[$ports[$i]][1]));

                $ts = $timestamp - 600;

                for ($j = 6; $j > 1; $j--) {

                    $this->db->insert("esm_leituras_".$m['tabela']."_nivel", array("medidor_id" => $m['id'], "timestamp" => $ts, "leitura" => $level[$ports[$i]][$j]));

                    $ts -= 600;
                }
            }
        }
    }

    public function get_data($count = 30)
    {
        $query = $this->db->query("
            SELECT post_raw.id, post_raw.device, esm_condominios_centrais.localizador, post_raw.origin, LENGTH(post_raw.payload) AS size, post_raw.stamp
            FROM post_raw 
            JOIN esm_condominios_centrais ON esm_condominios_centrais.nome = post_raw.device
            ORDER BY stamp DESC 
            LIMIT $count
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->result();

    }

    public function get_data_all($device, $start)
    {
        $query = $this->db->query("
            SELECT id
            FROM post_raw
            WHERE 
                device = '$device' AND 
                origin = 'data' AND
                id >= $start
            ORDER BY stamp ASC
        ");

        return $query->result();
    }

    public function save_energy_data($data)
    {
        $this->db->insert("esm_leituras_ancar_energia", $data);
    }

    public function get_devices($central)
    {
        $query = $this->db->query("
            SELECT sensor_id
            FROM esm_medidores
            where central = '$central'
            ORDER BY RAND()
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->result();
    }

    public function ancar()
    {
        $query = $this->db->query("
            SELECT 
                FROM_UNIXTIME(esm_leituras_ancar_energia.timestamp) AS timestamp, 
                COUNT(*) AS total
            FROM esm_medidores 
            LEFT JOIN esm_leituras_ancar_energia ON esm_medidores.nome = esm_leituras_ancar_energia.device
            WHERE esm_medidores.central = '25EBEDD4' OR esm_medidores.central = '14013FEC' OR esm_medidores.central = '1403097C' OR esm_medidores.central = '1401402C' OR esm_medidores.central = 'C2F8C35C' OR esm_medidores.central = '14013FB8'
            GROUP BY esm_leituras_ancar_energia.timestamp
            ORDER BY esm_leituras_ancar_energia.timestamp DESC, esm_leituras_ancar_energia.device
            LIMIT 100
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->result();
    }

    public function get_ancar($id, $post)
    {
        $p = ($post > 0) ? " AND id = $post" : "";
        $query = $this->db->query("
            SELECT * 
            FROM post_raw
            WHERE device = '$id' AND origin = 'data' AND LENGTH(payload) > 1 $p
            ORDER BY stamp DESC
            LIMIT 1
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row();
    }

    public function get_ancar_devices()
    {
        $query = $this->db->query("
            SELECT esm_medidores.sensor_id, esm_medidores.central, esm_medidores.nome AS device, esm_unidades.nome AS local
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            WHERE esm_medidores.central = '25EBEDD4' OR central = '14013FEC' OR central = '1403097C' OR central = '1401402C' OR central = 'C2F8C35C' OR central = '14013FB8'
            ORDER BY esm_medidores.sensor_id
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->result();
    }

    public function GetBobs()
    {
        $query = $this->db->query("
            SELECT activePositiveConsumption, activeNegativeConsumption, reactivePositiveConsumption, reactiveNegativeConsumption
            FROM esm_leituras_ancar_energia
            where device = '03D27702'
            ORDER BY timestamp DESC
            LIMIT 1
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row();
    }

    public function GetFabrica()
    {
        $query = $this->db->query("
            SELECT activePositive, activeNegative, reactivePositive, reactiveNegative
            FROM esm_leituras_ancar_energia
            where device = '03D278DC'
            ORDER BY timestamp DESC
            LIMIT 1
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        return $query->row();
    }

    public function GetAlertCfg($aid)
    {
        $result = $this->db->query("
            SELECT 
                esm_alertas_cfg.notify_shopping,
                esm_alertas_cfg.notify_unity,
                esm_alertas_cfg.description,
                esm_alertas_cfg_devices.device            
            FROM esm_alertas_cfg
            JOIN 
                esm_alertas_cfg_devices ON esm_alertas_cfg_devices.config_id = esm_alertas_cfg.id
            WHERE 
                esm_alertas_cfg.active = 1 AND esm_alertas_cfg.type = $aid
        ");

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function GetAlert($aid, $device = "")
    {
        if ($aid == 1) {

            $result = $this->db->query("
                SELECT 
                    today.value AS today,
                    last.value AS last
                FROM (
                    SELECT 
                        IFNULL(SUM(activePositiveConsumption), 0) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_ancar_energia d ON 
                        d.timestamp > (esm_calendar.ts_start) AND 
                        d.timestamp <= (esm_calendar.ts_end + 600) 
                        AND d.device = '$device'
                    WHERE 
                        esm_calendar.dt = CURDATE()
                    GROUP BY 
                        esm_calendar.dt
                ) today
                JOIN (
                    SELECT IFNULL(ROUND(AVG(l.value), 3), 0) AS value
                    FROM (
                        SELECT 
                            SUM(activePositiveConsumption) AS value
                        FROM esm_calendar
                        LEFT JOIN esm_leituras_ancar_energia d ON 
                            d.timestamp > (esm_calendar.ts_start) AND 
                            d.timestamp <= (esm_calendar.ts_end + 600) 
                            AND d.device = '$device'
                        WHERE 
                            esm_calendar.dt BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() - INTERVAL 1 DAY
                        GROUP BY 
                            esm_calendar.dt
                    ) l
                ) last
            ");

            return ($result->num_rows()) ? $result->row() : false;
        }

        return false;
    }

    private function GetUserIdByDevice($device)
    {
        $result = $this->db->query("
            SELECT auth_users.id
            FROM auth_users
            JOIN auth_users_unidades ON auth_users_unidades.user_id = auth_users.id
            JOIN esm_medidores ON esm_medidores.unidade_id = auth_users_unidades.unidade_id
            WHERE esm_medidores.nome = '$device'        
        ");

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    private function GetGroupUserIdByDevice($device)
    {
        $result = $this->db->query("
            SELECT auth_users_group.user_id AS id
            FROM auth_users_group
            WHERE group_id = (
                SELECT esm_unidades.bloco_id
                FROM esm_medidores
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE esm_medidores.nome = '$device'
            )
        ");

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function AddAlerts($data, $cfg, $set = array())
    {
        foreach($data as $d) {

            $this->db->trans_start();

            // insere alerta
            $this->db->insert('esm_alertas_energia', $d);

            $id = $this->db->insert_id();

            // envia para ancar
            $this->db->insert('esm_alertas_energia_envios', array("user_id" => 538, "alerta_id" => $id));

            // envia para shopping
            if ($cfg->notify_shopping) {

                $group = $this->GetGroupUserIdByDevice($d["device"]);

                if ($group) {
                    foreach($group as $g) {
                        $this->db->insert('esm_alertas_energia_envios', array("user_id" => $g->id, "alerta_id" => $id));
                    }
                }
            }

            // envia para lojas
            if ($cfg->notify_unity) {
                $users = $this-> GetUserIdByDevice($d["device"]);
                if ($users) {
                    foreach($users as $u) {
                        $this->db->insert('esm_alertas_energia_envios', array("user_id" => $u->id, "alerta_id" => $id));
                    }
                }
            }

            // atualiza dados
            if ($cfg->type == 2) {
                $this->db->update('esm_unidades_config', array('alerta_consumo' => $set["current"]), array('unidade_id' => $set["unidade_id"]));
            }

            $this->db->trans_complete();
        }
    }

    public function envia_reports($device, $type, $start, $end)
    {
        $result = $this->db->query("
            SELECT esm_relatorios_config.*, esm_condominios.tabela
            FROM esm_relatorios_config
            JOIN esm_condominios ON esm_condominios.id = esm_relatorios_config.condo_id
            WHERE esm_relatorios_config.central = '$device' AND esm_relatorios_config.tipo = $type AND esm_relatorios_config.ultimo != '$start'
        ");

        if ($result->num_rows()) {

            $rels = $result->result();

            foreach($rels as $r) {

                if ($r->tipo == 1 || $r->tipo == 2) {

                    $this->db->trans_start();

                    $this->db->insert('esm_relatorios', array('condo_id' => $r->condo_id, 'tipo' => $r->tipo, 'competencia' => date($r->tipo == 1 ? "m/Y" : "d/m/Y", strtotime($start))));

                    $rid = $this->db->insert_id();

                    $result = $this->db->query("
                        SELECT 
                            $rid AS relatorio_id,
                            esm_medidores.id AS medidor_id, 
                            LPAD(ROUND(MIN(leitura)), 6 , '0') AS leitura_anterior, 
                            LPAD(ROUND(MAX(leitura)), 6 , '0') AS leitura_atual,
                            ROUND(MAX(leitura) - MIN(leitura)) AS consumo
                        FROM esm_leituras_{$r->tabela}_agua
                        JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$r->tabela}_agua.medidor_id
                        JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
                        WHERE 
                            esm_entradas.condo_id = {$r->condo_id} AND 
                            esm_medidores.tipo = 'agua' AND
                            esm_leituras_{$r->tabela}_agua.timestamp >= UNIX_TIMESTAMP('$start') AND
                            esm_leituras_{$r->tabela}_agua.timestamp <= (UNIX_TIMESTAMP('$end') + 86400)
                        GROUP BY esm_medidores.id
                    ");

                    if ($result->num_rows()) {

                        $data = $result->result_array();

                        $total = 0;
                        foreach($data as $d) {
                            $total += $d['consumo'];
                        }

                        $this->db->update('esm_relatorios', array('consumo' => $total), array('id' => $rid));

                        if ($this->db->insert_batch('esm_relatorios_dados', $data)) {
                            // insert envios
                            $result = $this->db->query("
                                SELECT email
                                FROM auth_users
                                WHERE auth_users.id = {$r->user_id}
                                UNION
                                SELECT email
                                FROM esm_user_emails
                                WHERE user_id = {$r->user_id}
                            ");

                            if ($result->num_rows()) {

                                $users = $result->result();

                                foreach($users as $u) {
                                    $this->db->insert('esm_relatorios_envios', array('relatorio_id' => $rid, 'email' => $u->email, 'data' => date("Y-m-d H:i:s", time()), 'uid' => md5($rid.$u->email)));
                                    //TODO: send email

                                    $config['protocol']     = 'smtp';
                                    $config['smtp_host']    = 'email-ssl.com.br';
                                    $config['smtp_port']    = '587';
                                    $config['smtp_timeout'] = '60';
                                    $config['smtp_user']    = 'contato@easymeter.com.br';
                                    $config['smtp_pass']    = 'index#1996';
                                    $config['charset']      = 'utf-8';
                                    $config['newline']      = "\r\n";
                                    $config['mailtype']     = "html";
                                    $config['smtp_crypto']  = 'tls';

                                    $this->load->library('email');
                                    $this->email->initialize($config);

                                    $this->email->attach('assets/img/logo_b.png');
                                    $this->email->attach('assets/img/convite.png');

                                    $logo    = $this->email->attachment_cid('assets/img/logo_b.png');
                                    $convite = $this->email->attachment_cid('assets/img/convite.png');

                                    $this->email->from('contato@easymeter.com.br');
                                    //$this->email->to("gustavo@unorobotica.com.br");
                                    $this->email->to($u->email);
                                    $this->email->reply_to('');
                                    $this->email->subject('Relatório de Consumo');
                                    $this->email->message($this->load->view('painel/template/emails/report', array('uid' => md5($rid.$u->email), 'tipo' => $r->tipo, 'logo' => $logo, 'convite' => $convite), TRUE));

                                    if ($this->email->send()) {

                                    }
                                }
                            }

                            if ($this->db->insert('esm_alertas', array(
                                'tipo'          => 'relatorio',
                                'titulo'        => 'Relatório de Consumo',
                                'texto'         => $r->tipo == 1 ? "O Relatório Mensal de Consumo foi gerado e enviado." : "O Relatório Diário de Consumo foi gerado e enviado.",
                                'enviada'       => date('Y-m-d H:i:s'),
                                'enviado_por'   => 0,
                                'email'         => 0,
                                'monitoramento' => 'agua',
                                'unidade_id'    => 0,
                                'medidor_id'    => 0,
                                'consumo_horas' => 0
                            ))) {

                                $aid = $this->db->insert_id();

                                $this->db->insert('esm_alertas_envios', array(
                                    'user_id' => $r->user_id,
                                    'alerta_id' => $aid
                                ));
                            }
                        }
                    }

                    $this->db->update('esm_relatorios_config', array('ultimo' => $start), array('condo_id' => $r->condo_id, 'central' => $r->central, 'tipo' => $r->tipo));

                    $this->db->trans_complete();
                }
            }
        }
    }

    public function get_alertas_config($central)
    {
        $result = $this->db->query("
            SELECT *
            FROM esm_alertas_config
            WHERE central = '$central'
        ");

        if ($result->num_rows()) {

            return $result->result();
        }

        return false;
    }

    private function alertas_send_email($alerta, $id, $mensagem)
    {
        // insert envios
        $result = $this->db->query("
            SELECT email
            FROM auth_users
            WHERE auth_users.id = {$alerta->user_id}
            UNION
            SELECT email
            FROM esm_user_emails
            WHERE user_id = {$alerta->user_id}
        ");

        if ($result->num_rows()) {

            $users = $result->result();

            $config['protocol']     = 'smtp';
            $config['smtp_host']    = 'email-ssl.com.br';
            $config['smtp_port']    = '587';
            $config['smtp_timeout'] = '60';
            $config['smtp_user']    = 'contato@easymeter.com.br';
            $config['smtp_pass']    = 'index#1996';
            $config['charset']      = 'utf-8';
            $config['newline']      = "\r\n";
            $config['mailtype']     = "html";
            $config['smtp_crypto']  = 'tls';

            $this->load->library('email');
            $this->email->initialize($config);

            $this->email->attach('assets/img/logo_b.png');
            $this->email->attach('assets/img/'.$alerta->tipo.'.png');

            $logo = $this->email->attachment_cid('assets/img/logo_b.png');
            $icon = $this->email->attachment_cid('assets/img/'.$alerta->tipo.'.png');
            $bg   = $alerta->tipo == 'consumo' ? 'aviso' : 'orange';
            $tit  = $alerta->tipo == 'consumo' ? 'Alerta de Consumo' : 'Alerta de Nível';

            $this->email->from('contato@easymeter.com.br');
            $this->email->to("gustavo@unorobotica.com.br");
            //$this->email->to($u->email);
            $this->email->reply_to('');
            $this->email->subject($tit);

            foreach($users as $u) {

                $this->email->message($this->load->view('painel/template/emails/alerta', array('logo' => $logo, 'icon' => $icon, 'bg' => $bg, 'titulo' => $tit, 'mensagem' => $mensagem), TRUE));

                $this->email->send();
            }
        }
    }

    public function generate_alertas($alerta)
    {
        $process = false;
        $data    = "";

        if ($alerta->tipo == 'consumo' && $alerta->monitoramento = 'agua' && $alerta->quando == 'dia') {

            $query = $this->db->query("
                SELECT 
                    esm_unidades.nome, 
                    SUM(consumo) AS value
                FROM esm_leituras_{$alerta->tabela}_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$alerta->tabela}_agua.medidor_id
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE 
                    timestamp >= UNIX_TIMESTAMP(DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%y-%m-%d %H:%i:00')) AND
                    timestamp <= UNIX_TIMESTAMP(DATE_FORMAT(CURDATE(), '%y-%m-%d %H:%i:00')) AND
                    medidor_id = {$alerta->medidor_id}
            ");

            if ($query->num_rows()) {
                $result = $query->row();
                $data    = date('d/m/Y', strtotime('yesterday'));
                $process = true;
            }

        } else if ($alerta->tipo == 'nivel' && $alerta->monitoramento = 'nivel' && $alerta->quando == 'hora' ) {

            $query = $this->db->query("
                SELECT 
                    esm_medidores.nome,
                    ((esm_leituras_{$alerta->tabela}_nivel.leitura - 1162) * esm_sensores_nivel.mca / 4649 / esm_sensores_nivel.profundidade_total) * 100 as value
                FROM esm_leituras_{$alerta->tabela}_nivel
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$alerta->tabela}_nivel.medidor_id
                JOIN esm_sensores_nivel ON esm_sensores_nivel.medidor_id = esm_leituras_{$alerta->tabela}_nivel.medidor_id
                WHERE
                    esm_leituras_{$alerta->tabela}_nivel.medidor_id = {$alerta->medidor_id} AND
                    esm_leituras_{$alerta->tabela}_nivel.leitura > 1162 AND
                    timestamp = (SELECT MAX(timestamp) FROM esm_leituras_{$alerta->tabela}_nivel WHERE timestamp = UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d %H')))
            ");

            if ($query->num_rows()) {

                $result = $query->row();

                $process = is_null($alerta->ultimo);

                if (!$process) {

                    if ($result->value >= $alerta->min) {

                        $this->db->update('esm_alertas_config', array('ultimo' => null), array('id' => $alerta->id));

                        // resolve os vazamentos finalizados
                        $query = $this->db->query("
                            UPDATE esm_alertas
                            SET finalizado = NOW()
                            WHERE 
                                ISNULL(finalizado) AND 
                                medidor_id = {$alerta->medidor_id}
                        ");
                    }
                }
            }

        } else if ($alerta->tipo == 'captacao' && $alerta->monitoramento = 'agua' && $alerta->quando == 'hora' ) {

            $query = $this->db->query("
                SELECT 
                    esm_unidades.nome, 
                    SUM(consumo) AS value
                FROM esm_leituras_{$alerta->tabela}_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$alerta->tabela}_agua.medidor_id
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE 
                    timestamp = UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%y-%m-%d %H:00:00')) AND
                    medidor_id = {$alerta->medidor_id}
            ");

            if ($query->num_rows()) {
                $result = $query->row();
                $data    = date('d/m/Y H:00', strtotime('last hour'));
                $process = true;
            }

        } else if ($alerta->tipo == 'tempo' && $alerta->monitoramento = 'agua' && $alerta->quando == 'dia' ) {

            $query = $this->db->query("
                SELECT 
                    esm_unidades.nome, 
                    COUNT(consumo) AS value
                FROM esm_leituras_{$alerta->tabela}_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_{$alerta->tabela}_agua.medidor_id
                JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE 
                    timestamp > UNIX_TIMESTAMP(DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%y-%m-%d %H:%i:00')) AND
                    timestamp <= UNIX_TIMESTAMP(DATE_FORMAT(CURDATE(), '%y-%m-%d %H:%i:00')) AND
                    consumo > 0 AND
                    medidor_id = {$alerta->medidor_id}
            ");

            if ($query->num_rows()) {
                $result  = $query->row();
                $data    = date('d/m/Y', strtotime('yesterday'));
                $process = true;
            }
        }

        if ($process) {

            $titulo   = str_replace('%d', $result->nome, $alerta->titulo);
            $mensagem = str_replace(array('%d', '%v', '%i', '%a', '%t'), array($result->nome, number_format($result->value, 0, '', '.'), number_format($alerta->min, 0, '', '.'), number_format($alerta->max, 0, '', '.'), $data), $alerta->texto);
            $generate = false;

            if (!is_null($alerta->max) && is_null($alerta->min)) {
                // maior que
                if ($result->value > $alerta->max) {
                    $generate = true;
                }

            } else if (is_null($alerta->max) && !is_null($alerta->min)) {
                // menor que
                if ($result->value < $alerta->min) {
                    $generate = true;
                }

            } else if (!is_null($alerta->max) && !is_null($alerta->min)) {
                // fora da faixa de valores
                if ($result->value > $alerta->max || $result->value < $alerta->min) {
                    $generate = true;
                }
            }

            if ($generate) {

                $this->db->trans_start();

                if ($this->db->insert('esm_alertas', array(
                    'tipo'          => $alerta->tipo,
                    'titulo'        => $titulo,
                    'texto'         => $mensagem,
                    'enviada'       => date('Y-m-d H:i:s'),
                    'enviado_por'   => 0,
                    'email'         => 0,
                    'monitoramento' => $alerta->monitoramento,
                    'unidade_id'    => 0,
                    'medidor_id'    => $alerta->medidor_id,
                    'consumo_horas' => $result->value
                ))) {

                    $id = $this->db->insert_id();

                    $this->db->insert('esm_alertas_envios', array(
                        'user_id' => $alerta->user_id,
                        'alerta_id' => $id
                    ));

                    $this->alertas_send_email($alerta, $id, $mensagem);
                }

                $this->db->update('esm_alertas_config', array('ultimo' => date('Y-m-d 00:00:00')), array('id' => $alerta->id));

                $this->db->trans_complete();
            }
        }
    }

    /* API */

    public function GetClientConfig($gid)
    {
        $result = $this->db->query("
            SELECT 
                *
            FROM esm_client_config
            WHERE 
                group_id = $gid
        ");

        if ($result->num_rows()) {
            return $result->row();
        }

        return false;
    }

    public function api_get_resume($cfg, $type, $array = false)
    {

        $where = "";
        if (!is_null($type))
            $where = "AND esm_unidades_config.type = $type";

        $query = $this->db->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                LPAD(ROUND(esm_medidores.ultima_leitura), 6, '0') AS current,
                m.value AS month,
                h.value AS month_opened,
                m.value - h.value AS month_closed,
                p.value AS ponta,
                m.value - p.value AS fora,
                l.value AS last,
                ROUND(m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE())), 3) AS prevision
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            LEFT JOIN (  
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_ancar_energia
                    WHERE 
                        timestamp > UNIX_TIMESTAMP() - 86400
                    GROUP BY device
                ) l ON l.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        d.device,
                        SUM(activePositiveConsumption) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_ancar_energia d ON 
                        (d.timestamp) > (esm_calendar.ts_start) AND 
                        (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    WHERE 
                        esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                        esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.device
                ) m ON m.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_ancar_energia
                    WHERE 
                        MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                    GROUP BY device                
                ) c ON c.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        device,
                        SUM(activePositiveConsumption) AS value
                    FROM 
                        esm_leituras_ancar_energia
                    WHERE 
                        MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now())
                        AND (MOD((timestamp), 86400) >= {$cfg->open} AND MOD((timestamp), 86400) <= {$cfg->close})
                    GROUP BY device
                ) h ON h.device = esm_medidores.nome
            LEFT JOIN (
                    SELECT 
                        d.device,
                        SUM(activePositiveConsumption) AS value
                    FROM esm_calendar
                    LEFT JOIN esm_leituras_ancar_energia d ON 
                        (d.timestamp) > (esm_calendar.ts_start) AND 
                        (d.timestamp) <= (esm_calendar.ts_end + 600) 
                        AND ((MOD((d.timestamp), 86400) >= {$cfg->ponta_start} AND MOD((d.timestamp), 86400) <= {$cfg->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)
                    WHERE 
                        esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                        esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                    GROUP BY d.device
                ) p ON p.device = esm_medidores.nome
            WHERE 
                entrada_id = 72 $where
            ORDER BY 
            esm_unidades_config.type, esm_unidades.nome
        ");

        // verifica se retornou algo
        if ($query->num_rows() == 0)
            return false;

        if ($array) {

            return $query->result_array();

        } else {

            $data = $query->result();

            foreach ($data as $d) {
                $d->type         = intval($d->type);
                $d->month        = floatval($d->month);
                $d->month_opened = floatval($d->month_opened);
                $d->month_closed = floatval($d->month_closed);
                $d->ponta        = floatval($d->ponta);
                $d->fora         = floatval($d->fora);
                $d->last         = floatval($d->last);
                $d->prevision    = floatval($d->prevision);
            }

            return $data;
        }
    }

    public function api_get_active_positive($device, $start, $end, $st = array(), $gp = false)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }


        if ($start == $end) {

            $station = "";
            if (count($st)) {
                if ($st[0] == 'fora') {
                    $station = " AND (MOD((d.timestamp), 86400) < $st[1] OR MOD((d.timestamp), 86400) > $st[2])";
                } else if ($st[0] == 'ponta') {
                    $station = "AND (MOD((d.timestamp), 86400) >= $st[1] AND MOD((d.timestamp), 86400) <= $st[2])";
//                } else if ($st == 'inter') {
//                    $station = "AND ((MOD((d.timestamp), 86400) >= 59400 AND MOD((d.timestamp), 86400) < 63000) OR (MOD((d.timestamp), 86400) > 73800 AND MOD((d.timestamp), 86400) <= 77400))";
                }
            }

            $group = "";
            if (!$gp)
                $group = "GROUP BY esm_hours.num";

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    SUM(activePositiveConsumption) AS value
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $station
                    $dvc
                $group
                ORDER BY esm_hours.num
            ");

        } else {

            $station = "";
            if (count($st)) {
                if ($st[0] == 'fora') {
                    $station = " AND (((MOD((d.timestamp), 86400) < $st[1] OR MOD((d.timestamp), 86400) > $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7) OR esm_calendar.dw = 1 OR esm_calendar.dw = 7)";
                } else if ($st[0] == 'ponta') {
                    $station = "AND ((MOD((d.timestamp), 86400) >= $st[1] AND MOD((d.timestamp), 86400) <= $st[2]) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)";
                    //            } else if ($st == 'inter') {
                    //                $station = "AND (((MOD((d.timestamp), 86400) >= 59400 AND MOD((d.timestamp), 86400) < 63000) OR (MOD((d.timestamp), 86400) > 73800 AND MOD((d.timestamp), 86400) <= 77400)) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)";
                }
            }

            $group = "";
            if (!$gp)
                $group = "GROUP BY esm_calendar.dt";

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label,
                    SUM(activePositiveConsumption) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    $station
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                $group
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function GetActiveDemand($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    MAX(activeDemand) AS valueMax,
                    SUM(activePositiveConsumption) AS valueSum
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label,
                    MAX(activeDemand) AS valueMax,
                    SUM(activePositiveConsumption) AS valueSum
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function GetMainReactive($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    SUM(reactivePositiveConsumption) AS valueInd,
                    SUM(ABS(reactiveNegativeConsumption)) AS valueCap
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label,
                    SUM(reactivePositiveConsumption) AS valueInd,
                    SUM(ABS(reactiveNegativeConsumption)) AS valueCap
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function GetMainLoad($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(esm_hours.num, ':00') AS label, 
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, ROUND(IFNULL(AVG(activePositiveConsumption + ABS(activePositiveConsumption)) / MAX(activePositiveConsumption + ABS(activePositiveConsumption)), 1), 3)) AS value
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label,
                    ROUND(IFNULL(AVG(activePositiveConsumption + ABS(activePositiveConsumption)) / MAX(activePositiveConsumption + ABS(activePositiveConsumption)), 1), 3) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {

            $data = $result->result();

            foreach ($data as $d) {
                $d->value = floatval($d->value);
            }

            return $data;
        }

        return false;
    }

    public function GetValuesPhases($device, $start, $end, $field)
    {
        $operation["instant_active"]   = ["active", "SUM("];
        $operation["instant_current"]  = ["current", "AVG("];
        $operation["instant_voltage"]  = ["voltage", "AVG("];
        $operation["instant_power"]    = ["active", "MAX("];
        $operation["instant_reactive"] = ["active", "SUM(ABS"];

        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    {$operation[$field][1]}({$operation[$field][0]}A)) AS value_a,
                    {$operation[$field][1]}({$operation[$field][0]}B)) AS value_b,
                    {$operation[$field][1]}({$operation[$field][0]}C)) AS value_c
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label,
                    {$operation[$field][1]}({$operation[$field][0]}A)) AS value_a,
                    {$operation[$field][1]}({$operation[$field][0]}B)) AS value_b,
                    {$operation[$field][1]}({$operation[$field][0]}C)) AS value_c
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                        d.timestamp >= esm_calendar.ts_start AND 
                        d.timestamp <= esm_calendar.ts_end 
                        $dvc
                WHERE 
                        esm_calendar.dt >= '$start' AND 
                        esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function GetLoadPhases($device, $start, $end, $field)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(AVG(activeA) / MAX(activeA), 1)) AS value_a,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(AVG(activeB) / MAX(activeB), 1)) AS value_b,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(AVG(activeC) / MAX(activeC), 1)) AS value_c
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label,
                    esm_calendar.dw AS dw,
                    IFNULL(AVG(activeA) / MAX(activeA), 1) AS value_a,
                    IFNULL(AVG(activeB) / MAX(activeB), 1) AS value_b,
                    IFNULL(AVG(activeC) / MAX(activeC), 1) AS value_c
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                        d.timestamp >= esm_calendar.ts_start AND 
                        d.timestamp <= esm_calendar.ts_end 
                        $dvc
                WHERE 
                        esm_calendar.dt >= '$start' AND 
                        esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function GetFactorPhases($device, $start, $end)
    {
        $dvc = "";
        if (is_numeric($device)) {
            if ($device == 0) {

            } else {
                $dvc = " AND d.device IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";
            }

        } else if ($device == "C") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 1)";

        } else if ($device == "U") {

            $dvc = "AND d.device IN (SELECT esm_medidores.nome FROM esm_unidades_config LEFT JOIN esm_medidores ON esm_medidores.unidade_id= esm_unidades_config.unidade_id WHERE type = 2)";

        } else {
            $dvc = " AND d.device = '$device'";
        }

        if ($start == $end) {

            $result = $this->db->query("
                SELECT 
                    CONCAT(esm_hours.num, ':00') AS label, 
                    IF(SUM(reactiveA) > 0, 'I', 'C') AS type_a,
                    IF(SUM(reactiveB) > 0, 'I', 'C') AS type_b,
                    IF(SUM(reactiveC) > 0, 'I', 'C') AS type_c,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(SUM(activeA) / SQRT(POW(SUM(activeA), 2) + POW(SUM(ABS(reactiveA)), 2)), 1)) AS value_a,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(SUM(activeB) / SQRT(POW(SUM(activeB), 2) + POW(SUM(ABS(reactiveB)), 2)), 1)) AS value_b,
                    IF(DATE(NOW()) = '$start' AND esm_hours.num >= HOUR(NOW()), null, IFNULL(SUM(activeC) / SQRT(POW(SUM(activeC), 2) + POW(SUM(ABS(reactiveC)), 2)), 1)) AS value_c
                FROM esm_hours
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    HOUR(FROM_UNIXTIME(d.timestamp - 600)) = esm_hours.num AND 
                    d.timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    d.timestamp <= UNIX_TIMESTAMP('$end 23:59:59') 
                    $dvc
                GROUP BY esm_hours.num
                ORDER BY esm_hours.num
            ");

        } else {

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label, 
                    IF(SUM(reactiveA) > 0, 'I', 'C') AS type_a,
                    IF(SUM(reactiveB) > 0, 'I', 'C') AS type_b,
                    IF(SUM(reactiveC) > 0, 'I', 'C') AS type_c,
                    IFNULL(SUM(activeA) / SQRT(POW(SUM(activeA), 2) + POW(SUM(ABS(reactiveA)), 2)), 1) AS value_a,
                    IFNULL(SUM(activeB) / SQRT(POW(SUM(activeB), 2) + POW(SUM(ABS(reactiveB)), 2)), 1) AS value_b,
                    IFNULL(SUM(activeC) / SQRT(POW(SUM(activeC), 2) + POW(SUM(ABS(reactiveC)), 2)), 1) AS value_c
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    d.timestamp > (esm_calendar.ts_start) AND 
                    d.timestamp <= (esm_calendar.ts_end) 
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                GROUP BY esm_calendar.dt
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function GetConsumption($device, $start, $end, $st = array(), $group = true)
    {
        $dvc  = "";
        $dvc1 = "";
        if (is_numeric($device)) {

            $dvc = "JOIN esm_medidores on esm_medidores.id = esm_leituras_ancar_agua.medidor_id AND esm_medidores.nome IN (SELECT device FROM esm_device_groups_entries WHERE group_id = $device)";

        } else if ($device == "C") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                    LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 1";

        } else if ($device == "U") {

            $dvc = "LEFT JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                    LEFT JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_medidores.unidade_id AND esm_unidades_config.type = 2";

        } else if ($device == "T") {

        } else {

            $dvc1 = "JOIN esm_medidores ON esm_medidores.nome = '$device'";
            $dvc = "AND medidor_id = esm_medidores.id";

        }

        $station = "";
        if (count($st)) {
            if ($st[0] == 'opened') {
                $station = "AND HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$st[1]})) AND HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$st[2]}))";
            } else if ($st[0] == 'closed') {
                $station = "AND (HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$st[1]})) OR HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$st[2]})))";
            }
        }

        if ($start == $end) {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_hours.num";

            $result = $this->db->query("
                SELECT 
                    CONCAT(LPAD(esm_hours.num, 2, '0'), ':00') AS label, 
                    SUM(consumo) AS value
                FROM esm_hours
                $dvc1
                JOIN esm_leituras_ancar_agua ON 
                    HOUR(FROM_UNIXTIME(timestamp - 3600)) = esm_hours.num AND 
                    timestamp > UNIX_TIMESTAMP('$start 00:00:00') AND 
                    timestamp <= UNIX_TIMESTAMP('$end 23:59:59') + 600
                    $station
                    $dvc
                $group_by
                ORDER BY esm_hours.num
            ");

        } else {

            $group_by = "";
            if ($group)
                $group_by = "GROUP BY esm_calendar.dt";

            $result = $this->db->query("
                SELECT 
                    esm_calendar.dt AS label,
                    SUM(consumo) AS value
                FROM esm_calendar
                $dvc1
                JOIN esm_leituras_ancar_agua ON 
                    timestamp > esm_calendar.ts_start AND 
                    timestamp <= (esm_calendar.ts_end + 600)
                    $station
                    $dvc
                WHERE 
                    esm_calendar.dt >= '$start' AND 
                    esm_calendar.dt <= '$end' 
                $group_by
                ORDER BY esm_calendar.dt
            ");
        }

        if ($result->num_rows()) {
            return $result->result();
        }

        return false;
    }

    public function water_resume($cfg, $type)
    {
        $where = "";
        if (!is_null($type))
            $where = "AND esm_unidades_config.type = $type";

        $query = $this->db->query("
            SELECT 
                esm_medidores.nome AS device, 
                esm_unidades.nome AS name, 
                esm_unidades_config.type AS type,
                LPAD(ROUND(esm_medidores.ultima_leitura), 6, '0') AS current,
                ROUND(m.value) AS month,
                ROUND(h.value) AS month_opened,
                ROUND(m.value) - h.value AS month_closed,
                ROUND(l.value) AS last,
                ROUND(m.value / (DATEDIFF(CURDATE(), DATE_FORMAT(CURDATE() ,'%Y-%m-01')) + 1) * DAY(LAST_DAY(CURDATE()))) AS prevision
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            JOIN (  
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_ancar_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                WHERE timestamp > UNIX_TIMESTAMP() - 86400
                GROUP BY medidor_id
            ) l ON l.device = esm_medidores.nome
            JOIN (
                SELECT esm_medidores.nome as device, SUM(consumo) AS value
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_agua d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                JOIN esm_medidores ON esm_medidores.id = d.medidor_id
                WHERE 
                    esm_calendar.dt >= DATE_FORMAT(CURDATE() ,'%Y-%m-01') AND 
                    esm_calendar.dt <= DATE_FORMAT(CURDATE() ,'%Y-%m-%d') 
                GROUP BY d.medidor_id
            ) m ON m.device = esm_medidores.nome
            JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_ancar_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                WHERE MONTH(FROM_UNIXTIME(timestamp)) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(timestamp)) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                GROUP BY medidor_id
            ) c ON c.device = esm_medidores.nome
            JOIN (
                SELECT esm_medidores.nome AS device, SUM(consumo) AS value
                FROM esm_leituras_ancar_agua
                JOIN esm_medidores ON esm_medidores.id = esm_leituras_ancar_agua.medidor_id
                WHERE 
                    MONTH(FROM_UNIXTIME(timestamp)) = MONTH(now()) AND 
                    YEAR(FROM_UNIXTIME(timestamp)) = YEAR(now()) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$cfg->open})) AND 
                    HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$cfg->close}))
                GROUP BY medidor_id
            ) h ON h.device = esm_medidores.nome
            WHERE 
                entrada_id = 73 $where
            ORDER BY 
            esm_unidades_config.type, esm_unidades.nome
        ");

        if ($query->num_rows() == 0)
            return false;

        $data = $query->result();

        foreach ($data as $d) {
            $d->type         = intval($d->type);
            $d->month        = floatval($d->month);
            $d->month_opened = floatval($d->month_opened);
            $d->month_closed = floatval($d->month_closed);
            $d->last         = floatval($d->last);
            $d->prevision    = floatval($d->prevision);
        }

        return $data;
    }

    public function api_device_list($id, $type)
    {
        $where = "";
        if (!is_null($type))
            $where = "AND esm_unidades_config.type = $type";

        $result = $this->db->query("
            SELECT esm_medidores.nome as device, esm_unidades.nome AS name, esm_unidades_config.type
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id
            WHERE esm_medidores.entrada_id = $id $where
        ");

        if ($result->num_rows()) {

            $data = $result->result();

            foreach ($data as $d) {
                $d->type = intval($d->type);
            }

            return $data;
        }

        return false;
    }

    public function api_get_token($key)
    {
        $result = $this->db->query("
            SELECT esm_api_keys.*, e.id AS energia_id, a.id AS agua_id
            FROM esm_api_keys
            JOIN esm_blocos ON esm_blocos.id = esm_api_keys.group_id
            LEFT JOIN (SELECT id, condo_id FROM esm_entradas WHERE tipo = 'energia') e ON e.condo_id = esm_blocos.condo_id
            LEFT JOIN (SELECT id, condo_id FROM esm_entradas WHERE tipo = 'agua') a ON a.condo_id = esm_blocos.condo_id
            WHERE token = '$key'
        ");

        if ($result->num_rows()) {
            return $result->row();
        }

        return false;
    }

    public function api_get_device($device, $entrada, $tipo)
    {
        $result = $this->db->query("
            SELECT id
            FROM esm_medidores
            WHERE nome = '$device' AND entrada_id = $entrada AND tipo = '$tipo'
        ");

        return ($result->num_rows() > 0);
    }

    public function api_get_lancamentos($type, $gid, $pag)
    {
        if ($type == 'agua') {

            $result = $this->db->query("
                SELECT
                    esm_fechamentos_agua.id,
                    competencia AS competence,
                    FROM_UNIXTIME(inicio, '%Y-%m-%d') AS start,
                    FROM_UNIXTIME(fim, '%Y-%m-%d') AS end,
                    ROUND(consumo_c + consumo_u) AS consumption,
                    ROUND(consumo_c_o + consumo_u_o) AS consumption_opened,
                    ROUND(consumo_c_c + consumo_u_c) AS consumption_closed,
                    DATE_FORMAT(cadastro, '%Y-%m-%d') AS date
                FROM
                    esm_fechamentos_agua
                JOIN 
                    esm_blocos ON esm_blocos.id = esm_fechamentos_agua.group_id AND esm_blocos.id = $gid
                ORDER BY cadastro DESC
                LIMIT 10 OFFSET $pag
            ");

        } else if ($type == 'energia') {

            $result = $this->db->query("
                SELECT
                    esm_fechamentos_energia.id,
                    competencia AS competence,
                    FROM_UNIXTIME(inicio, '%Y-%m-%d') AS start,
                    FROM_UNIXTIME(fim, '%Y-%m-%d') AS end,
                    consumo AS common_consumption,
                    consumo_p AS common_ponta,
                    consumo_f AS common_fora,
                    demanda AS common_demand,
                    consumo_u AS units_consumption,
                    consumo_u_p  AS units_ponta,
                    consumo_u_f  AS units_fora,
                    demanda_u AS units_demand,
                    DATE_FORMAT(cadastro, '%Y-%m-%d') AS date
                FROM
                    esm_fechamentos_energia
                JOIN 
                    esm_blocos ON esm_blocos.id = esm_fechamentos_energia.group_id AND esm_blocos.id = $gid
                ORDER BY cadastro DESC
                LIMIT 10 OFFSET $pag
            ");
        }

        if ($result->num_rows()) {

            $data = $result->result();

            if ($type == 'agua') {

                foreach ($data as $d) {
                    $d->id         = intval($d->id);
                    $d->consumption        = floatval($d->consumption);
                    $d->consumption_opened = floatval($d->consumption_opened);
                    $d->consumption_closed = floatval($d->consumption_closed);
                }

            } else if ($type == 'energia') {

                foreach ($data as $d) {
                    $d->id                 = intval($d->id);
                    $d->common_consumption        = floatval($d->common_consumption);
                    $d->common_ponta = floatval($d->common_ponta);
                    $d->common_fora = floatval($d->common_fora);
                    $d->common_demand = floatval($d->common_demand);
                    $d->units_consumption = floatval($d->units_consumption);
                    $d->units_fora = floatval($d->units_fora);
                    $d->units_demand = floatval($d->units_demand);
                }
            }

            return $data;
        }

        return array();
    }

    public function api_get_lancamento_details($type, $fid, $t = null)
    {
        if ($type == 'agua') {

            $where = "";
            if (!is_null($t))
                $where = "AND esm_fechamentos_energia_entradas.type = $t";

            $result = $this->db->query("
                SELECT 
                    esm_medidores.nome AS device,
                    esm_unidades.nome AS name,
                    LPAD(ROUND(leitura_anterior), 6, '0') AS previous_read,
                    LPAD(ROUND(leitura_atual), 6, '0') AS current_read,
                    ROUND(consumo) AS consumption,
                    ROUND(consumo_o) AS consumption_opened,
                    ROUND(consumo_c) AS consumption_closed
                FROM 
                    esm_fechamentos_agua_entradas
                JOIN 
                    esm_medidores ON esm_medidores.nome = esm_fechamentos_agua_entradas.device
                JOIN 
                    esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE 
                    esm_fechamentos_agua_entradas.fechamento_id = $fid
                    $where
            ");

        } else if ($type == 'energia') {

            $where = "";
            if (!is_null($t))
                $where = "AND esm_fechamentos_energia_entradas.type = $t";

            $result = $this->db->query("
                SELECT 
                    esm_medidores.nome AS device,
                    esm_unidades.nome AS name,
                    LPAD(ROUND(leitura_anterior), 6, '0') AS previous_read,
                    LPAD(ROUND(leitura_atual), 6, '0') AS current_read,
                    consumo AS consumption,
                    consumo_p AS consumption_ponta,
                    consumo_f AS consumption_fora,
                    demanda AS demand,
                    demanda_p AS demand_ponta,
                    demanda_f AS demand_fora
                FROM 
                    esm_fechamentos_energia_entradas
                JOIN 
                    esm_medidores ON esm_medidores.nome = esm_fechamentos_energia_entradas.device
                JOIN 
                    esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
                WHERE 
                    esm_fechamentos_energia_entradas.fechamento_id = $fid
                    $where
            ");
        }

        if ($result->num_rows()) {

            if ($type == 'agua') {

                $data = $result->result();

                foreach ($data as $d) {
                    $d->consumption        = floatval($d->consumption);
                    $d->consumption_opened = floatval($d->consumption_opened);
                    $d->consumption_closed = floatval($d->consumption_closed);
                }

                return $data;

            } else if ($type == 'energia') {

                $data = $result->result();

                foreach ($data as $d) {
                    $d->consumption        = floatval($d->consumption);
                    $d->consumption_ponta = floatval($d->consumption_ponta);
                    $d->consumption_fora = floatval($d->consumption_fora);
                    $d->demand = floatval($d->demand);
                    $d->demand_ponta = floatval($d->demand_ponta);
                    $d->demand_fora = floatval($d->demand_fora);
                }

                return $data;
            }
        }

        return false;
    }

    public function api_get_lancamento_message($type, $fid)
    {
        if ($type == 'agua') {

            $result = $this->db->query("
                SELECT 
                    mensagem
                FROM 
                    esm_fechamentos_agua
                WHERE 
                    id = $fid
            ");

        } else if ($type == 'energia') {

            $result = $this->db->query("
                SELECT 
                    mensagem
                FROM 
                    esm_fechamentos_energia
                WHERE 
                    id = $fid
            ");
        }

        if ($result->num_rows()) {
            return $result->row();
        }

        return false;
    }

    public function VerifyCompetencia($type, $entrada_id, $competencia)
    {
        $result = $this->db->query("
            SELECT id
            FROM esm_fechamentos_$type
            WHERE entrada_id = $entrada_id AND competencia = '$competencia'
        ");

        return ($result->num_rows() > 0);
    }

    private function CalculateQuery($data, $inicio, $fim, $type, $config)
    {
        $query = $this->db->query("
            SELECT
                {$data['id']} AS fechamento_id,
                esm_medidores.nome AS device,
                esm_unidades_config.type AS type,
                a.leitura_anterior,
                a.leitura_atual,
                a.consumo,
                o.consumo AS consumo_o,
                c.consumo AS consumo_c
            FROM esm_medidores
            LEFT JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id AND esm_unidades_config.type = $type
            LEFT JOIN (
                SELECT 
                    medidor_id,
                    MIN(leitura) AS leitura_anterior,
                    MAX(leitura) AS leitura_atual,
                    MAX(leitura) - MIN(leitura) AS consumo
                FROM esm_leituras_ancar_agua
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                GROUP BY medidor_id
            ) a ON a.medidor_id = esm_medidores.id
            LEFT JOIN (
                SELECT 
                    medidor_id,
                    SUM(consumo) AS consumo
                FROM esm_leituras_ancar_agua
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                    AND HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$config->open})) AND HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$config->close}))
                GROUP BY medidor_id
            ) o ON o.medidor_id = esm_medidores.id
            LEFT JOIN (
                SELECT 
                    medidor_id,
                    SUM(consumo) AS consumo
                FROM esm_leituras_ancar_agua
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
					AND (HOUR(FROM_UNIXTIME(timestamp)) <= HOUR(FROM_UNIXTIME({$config->open})) OR HOUR(FROM_UNIXTIME(timestamp)) > HOUR(FROM_UNIXTIME({$config->close})))
                GROUP BY medidor_id
            ) c ON c.medidor_id = esm_medidores.id
            WHERE 
                esm_medidores.entrada_id = {$data['entrada_id']}
        ");

        return $query;
    }

    private function calculate_query_energy($data, $inicio, $fim, $config, $type)
    {
        $query = $this->db->query("
            SELECT
                {$data['id']} AS fechamento_id,
                esm_medidores.nome AS device, 
                $type AS type,
                a.leitura_anterior,
                a.leitura_atual,
                a.consumo,
                p.consumo_p,
                f.consumo_f,
                a.demanda,
                p.demanda_p,
                f.demanda_f
            FROM esm_medidores
            JOIN esm_unidades ON esm_unidades.id = esm_medidores.unidade_id
            JOIN esm_unidades_config ON esm_unidades_config.unidade_id = esm_unidades.id AND esm_unidades_config.type = $type
            LEFT JOIN (
                SELECT 
                    device,
                    MIN(activePositive) AS leitura_anterior,
                    MAX(activePositive) AS leitura_atual,
                    MAX(activePositive) - MIN(activePositive) AS consumo,
                    MAX(activeDemand) AS demanda
                FROM esm_leituras_ancar_energia
                WHERE timestamp >= UNIX_TIMESTAMP('$inicio 00:00:00') AND timestamp <= UNIX_TIMESTAMP('$fim 00:00:00')
                GROUP BY device
            ) a ON a.device = esm_medidores.nome
            JOIN (  
                SELECT 
                    d.device,
                    SUM(activePositiveConsumption) AS consumo_p,
                    MAX(activeDemand) AS demanda_p
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    AND ((MOD((d.timestamp), 86400) >= {$config->ponta_start} AND MOD((d.timestamp), 86400) <= {$config->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7)
                WHERE 
                    esm_calendar.dt >= '$inicio' AND 
                    esm_calendar.dt < '$fim'
                GROUP BY device
            ) p ON p.device = esm_medidores.nome
            JOIN (  
                SELECT 
                    d.device,
                    SUM(activePositiveConsumption) AS consumo_f,
                    MAX(activeDemand) AS demanda_f
                FROM esm_calendar
                LEFT JOIN esm_leituras_ancar_energia d ON 
                    (d.timestamp) > (esm_calendar.ts_start) AND 
                    (d.timestamp) <= (esm_calendar.ts_end + 600) 
                    AND (((MOD((d.timestamp), 86400) < {$config->ponta_start} OR MOD((d.timestamp), 86400) > {$config->ponta_end}) AND esm_calendar.dw > 1 AND esm_calendar.dw < 7) OR esm_calendar.dw = 1 OR esm_calendar.dw = 7)
                WHERE 
                    esm_calendar.dt >= '$inicio' AND 
                    esm_calendar.dt < '$fim'
                GROUP BY device
            ) f ON f.device = esm_medidores.nome
            WHERE 
                entrada_id = {$data['entrada_id']}
        ");

        return $query;
    }

    public function Calculate($data, $config)
    {
        $inicio = $data["inicio"];
        $fim    = $data["fim"];

        $data["inicio"] = date_create_from_format('Y-m-d H:i', $data["inicio"] . ' 00:00')->format('U');
        $data["fim"]    = date_create_from_format('Y-m-d H:i', $data["fim"] . ' 00:00')->format('U');

        // inicia transação
        $failure = array();
        $this->db->trans_start();

        // insere novo registro
        if (!$this->db->insert('esm_fechamentos_agua', $data)) {
            // se erro, salva info do erro
            $failure[] = $this->db->error();
        }

        // retorna fechamento id
        $data['id'] = $this->db->insert_id();

        $query = $this->CalculateQuery($data, $inicio, $fim, 1, $config);

        $comum       = $query->result();
        $consumo_c   = 0;
        $consumo_c_c = 0;
        $consumo_c_o = 0;

        foreach ($comum as $c) {
            $consumo_c   += $c->consumo;
            $consumo_c_c += $c->consumo_c;
            $consumo_c_o += $c->consumo_o;
        }

        $query = $this->CalculateQuery($data, $inicio, $fim, 2, $config);

        $unidades    = $query->result();
        $consumo_u   = 0;
        $consumo_u_c = 0;
        $consumo_u_o = 0;

        foreach ($unidades as $u) {
            $consumo_u   += $u->consumo;
            $consumo_u_c += $u->consumo_c;
            $consumo_u_o += $u->consumo_o;
        }

        // inclui dados na tabela esm_fechamentos_entradas
        if (!$this->db->insert_batch('esm_fechamentos_agua_entradas', $comum)) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->insert_batch('esm_fechamentos_agua_entradas', $unidades)) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->update('esm_fechamentos_agua', array(
            'consumo_c'        => $consumo_c,
            'consumo_u'        => $consumo_u,
            'consumo_c_c'      => $consumo_c_c,
            'consumo_c_o'      => $consumo_c_o,
            'consumo_u_c'      => $consumo_u_c,
            'consumo_u_o'      => $consumo_u_o,
        ), array('id' => $data['id']))) {

            $failure[] = $this->db->error();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "accounting" => $data['id']));
        }
    }

    public function calculate_energy($data, $config)
    {
        $inicio = $data["inicio"];
        $fim    = $data["fim"];

        $data["inicio"] = date_create_from_format('Y-m-d H:i', $data["inicio"] . ' 00:00')->format('U');
        $data["fim"]    = date_create_from_format('Y-m-d H:i', $data["fim"] . ' 00:00')->format('U');

        // inicia transação
        $failure = array();
        $this->db->trans_start();

        // insere novo registro
        if (!$this->db->insert('esm_fechamentos_energia', $data)) {
            // se erro, salva info do erro
            $failure[] = $this->db->error();
        }

        // retorna fechamento id
        $data['id'] = $this->db->insert_id();

        $query = $this->calculate_query_energy($data, $inicio, $fim, $config, 1);

        $comum       = $query->result();
        $consumo_c   = 0;
        $consumo_c_f = 0;
        $consumo_c_p = 0;
        $demanda_c_f = 0;
        $demanda_c_p = 0;

        foreach ($comum as $c) {
            $consumo_c   += $c->consumo;
            $consumo_c_p += $c->consumo_p;
            $consumo_c_f += $c->consumo_f;
            $demanda_c_f  = ($c->demanda_f > $demanda_c_f) ? $c->demanda_f : $demanda_c_f;
            $demanda_c_p  = ($c->demanda_p > $demanda_c_p) ? $c->demanda_p : $demanda_c_p;
        }

        $query = $this->calculate_query_energy($data, $inicio, $fim, $config, 2);

        $unidades    = $query->result();
        $consumo_u   = 0;
        $consumo_u_p = 0;
        $consumo_u_f = 0;
        $demanda_u_f = 0;
        $demanda_u_p = 0;

        foreach ($unidades as $u) {
            $consumo_u   += $u->consumo;
            $consumo_u_p += $u->consumo_p;
            $consumo_u_f += $u->consumo_f;
            $demanda_u_f  = ($u->demanda_f > $demanda_u_f) ? $u->demanda_f : $demanda_u_f;
            $demanda_u_p  = ($u->demanda_p > $demanda_u_p) ? $u->demanda_p : $demanda_u_p;
        }

        // inclui dados na tabela esm_fechamentos_entradas
        if (!$this->db->insert_batch('esm_fechamentos_energia_entradas', $comum)) {
            $failure[] = $this->db->error();
        }

        if (!$this->db->insert_batch('esm_fechamentos_energia_entradas', $unidades)) {
            $failure[] = $this->db->error();
        }

        // atualiza tabela esm_fechamento com dados da área comum
        if (!$this->db->update('esm_fechamentos_energia', array(
            'consumo'          => $consumo_c,
            'consumo_p'        => $consumo_c_p,
            'consumo_f'        => $consumo_c_f,
            'demanda'          => ($demanda_c_p > $demanda_c_f) ? $demanda_c_p : $demanda_c_f,
            'demanda_p'        => $demanda_c_p,
            'demanda_f'        => $demanda_c_f,
            'consumo_u'        => $consumo_u,
            'consumo_u_p'      => $consumo_u_p,
            'consumo_u_f'      => $consumo_u_f ,
            'demanda_u'        => ($demanda_u_p > $demanda_u_f) ? $demanda_u_p : $demanda_u_f,
            'demanda_u_p'      => $demanda_u_p,
            'demanda_u_f'      => $demanda_u_f
        ), array('id' => $data['id']))) {

            $failure[] = $this->db->error();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return json_encode(array("status"  => "error", "message" => $failure[0]));
        } else {
            return json_encode(array("status"  => "success", "accounting" => $data['id']));
        }
    }
}
