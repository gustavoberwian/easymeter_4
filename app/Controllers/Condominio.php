<?php

namespace App\Controllers;

use App\Models\Condominio_model;
use App\Models\Energy_model;
use App\Models\Water_model;
use App\Models\Gas_model;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Condominio extends UNO_Controller
{
    protected $input;
    protected $email;

    /**
     * @var Datatables
     */
    protected Datatables $datatables;

    /**
     * @var Condominio_model
     */
    private Condominio_model $condominio_model;

    /**
     * @var Energy_model
     */
    private Energy_model $energy_model;

    /**
     * @var Water_model
     */
    private Water_model $water_model;

    /**
     * @var Water_model
     */
    private Gas_model $gas_model;

    public $url;
    public $user;

    public function __construct()
    {
        parent::__construct();

        // load services
        $this->input = \Config\Services::request();
        $this->email = \Config\Services::email();

        // load models
        $this->condominio_model = new Condominio_model();
        $this->energy_model = new Energy_model();
        $this->water_model = new Water_model();
        $this->gas_model = new Gas_model();

        // load libraries
        $this->datatables = new Datatables(new Codeigniter4Adapter);

        // set variables
        $this->url = service('uri')->getSegment(1);

        $this->user->unidade = (object)[];
        $this->user->entity = (object)[];

        if ($this->user->inGroup('superadmin')) {
            $this->user->entity->classificacao = $this->user->page;
        } else if ($this->user->inGroup('admin', 'unity')) {
            $this->user->entity = $this->condominio_model->get_entidade_by_user($this->user->id);
            $this->user->unidade = $this->condominio_model->get_unidade_by_user($this->user->id);
        } else if ($this->user->inGroup('admin')) {
            $this->user->entity = $this->condominio_model->get_entidade_by_user($this->user->id);
        } else if ($this->user->inGroup('unity')) {
            $this->user->unidade = $this->condominio_model->get_unidade_by_user($this->user->id);
        }
    }

    public function index()
    {
        $data['user'] = $this->user;
        $data['url'] = $this->url;

        // busca aviso se ultimo aviso recebido não estiver lido
        $data['aviso'] = $this->condominio_model->get_last_aviso($this->user->id);

        if ($this->user->inGroup('unity') or $this->user->inGroup('admin')) {
            // leitura atual do monitamento da unidade
            $data['leitura_agua']    = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'agua', $this->user->unidade->id);
            $data['leitura_gas']     = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'gas', $this->user->unidade->id);
            $data['leitura_energia'] = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'energia', $this->user->unidade->id);

            // busca resumo do consumo da água
            $data['hora_agua'] = $this->condominio_model->get_consumo_ultima_hora($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['hoje_agua'] = $this->condominio_model->get_consumo_hoje($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['last_agua'] = $this->condominio_model->get_consumo_last_24($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['fatu_agua'] = $this->condominio_model->get_consumo_last_fechamento($this->user->unidade->id, $this->user->entity->tabela, 'agua');

            $data['hora_gas'] = $this->condominio_model->get_consumo_ultima_hora($this->user->unidade->id, $this->user->entity->tabela, 'gas');
            $data['hoje_gas'] = $this->condominio_model->get_consumo_hoje($this->user->unidade->id, $this->user->entity->tabela, 'gas');
            $data['last_gas'] = $this->condominio_model->get_consumo_last_24($this->user->unidade->id, $this->user->entity->tabela, 'gas');
            $data['fatu_gas'] = $this->condominio_model->get_consumo_last_fechamento($this->user->unidade->id, $this->user->entity->tabela, 'gas');

            $data['hora_energia'] = $this->condominio_model->get_consumo_ultima_hora($this->user->unidade->id, $this->user->entity->tabela, 'energia');
            $data['hoje_energia'] = $this->condominio_model->get_consumo_hoje($this->user->unidade->id, $this->user->entity->tabela, 'energia');
            $data['last_energia'] = $this->condominio_model->get_consumo_last_24($this->user->unidade->id, $this->user->entity->tabela, 'energia');
            $data['fatu_energia'] = $this->condominio_model->get_consumo_last_fechamento($this->user->unidade->id, $this->user->entity->tabela, 'energia');

            $data['ultima_leitura'] = $this->condominio_model->get_last_leitura($this->user->unidade->id, $this->user->entity->tabela, 'agua');
            $data['central']        = $this->condominio_model->get_central_by_unidade($this->user->unidade->id);
        }

        // renderiza pagina
        echo $this->render('index', $data);
    }

    public function agua($unidade_id = false)
    {
        // TODO: verificar se é sindico do condo da unidade pra poder ver

        // se unidade_id não definida, usa a unidade do usuário logado
        $data['acesso'] = $unidade_id;
        if (!$data['acesso']) {
            $unidade_id = $this->user->unidade->id;
            $data['unidade'] = $this->user->unidade;
        } else {
            $data['unidade'] = $this->condominio_model->get_unidade($unidade_id);
        }

        // user
        $data['user'] = $this->user;
        $data['url'] = $this->url;
        // busca lista das entradas de água da unidade do usuário
        $data['entradas'] = $this->condominio_model->get_entradas($this->user->entity->id, 'agua');
        // leitura atual do monitamento da unidade
        $data['leitura'] = $this->condominio_model->get_consumo_unidade($this->user->entity->tabela, 'agua', $unidade_id);
        // busca últimos 3 alertas de água recebidos pelo usuário
        $data['alertas'] = $this->condominio_model->get_alertas($this->user->id, '', 'agua', 3, true);
        // busca dados do último faturamento realizado
        $ultimo = $this->condominio_model->get_ultimo_faturamento($this->user->entity->id);
        // busca data da primeira leitura
        $primeira_leitura = $this->condominio_model->get_primeira_leitura($this->user->entity->tabela, 'agua', $unidade_id);
        $data['primeira_leitura'] = date("d/m/Y", $primeira_leitura);
        $data['ultima_leitura'] = ($data['acesso']) ? time() : $this->condominio_model->get_last_leitura($unidade_id, $this->user->entity->tabela, 'agua');
        $data['central'] = $this->condominio_model->get_central_by_unidade($unidade_id);

        // se possui faturamento usa a data final, senão últimos 30 dias
        $valor_litro = 0;
        if ($ultimo) {
            // se data mais de 35 dias, usa ultimos 30
            if ($ultimo->data_fim < strtotime('-35 days')) {
                $data_desde = strtotime('-30 days');
                $data['aviso'] = '<i class="fas fa-exclamation-circle ml-1 text-danger" data-toggle="tooltip" data-placement="bottom" title="O último faturamento realizado é antigo, usando últimos 30 dias como referência"></i>';
                $valor_litro = $ultimo->v_litro;
            } else {
                $data_desde = $ultimo->data_fim;
                $data['aviso'] = '<i class="fas fa-info-circle ml-1" data-toggle="tooltip" data-placement="bottom" title="Desde ' . date('d/m/Y', $data_desde + 86400) . '"></i>';
                $valor_litro = $ultimo->v_litro;
            }
        } else {
            $data_desde = ($primeira_leitura > strtotime('-30 days')) ? $primeira_leitura : strtotime('-30 days');
            $data['aviso'] = '<i class="fas fa-exclamation-circle ml-1 text-danger" data-toggle="tooltip" data-placement="bottom" title="Nenhum faturamento realizado, usando últimos ' . (floor((time() - $data_desde) / 86400) + 1) . ' dias como referência"></i>';
        }
        // busca consumo unidade desde o último faturamento ou 30 dias
        $consumo_faturamento = $this->condominio_model->get_consumo_desde($unidade_id, $this->user->entity->tabela, 'agua', strtotime(date('Y-m-d', $data_desde) . ' 23:59'));
        $data['voce'] = number_format($consumo_faturamento, 0, '', '.');

        // busca consumo dos vizinhos desde o último faturamento ou 30 dias
        $vizinhos = $this->condominio_model->get_consumo_vizinhos_desde($unidade_id, $this->user->entity->tabela, 'agua', strtotime(date('Y-m-d', $data_desde) . ' 23:59'));
        // busca numero de medidores do condomínio, menos os da unidade
        $unidades_count = $this->condominio_model->get_medidores_count($unidade_id, $this->user->entity->id, 'agua');
        $data['vizinhos'] = ($unidades_count == 0) ? 0 : number_format($vizinhos->consumo / $unidades_count, 0, '', '.');
        // calcular valor consumo brasil: consumo médio x numero de dias do período
        //TODO: como definir o valor médio?
        $data['brasil'] = number_format(2.66 * 110 * (strtotime('now') - $data_desde) / 86400, 0, '', '.'); // 2.66 moradores médios Metrop. Porto Alegre fipe, 110 l/dia ONU

        // previsão
        $data['previsao']['mostra'] = false;
        if ($ultimo) {
            // até agora
            $dias = floor((time() - $data_desde) / 86400);
            if ($dias == 0)
                $dias = 1;

            $previsao_consumo = $consumo_faturamento / $dias * 31;

            $faturamento_unidade = $this->condominio_model->get_faturamento_unidade($ultimo->id,  $unidade_id);

            if ($faturamento_unidade) {
                $data['previsao']['ate_agora'] = number_format($consumo_faturamento * $valor_litro, 2, ',', '.');

                $data['previsao']['fechamento'] = number_format($previsao_consumo * $valor_litro + $faturamento_unidade->v_basico, 2, ',', '.');
                $data['previsao']['comparativo'] = ($previsao_consumo > $faturamento_unidade->consumo);

                $data['previsao']['mostra'] = true;
            }
        }

        // renderiza página
        echo $this->render('agua', $data);
    }


    /////////////////////////
    /// REQUESTS
    /////////////////////////

    public function md_chamado()
    {
        echo view('Condominio/modals/chamado');
    }

    public function new_chamado()
    {
        $ass = $this->input->getPost('a');
        $msg = $this->input->getPost('m');
        $this->user->email = $this->condominio_model->get_user_email($this->user->id);

        $ret = $this->condominio_model->new_chamado($this->user, $ass, $msg);

        if ($ret['status'] == 'success') {
            //envia email
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'email-ssl.com.br';
            $config['smtp_port'] = '587';
            $config['smtp_timeout'] = '60';
            $config['smtp_user'] = 'contato@easymeter.com.br';
            $config['smtp_pass'] = 'index#1996';
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['mailtype'] = "html";
            $config['smtp_crypto'] = 'tls';

            $this->email->initialize($config);

            $data['cid'] = date('Y') . str_pad($ret['id'], 6, "0", STR_PAD_LEFT);
            $data['titulo'] = $ret['assunto'];
            $data['nome'] = $this->user->username;
            $data['msg'] = $msg;
            $data['prev'] = date('d/m/Y', strtotime("+2 days", time()));

            $this->email->setFrom('contato@easymeter.com.br', "Easymeter");
            $this->email->setTo($this->user->email);
            $this->email->setReplyTo('contato@easymeter.com.br');
            $this->email->setSubject('Suporte Easymeter');
            $this->email->setMessage(view('Condominio/emails/suporte', $data));

            $this->email->send();
        }

        echo json_encode($ret);
    }

    public function get_leitura()
    {
        $leitura = $this->condominio_model->get_leitura_unidade($this->user->condo->tabela, $this->user->unidade->id);

        if ($leitura) {
            //$this->user->unidade->leitura_agua = $leitura->leitura_agua;

            echo json_encode(array('status' => 'success', 'data' => $leitura));
        } else
            echo json_encode(array('status' => 'success', 'data' => false));
    }
}