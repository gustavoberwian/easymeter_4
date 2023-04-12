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

        // load requests
        $this->input = \Config\Services::request();

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


    /////////////////////////
    /// REQUESTS
    /////////////////////////

    public function md_chamado()
    {
        echo view('Condominio/modals/md_chamado');
    }
}