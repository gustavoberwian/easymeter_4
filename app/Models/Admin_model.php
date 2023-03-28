<?php

namespace App\Models;

class Admin_model extends Base_model
{
    public function get_all_centrais()
    {
        // realiza a consulta
        $query = $this->db->query("
            SELECT esm_condominios_centrais.nome as DT_RowId, esm_condominios_centrais.nome, esm_condominios_centrais.modo, esm_condominios_centrais.board, 
                esm_condominios_centrais.firmware, esm_condominios_centrais.simcard, esm_condominios.nome AS condo, esm_condominios.tabela, 
                esm_condominios_centrais.parent, esm_condominios_centrais.auto_ok, esm_condominios_centrais.ultimo_envio,
                esm_condominios_centrais.localizador, esm_condominios_centrais.tamanho, esm_central_data.fonte, esm_central_data.tensao, esm_central_data.fraude_hi, esm_central_data.fraude_low
            FROM esm_condominios_centrais 
            JOIN esm_condominios ON esm_condominios.id = esm_condominios_centrais.condo_id
            LEFT JOIN esm_central_data ON esm_central_data.nome = esm_condominios_centrais.nome AND esm_central_data.timestamp = esm_condominios_centrais.ultimo_envio
            ORDER BY esm_condominios.ordem, esm_condominios_centrais.condo_id, esm_condominios_centrais.nome
        ");

        return $query->getResult();
    }

    public function get_condo($id)
    {
        // seleciona todos os campos
        $query = $this->db->table('esm_condominios')
            ->select('esm_condominios.*, esm_administradoras.nome AS nome_adm, esm_pessoas.nome as nome_sindico')
            ->join('esm_administradoras', 'esm_condominios.admin_id = esm_administradoras.id', 'LEFT')
            ->join('esm_pessoas', 'esm_condominios.sindico_id = esm_pessoas.id', 'LEFT')
            ->where('esm_condominios.id', $id)
            ->where('esm_condominios.visibility', 'normal')
            ->get();

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getRow();
    }

    public function get_blocos($condo)
    {
        $query = $this->db->table('esm_blocos')
            ->where('esm_blocos.condo_id', $condo)
            ->orderBy('nome', 'ASC')
            ->get();

        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }

    public function get_ramais($condo, $as_string = false, $tipo = '')
    {
        $query = $this->db->table('esm_ramais')->orderBy('id')->where('condo_id', $condo);

        if ($tipo != '')
            $query->where('tipo', $tipo);

        $query = $query->get();

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        if ($as_string) {
            $ramais = $query->getResult();
            $ret = '';
            foreach ($ramais as $r) {
                $ret = $ret . ',' . $r->nome;
            }

            return substr($ret, 1);
        } else {
            return $query->getResult();
        }
    }

    public function get_centrais($condo, $as_string = false)
    {
        $query = $this->db->table('esm_condominios_centrais')
            ->where('condo_id', $condo)
            ->orderBy('id')
            ->get();

        if ($query->getNumRows() == 0)
            return false;

        if ($as_string) {
            $centrais = $query->getResult();
            $ret = '';
            foreach ($centrais as $c) {
                $ret = $ret . ',' . $c->nome;
            }

            return substr($ret, 1);
        } else {
            return $query->getResult();
        }
    }

    public function get_entradas($condo_id)
    {
        $query = $this->db->query("
            SELECT id AS eid, nome AS entrada, tipo
            FROM esm_entradas
            WHERE condo_id = $condo_id
            ORDER BY FIELD(tipo, 'agua', 'gas', 'energia'), ordem, id
        ");

        // verifica se retornou algo
        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }

    public function get_central_leituras($central, $tabela)
    {
        return "
            SELECT DATE_FORMAT(FROM_UNIXTIME(timestamp),'%d/%m/%Y') AS label, COUNT(*) AS leituras
            FROM esm_leituras_{$tabela}_agua
            WHERE timestamp >= UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 40 DAY)) AND medidor_id = (SELECT id FROM esm_medidores WHERE central = '$central' LIMIT 1)
            GROUP BY year(FROM_UNIXTIME(`timestamp`)), month(FROM_UNIXTIME(`timestamp`)), day(FROM_UNIXTIME(`timestamp`))
        ";
        $query = $this->db->query("
            SELECT DATE_FORMAT(FROM_UNIXTIME(timestamp),'%d/%m/%Y') AS label, COUNT(*) AS leituras
            FROM esm_leituras_{$tabela}_agua
            WHERE timestamp >= UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 40 DAY)) AND medidor_id = (SELECT id FROM esm_medidores WHERE central = '$central' LIMIT 1)
            GROUP BY year(FROM_UNIXTIME(`timestamp`)), month(FROM_UNIXTIME(`timestamp`)), day(FROM_UNIXTIME(`timestamp`))
        ");

        $values = array();
        if ($query->getNumRows() == 0)
            return false;

        foreach ($query->getResult() as $r) {
            $values[$r->label] = $r->leituras;
        }

        return $values;
    }

    public function get_medidores_unidade($unidade_id)
    {
        $query = $this->db->query("
            SELECT esm_medidores.id, esm_medidores.sensor_id, esm_medidores.nome, esm_medidores.central, esm_medidores.posicao, 
            esm_medidores.fator, esm_entradas.id AS eid, esm_entradas.nome AS entrada, esm_entradas.tipo
            FROM esm_medidores
            JOIN esm_entradas ON esm_entradas.id = esm_medidores.entrada_id
            WHERE unidade_id = $unidade_id
            ORDER BY FIELD(esm_entradas.tipo, 'agua', 'gas', 'energia'), esm_entradas.ordem, id
        ");

        if ($query->getNumRows() == 0)
            return false;

        return $query->getResult();
    }
}