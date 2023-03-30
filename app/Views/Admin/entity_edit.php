<section role="main" class="content-body">
    <header class="page-header">
        <h2>Condomínio <?php echo $condo->nome; ?></h2>
        <div class="right-wrapper text-end">
            <ol class="breadcrumbs pe-4">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><a href="<?php echo site_url('admin/condominios'); ?>"><span>Condomínios</span></a></li>
                <li><span><?php echo $condo->nome; ?></span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <div class="row">
        <div class="col">
            <section class="card">
                <form class="form-horizontal form-bordered form-condo">
                    <input id="id-condo" name="id-condo" type="hidden" value="<?php if (isset($condo->id)) echo $condo->id; ?>" readonly>
                    <header class="card-header">
                        <h2 class="card-title"><?php echo $title; ?></h2>
                        <div class="card-actions buttons">
                            <div class="btn-group">
                                <button class="btn btn-primary btn-cadastro active">Dados</button>
                                <button class="btn btn-primary btn-entradas">Entradas</button>
                                <button class="btn btn-primary btn-unidades">Unidades</button>
                                <button class="btn btn-primary btn-leituras">Leituras</button>
                                <button class="btn btn-primary btn-configuracoes">Configurações</button>
                                <button class="btn btn-primary btn-consumo">Fechamentos</button>
                                <?php if ($geral): ?>
                                    <button class="btn btn-primary btn-geral" data-central="<?php if (isset($central_geral)) echo $central_geral; ?>" data-unidade="<?php if (isset($unidade_geral)) echo $unidade_geral; ?>">Medidor Geral</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <div class="tab-form cadastro">
                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Nome <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="nome-condo" name="nome-condo" type="text" value="<?php if (isset($condo->nome)) echo $condo->nome; ?>" class="form-control vnome" placeholder="Razão Social do Condomínio" <?php echo $readonly; ?> required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">CNPJ <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cnpj-condo" name="cnpj-condo" value="<?php if (isset($condo->cnpj)) echo $condo->cnpj; ?>" placeholder="___.___.___/____-__" class="form-control vcnpj" <?php echo $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="tipo-condo" name="tipo-condo" class="form-control" required <?php if ($readonly != '') echo 'disabled="true"';?>>
                                                    <?php if (isset($condo->tipo)) $condo_tipo = $condo->tipo;?>
                                                    <option disabled value="">Tipo do Condomínio</option>
                                                    <option <?php if ($condo_tipo == 'vertical') echo 'selected '; ?> value="vertical">Residencial Vertical</option>
                                                    <option <?php if ($condo_tipo == 'horizontal') echo 'selected '; ?> value="horizontal">Residencial Horizontal</option>
                                                    <option <?php if ($condo_tipo == 'comercial') echo 'selected '; ?> value="comercial">Comercial</option>
                                                    <option <?php if ($condo_tipo == 'misto') echo 'selected '; ?> value="misto">Misto</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">CEP<span class="required">*</span></label>
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <input id="cep-condo" name="cep-condo" type="text" value="<?php if (isset($condo->cep)) echo $condo->cep; ?>" placeholder="_____-___" class="form-control vcep" <?php echo $readonly; ?> required>
                                        <span class="input-group-append">
															<button class="btn btn-success btn-busca overlay-small" type="button" data-loading-overlay disabled>Completar</button>
														</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Logradouro <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="logradouro-condo" name="logradouro-condo" type="text" value="<?php if (isset($condo->logradouro)) echo $condo->logradouro; ?>" class="form-control" placeholder="Nome da rua/avenida" <?php echo $readonly; ?> required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Número/Complemento <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="numero-condo" name="numero-condo" value="<?php if (isset($condo->numero)) echo $condo->numero; ?>" placeholder="Número" class="form-control" <?php echo $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="complemento-condo" name="complemento-condo" value="<?php if (isset($condo->complemento)) echo $condo->complemento; ?>" placeholder="Complemento" class="form-control" <?php echo $readonly; ?> >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Bairro <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="bairro-condo" name="bairro-condo" type="text" value="<?php if (isset($condo->bairro)) echo $condo->bairro; ?>" class="form-control" placeholder="Nome do bairro" <?php echo $readonly; ?> required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Cidade/Estado <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cidade-condo" name="cidade-condo" value="<?php if (isset($condo->cidade)) echo $condo->cidade; ?>" placeholder="Cidade" class="form-control" <?php echo $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="estado-condo" name="estado-condo" class="form-control" required <?php if ($readonly != '') echo 'disabled="true"';?>>
                                                    <?php $condo_uf = (is_null($condo->uf)) ? $condo->uf : ''?>
                                                    <option <?php if (is_null($condo_uf)) echo 'selected '; ?> disabled value="">Estado</option>
                                                    <option <?php if ($condo_uf == 'AC') echo 'selected '; ?> value="AC">Acre</option>
                                                    <option <?php if ($condo_uf == 'AL') echo 'selected '; ?> value="AL">Alagoas</option>
                                                    <option <?php if ($condo_uf == 'AP') echo 'selected '; ?> value="AP">Amapá</option>
                                                    <option <?php if ($condo_uf == 'AM') echo 'selected '; ?> value="AM">Amazonas</option>
                                                    <option <?php if ($condo_uf == 'BA') echo 'selected '; ?> value="BA">Bahia</option>
                                                    <option <?php if ($condo_uf == 'CE') echo 'selected '; ?> value="CE">Ceará</option>
                                                    <option <?php if ($condo_uf == 'DF') echo 'selected '; ?> value="DF">Distrito Federal</option>
                                                    <option <?php if ($condo_uf == 'ES') echo 'selected '; ?> value="ES">Espírito Santo</option>
                                                    <option <?php if ($condo_uf == 'GO') echo 'selected '; ?> value="GO">Goiás</option>
                                                    <option <?php if ($condo_uf == 'MA') echo 'selected '; ?> value="MA">Maranhão</option>
                                                    <option <?php if ($condo_uf == 'MT') echo 'selected '; ?> value="MT">Mato Grosso</option>
                                                    <option <?php if ($condo_uf == 'MS') echo 'selected '; ?> value="MS">Mato Grosso do Sul</option>
                                                    <option <?php if ($condo_uf == 'MG') echo 'selected '; ?> value="MG">Minas Gerais</option>
                                                    <option <?php if ($condo_uf == 'PA') echo 'selected '; ?> value="PA">Pará</option>
                                                    <option <?php if ($condo_uf == 'PB') echo 'selected '; ?> value="PB">Paraíba</option>
                                                    <option <?php if ($condo_uf == 'PR') echo 'selected '; ?> value="PR">Paraná</option>
                                                    <option <?php if ($condo_uf == 'PE') echo 'selected '; ?> value="PE">Pernambuco</option>
                                                    <option <?php if ($condo_uf == 'PI') echo 'selected '; ?> value="PI">Piauí</option>
                                                    <option <?php if ($condo_uf == 'RJ') echo 'selected '; ?> value="RJ">Rio de Janeiro</option>
                                                    <option <?php if ($condo_uf == 'RN') echo 'selected '; ?> value="RN">Rio Grande do Norte</option>
                                                    <option <?php if ($condo_uf == 'RS') echo 'selected '; ?> value="RS">Rio Grande do Sul</option>
                                                    <option <?php if ($condo_uf == 'RO') echo 'selected '; ?> value="RO">Rondônia</option>
                                                    <option <?php if ($condo_uf == 'RR') echo 'selected '; ?> value="RR">Roraima</option>
                                                    <option <?php if ($condo_uf == 'SC') echo 'selected '; ?> value="SC">Santa Catarina</option>
                                                    <option <?php if ($condo_uf == 'SP') echo 'selected '; ?> value="SP">São Paulo</option>
                                                    <option <?php if ($condo_uf == 'SE') echo 'selected '; ?> value="SE">Sergipe</option>
                                                    <option <?php if ($condo_uf == 'TO') echo 'selected '; ?> value="TO">Tocantins</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Administradora</label>
                                <div class="col-lg-6">
                                    <select id="select-adm" name="select-adm" class="form-control populate" data-url="<?php echo site_url('ajax/get_admnistadoras'); ?>" <?php if ($readonly != '') echo 'disabled="true"';?>>
                                        <option><?php if (isset($condo->nome_adm)) echo $condo->nome_adm; ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Síndico/Gestor <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <select id="select-sindico" name="select-sindico" class="form-control populate" data-url="<?php echo site_url('ajax/get_sindicos'); ?>" <?php if ($readonly != '') echo 'disabled="true"';?> required>
                                        <option><?php if (isset($condo->nome_sindico)) echo $condo->nome_sindico; ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Inicio e Fim do Mandato <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="inicio-condo" name="inicio-condo" value="<?php if (isset($condo->inicio)) echo $condo->inicio; ?>" placeholder="__/__/____" class="form-control vdate" <?php echo $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="fim-condo" name="fim-condo" value="<?php if (isset($condo->fim)) echo $condo->fim; ?>" placeholder="__/__/____" class="form-control vdate" <?php echo $readonly; ?> required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Monitoramento</label>
                                <div class="col-lg-6 align-self-center">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" id="agua-condo" name="agua-condo" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($condo->m_agua) and $condo->m_agua == 1) echo 'checked'; ?>>
                                                <label for="agua-condo">Água</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-warning">
                                                <input type="checkbox" id="gas-condo" name="gas-condo" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($condo->m_gas) and $condo->m_gas == 1) echo 'checked'; ?>>
                                                <label for="gas-condo">Gás</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-warning">
                                                <input type="checkbox" id="gas-condo-leitura" name="gas-condo-leitura" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($condo->m_gas) and $condo->m_gas == 2) echo 'checked'; ?>>
                                                <label for="gas-condo-leitura">Leitura do Gás</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-danger">
                                                <input type="checkbox" id="energia-condo" name="energia-condo" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($condo->m_energia) and $condo->m_energia == 1) echo 'checked'; ?>>
                                                <label for="energia-condo">Energia</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row ramais" <?php if (isset($condo->m_agua) and $condo->m_agua == 0) echo 'style="display:none;"'; ?>>
                                <label class="col-lg-3 control-label text-lg-right pt-2">Ramais <span class="required">*</span></label>
                                <div class="col-lg-6 <?php echo $readonly; ?>">
                                    <input id="ramais-condo" name="ramais-condo" data-role="tagsinput" data-tag-class="badge badge-info" class="form-control ramais-input <?php if (isset($condo->m_gas) and $condo->m_agua == 0) echo 'no-validate'; ?>" value="<?php if (isset($ramais)) echo $ramais; ?>" <?php if ($readonly != '') echo 'disabled="true"';?> required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Dados Proprietários</label>
                                <div class="col-lg-6 align-self-center <?php echo $readonly; ?>">
                                    <div class="checkbox-custom checkbox-default">
                                        <input type="checkbox" id="proprietarios-condo" name="proprietarios-condo" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($condo->d_proprietarios) and $condo->d_proprietarios == 1) echo 'checked'; ?>>
                                        <label for="fracao-condo">Cadastar nome e email dos proprietários?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Frações Ideais</label>
                                <div class="col-lg-6 align-self-center <?php echo $readonly; ?>">
                                    <div class="checkbox-custom checkbox-default">
                                        <input type="checkbox" id="fracao-condo" name="fracao-condo" <?php if(count($blocos) == 0 or $readonly != '') echo ' disabled'; ?> <?php if (isset($condo->fracao_ideal) and $condo->fracao_ideal == 1) echo 'checked'; ?>>
                                        <label for="fracao-condo">Cadastar as Frações Ideais das unidades para permitir rateio de valores da inadimplência?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Centrais Easymeter</label>
                                <div class="col-lg-6 <?php echo $readonly; ?>">
                                    <input id="centrais-condo" name="centrais-condo" class="form-control" value="<?php if (isset($centrais)) echo $centrais; ?>" <?php if ($readonly != '') echo 'disabled="true"';?>/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Observações</label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" rows="3" id="textareaAutosize" data-plugin-textarea-autosize <?php echo $readonly; ?>><?php echo service('uri')->getSegment(4); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Ativo</label>
                                <div class="col-lg-6">
                                    <div class="switch switch-sm switch-primary">
                                        <input type="checkbox" name="switch" data-plugin-ios-switch checked="checked" <?php if($readonly != '') echo ' disabled'; ?>/>
                                    </div>
                                </div>
                            </div>

                            <?php if($readonly == '') : ?>
                                <div class="row">
                                    <div class="col-lg-9 text-right">
                                        <button class="btn btn-primary btn-salvar mr-3">Salvar</button>
                                        <button type="reset" class="btn btn-back">Voltar</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- tab-form cadastro -->

                        <!-- tab-form entradas -->
                        <div class="tab-form entradas d-none">
                            <p>TODO: Cadastro dos tipos com diferentes configurações de entradas -> esm_entradas</p>
                        </div>
                        <!-- tab-form cadastro -->

                        <!-- tab-form unidades -->
                        <div class="tab-form unidades d-none<?php if(count($blocos) == 0) echo ' inactive'; ?>">
                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Bloco</label>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <select id="sel-bloco" name="sel-bloco" class="form-control">
                                            <?php foreach ($blocos as $bl) { ?>
                                                <option value="<?php echo $bl->id; ?>"><?= ($bl->nome == '') ? 'Não possui' : $bl->nome; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if ($readonly == '') { ?>
                                            <span class="input-group-append">
                                                                <button class="btn btn-warning btn-bloco-edit" type="button" title="Editar" <?php if(count($blocos) == 0 or $readonly != '') echo 'disabled'; ?>><i class="fas fa-edit"></i></button>
                                                                <button class="btn btn-success btn-bloco-add" type="button"  title="Adicionar"><i class="fas fa-plus"></i></button>                                                        
                                                                <button class="btn btn-danger btn-bloco-delete" type="button"  title="Excluir" <?php if(count($blocos) == 0 or $readonly != '') echo 'disabled'; ?>><i class="fas fa-times"></i></button>
                                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="alert alert-warning unidades">
                                        <strong>Atenção</strong> Antes de incluir unidades você deve configurar os blocos do condomínio.
                                    </div>
                                    <div class="unidades-container">
                                        <div class="mb-2 text-right">
                                            <button class="btn btn-success btn-inclui-unidade mr-2" type="button" <?php if(count($blocos) == 0 or $readonly != '') echo 'disabled'; ?>><i class="fa fa-plus"></i> Incluir Unidade</button>
                                            <button class="btn btn-primary btn-filter" type="button" <?php if(count($blocos) == 0 or $readonly != '') echo 'disabled'; ?>><i class="fa fa-filter"></i></button>
                                        </div>
                                        <table class="table table-bordered table-striped" id="dt-unidades">
                                            <thead>
                                            <tr role="row">
                                                <th width="4%">Apto</th>
                                                <th width="4%">Andar</th>
                                                <th width="4%">PIN</th>
                                                <th width="4%">Tipo</th>
                                                <th width="18%">Proprietário</th>
                                                <th width="16%">Email</th>
                                                <th width="12%">Telefone</th>
                                                <th width="30%">Entradas</th>
                                                <th width="5%">Ações</th>
                                            </tr>
                                            </thead>
                                        </table>
                                        <div class="legenda">
                                            <span class="badge badge-agua">Água</span>
                                            <span class="badge badge-gas">Gás</span>
                                            <span class="badge badge-energia">Energia Elétrica</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab-form unidades -->

                        <!-- tab-form leituras -->
                        <div class="tab-form leituras d-none">

                            <div class="row">
                                <div class="col">
                                    <table class="table table-responsive-md table-sm table-bordered mb-0">
                                        <thead>
                                        <tr>
                                            <th width="20">Central</td>
                                            <th colspan="<?= count($leituras); ?>">Leituras por Dia</td>
                                        </tr>
                                        </thead>
                                        </tbody>
                                        <?php
                                        $centrais = model('admin_model')->get_centrais($condo->id);
                                        foreach($centrais as $c) {
                                            echo "<tr>";
                                            echo "<td width='20'><a href='".site_url('admin/centrais/'.$c->nome)."' target='_blank'>{$c->nome}</a></td>";
                                            $x = model('admin_model')->get_central_leituras($c->nome, $condo->tabela);
                                            for($i = 0; $i < count($leituras); $i++) {
                                                $l = isset($x[$leituras[$i]]) ? $x[$leituras[$i]] : 0;
                                                echo '<td style="background-color:'.numberToColor($l < 25 ? $l : 0, 0, 24, ['#CC0000', '#EEEE00', '#4CAF50']).'!important;" title="'.$leituras[$i].': '.$l.'"></td>';
                                            }
                                            echo "</tr>";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                        </div>
                        <!-- tab-form leituras -->

                        <!-- tab-form configurações -->
                        <div class="tab-form configuracoes d-none">
                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Config 1 <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="conf1-condo" name="conf1-condo" type="text" value="" class="form-control" placeholder="Explicação" <?php echo $readonly; ?> required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Tipo de Tarifagem</label>
                                <div class="col-lg-6">
                                    <div class="radio-custom radio-primary">
                                        <input type="radio" id="tarifa-tipo1" name="tarifa-tipo" value="1" checked <?php if ( $readonly == 'readonly' ) echo 'disabled=""'; ?> >
                                        <label for="tarifa-tipo1">Rateio linear</label>
                                    </div>
                                    <div class="radio-custom radio-primary">
                                        <input type="radio" id="tarifa-tipo2" name="tarifa-tipo" value="2" <?php if ( $readonly == 'readonly' ) echo 'disabled=""'; ?> >
                                        <label for="tarifa-tipo2">Rateio pelo consumo</label>
                                    </div>
                                </div>
                            </div>

                            <?php if($readonly == '') : ?>
                                <div class="row">
                                    <div class="col-lg-9 text-right">
                                        <button class="btn btn-primary btn-salvar mr-3">Salvar</button>
                                        <button type="reset" class="btn btn-back">Voltar</button>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                        <!-- tab-form configurações -->

                        <!-- tab-form fechamentos -->
                        <div class="tab-form consumo d-none">

                            <ul class="nav nav-pills nav-pills-primary">
                                <?php if (isset($condo->m_agua) and $condo->m_agua) : ?>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#pill-agua" data-toggle="tab"><i class="fas fa-tint"></i> Água</a>
                                    </li>
                                <?php endif; ?>
                                <?php if (isset($condo->m_agua) and $condo->m_gas) : ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($condo->m_agua) ? "" : "active"; ?>" href="#pill-gas" data-toggle="tab"><i class="fas fa-fire"></i> Gás</a>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <div class="tab-content my-4" style="background-color: transparent;box-shadow: none;padding: 0; border: 0;">
                                <?php if (isset($condo->m_agua) and $condo->m_agua) : ?>
                                    <div id="pill-agua" class="tab-pane active">
                                        <div class="row mb-3">
                                            <div class="col text-right">
                                                <button class="btn btn-success btn-tarifar text-right">Tarifar</button>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-striped " id="dt-fechamentos" data-url="<?php echo site_url('ajax/get_fechamentos'); ?>">
                                            <thead>
                                            <tr role="row">
                                                <th class="filter" width="10%">Ramal</th>
                                                <th class="filter" width="10%">Competência</th>
                                                <th class="filter" width="10%">Data Inicial</th>
                                                <th class="filter" width="10%">Data Final</th>
                                                <th class="filter" width="10%">Consumo</th>
                                                <th class="filter" width="11%">Valor da Conta</th>
                                                <th class="filter" width="10%">Emissão</th>
                                                <th class="filter" width="10%">Relatórios</th>
                                                <th width="9%">Ações</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($condo->m_agua) and $condo->m_gas) : ?>
                                    <div id="pill-gas" class="tab-pane <?= ($condo->m_agua) ? "" : "active"; ?>">
                                        <table class="table table-bordered table-striped table-hover table-click" id="dt-leituras" data-url="<?php echo site_url('ajax/get_leituras'); ?>">
                                            <thead>
                                            <tr role="row">
                                                <th class="filter" width="17%">Competência</th>
                                                <th class="filter" width="17%">Data Inicial</th>
                                                <th class="filter" width="17%">Data Final</th>
                                                <th class="filter" width="17%">Consumo Total</th>
                                                <th class="filter" width="17%">Data Leitura</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                        <!-- tab-form fechamentos -->

                        <!-- tab-form geral -->
                        <?php if ($geral): ?>
                            <script>
                                var primeira_leitura = '<?= $primeira_leitura; ?>';
                            </script>

                            <div class="tab-form geral d-none" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="text-right">
                                    <div id="daterange" class="btn btn-primary">
                                        <i class="fa fa-calendar"></i>&nbsp;<span></span>
                                    </div>
                                </div>
                                <div class="chart-container" style="height:350px">
                                    <canvas id="chart"></canvas>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- tab-form geral -->

                    </div>
                </form>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>

<div id="modalExcluiFechamento" class="modal-block modal-header-color modal-block-danger mfp-hide">
    <input type="hidden" class="id" value="0">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Você tem certeza?</h2>
        </header>
        <div class="card-body">
            <div class="modal-wrapper">
                <div class="modal-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="modal-text">
                    <h4>Deseja realmente excluir este Faturamento?</h4>
                    <p>Ao excluir o faturamento, todas as informações de consumo das unidades também serão excluídos.</p>
                    <p><strong>Atenção:</strong> A exclusão é definitiva e não poderá ser desfeita.</p>
                </div>
            </div>
        </div>
        <footer class="card-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-danger modal-confirm overlay-small mr-3" style="min-width:69px;" data-timer="10" data-loading-overlay disabled>10</button>
                    <button class="btn btn-default modal-dismiss">Cancelar</button>
                </div>
            </div>
        </footer>
    </section>
</div>

<div id="modalNoFooter" class="modal-block mfp-hide">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Calculando...</h2>
        </header>
        <div class="card-body">
            <div class="modal-wrapper">
                <div class="modal-text">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated original" role="progressbar" aria-valuenow="0"
                             aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
echo view('Admin/modals/sindico');
echo view('Admin/modals/administradora');

$data['modal_id'] = 'modalBlocoRemove';
$data['modal_title'] = 'Você tem certeza?';
$data['modal_message'] = 'Deseja realmente excluir este bloco?';
$data['button'] = array('Excluir', 'Cancelar');
echo view('Admin/modals/confirm', $data);

$data['modal_id'] = 'modalUnidadeRemove';
$data['modal_title'] = 'Você tem certeza?';
$data['modal_message'] = 'Deseja realmente excluir esta unidade?';
$data['button'] = array('Excluir', 'Cancelar');
echo view('Admin/modals/confirm', $data);
