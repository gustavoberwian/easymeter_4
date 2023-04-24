<section role="main" class="content-body">
    <header class="page-header">
        <h2>Entidade <?= $entity->nome; ?></h2>
        <div class="right-wrapper text-end">
            <ol class="breadcrumbs pe-4">
                <li><a href="<?= site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><a href="<?= site_url('admin/entities'); ?>"><span>Entidades</span></a></li>
                <li><span><?= $entity->nome; ?></span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <div class="row">
        <div class="col">
            <section class="card">
                <form class="form-horizontal form-bordered form-entity">
                    <input id="id-entity" name="id-entity" type="hidden" value="<?php if (isset($entity->id)) echo $entity->id; ?>" readonly>
                    <header class="card-header">
                        <h2 class="card-title"><?= $readonly === '' ? 'Editar' : 'Visualizar' ?> </h2>
                        <div class="card-actions buttons">
                            <div class="btn-group">
                                <button class="btn btn-primary btn-cadastro active">Dados</button>
                                <button class="btn btn-primary btn-entradas">Entradas</button>
                                <button class="btn btn-primary btn-unidades">Unidades</button>
                                <button class="btn btn-primary btn-leituras">Leituras</button>
                                <button class="btn btn-primary btn-consumo">Fechamentos</button>
                                <button class="btn btn-primary btn-configuracoes">Configurações</button>
                                <?php if ($geral): ?>
                                    <button class="btn btn-primary btn-geral" data-central="<?php if (isset($central_geral)) echo $central_geral; ?>" data-unidade="<?php if (isset($unidade_geral)) echo $unidade_geral; ?>">Medidor Geral</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <div class="tab-form cadastro">

                            <div class="form-group row">
                                <label for="classificacao-entity" class="col-lg-3 control-label text-lg-right pt-2">Classificação da Entidade <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="classificacao-entity" name="classificacao-entity" class="form-control" <?=$readonly ?> required>
                                                <?php if (isset($entity->classificacao)) $entity_classificacao = $entity->classificacao;?>
                                                <option disabled value="">Classificação da Entidade</option>
                                                <option <?php if ($entity_classificacao == 'condominio') echo 'selected '; ?> value="condominio">Condomínio</option>
                                                <option <?php if ($entity_classificacao == 'shopping') echo 'selected '; ?> value="shopping">Shopping</option>
                                                <option <?php if ($entity_classificacao == 'industria') echo 'selected '; ?> value="industria">Indústria</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Nome <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="nome-condo" name="nome-condo" type="text" value="<?php if (isset($entity->nome)) echo $entity->nome; ?>" class="form-control" placeholder="Razão Social do Condomínio" <?= $readonly; ?> required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">CNPJ <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cnpj-condo" name="cnpj-condo" value="<?php if (isset($entity->cnpj)) echo $entity->cnpj; ?>" placeholder="___.___.___/____-__" class="form-control vcnpj" <?= $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="tipo-condo" name="tipo-condo" class="form-control" required <?php if ($readonly != '') echo 'disabled="true"';?>>
                                                    <?php if (isset($entity->tipo)) $entity_tipo = $entity->tipo;?>
                                                    <option disabled value="">Tipo do Condomínio</option>
                                                    <option <?php if ($entity_tipo == 'vertical') echo 'selected '; ?> value="vertical">Residencial Vertical</option>
                                                    <option <?php if ($entity_tipo == 'horizontal') echo 'selected '; ?> value="horizontal">Residencial Horizontal</option>
                                                    <option <?php if ($entity_tipo == 'comercial') echo 'selected '; ?> value="comercial">Comercial</option>
                                                    <option <?php if ($entity_tipo == 'misto') echo 'selected '; ?> value="misto">Misto</option>
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
                                        <input id="cep-condo" name="cep-condo" type="text" value="<?php if (isset($entity->cep)) echo $entity->cep; ?>" placeholder="_____-___" class="form-control vcep" <?= $readonly; ?> required>
                                        <button class="btn btn-success btn-busca overlay-small" type="button" data-loading-overlay disabled>Completar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Logradouro <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="logradouro-condo" name="logradouro-condo" type="text" value="<?php if (isset($entity->logradouro)) echo $entity->logradouro; ?>" class="form-control" placeholder="Nome da rua/avenida" <?= $readonly; ?> required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Número/Complemento <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="numero-condo" name="numero-condo" value="<?php if (isset($entity->numero)) echo $entity->numero; ?>" placeholder="Número" class="form-control" <?= $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="complemento-condo" name="complemento-condo" value="<?php if (isset($entity->complemento)) echo $entity->complemento; ?>" placeholder="Complemento" class="form-control" <?= $readonly; ?> >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Bairro <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="bairro-condo" name="bairro-condo" type="text" value="<?php if (isset($entity->bairro)) echo $entity->bairro; ?>" class="form-control" placeholder="Nome do bairro" <?= $readonly; ?> required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Cidade/Estado <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cidade-condo" name="cidade-condo" value="<?php if (isset($entity->cidade)) echo $entity->cidade; ?>" placeholder="Cidade" class="form-control" <?= $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="estado-condo" name="estado-condo" class="form-control" required <?php if ($readonly != '') echo 'disabled="true"';?>>
                                                    <?php $entity_uf = (!is_null($entity->uf)) ? $entity->uf : ''?>
                                                    <option <?php if (is_null($entity_uf)) echo 'selected '; ?> disabled value="">Estado</option>
                                                    <option <?php if ($entity_uf == 'AC') echo 'selected '; ?> value="AC">Acre</option>
                                                    <option <?php if ($entity_uf == 'AL') echo 'selected '; ?> value="AL">Alagoas</option>
                                                    <option <?php if ($entity_uf == 'AP') echo 'selected '; ?> value="AP">Amapá</option>
                                                    <option <?php if ($entity_uf == 'AM') echo 'selected '; ?> value="AM">Amazonas</option>
                                                    <option <?php if ($entity_uf == 'BA') echo 'selected '; ?> value="BA">Bahia</option>
                                                    <option <?php if ($entity_uf == 'CE') echo 'selected '; ?> value="CE">Ceará</option>
                                                    <option <?php if ($entity_uf == 'DF') echo 'selected '; ?> value="DF">Distrito Federal</option>
                                                    <option <?php if ($entity_uf == 'ES') echo 'selected '; ?> value="ES">Espírito Santo</option>
                                                    <option <?php if ($entity_uf == 'GO') echo 'selected '; ?> value="GO">Goiás</option>
                                                    <option <?php if ($entity_uf == 'MA') echo 'selected '; ?> value="MA">Maranhão</option>
                                                    <option <?php if ($entity_uf == 'MT') echo 'selected '; ?> value="MT">Mato Grosso</option>
                                                    <option <?php if ($entity_uf == 'MS') echo 'selected '; ?> value="MS">Mato Grosso do Sul</option>
                                                    <option <?php if ($entity_uf == 'MG') echo 'selected '; ?> value="MG">Minas Gerais</option>
                                                    <option <?php if ($entity_uf == 'PA') echo 'selected '; ?> value="PA">Pará</option>
                                                    <option <?php if ($entity_uf == 'PB') echo 'selected '; ?> value="PB">Paraíba</option>
                                                    <option <?php if ($entity_uf == 'PR') echo 'selected '; ?> value="PR">Paraná</option>
                                                    <option <?php if ($entity_uf == 'PE') echo 'selected '; ?> value="PE">Pernambuco</option>
                                                    <option <?php if ($entity_uf == 'PI') echo 'selected '; ?> value="PI">Piauí</option>
                                                    <option <?php if ($entity_uf == 'RJ') echo 'selected '; ?> value="RJ">Rio de Janeiro</option>
                                                    <option <?php if ($entity_uf == 'RN') echo 'selected '; ?> value="RN">Rio Grande do Norte</option>
                                                    <option <?php if ($entity_uf == 'RS') echo 'selected '; ?> value="RS">Rio Grande do Sul</option>
                                                    <option <?php if ($entity_uf == 'RO') echo 'selected '; ?> value="RO">Rondônia</option>
                                                    <option <?php if ($entity_uf == 'RR') echo 'selected '; ?> value="RR">Roraima</option>
                                                    <option <?php if ($entity_uf == 'SC') echo 'selected '; ?> value="SC">Santa Catarina</option>
                                                    <option <?php if ($entity_uf == 'SP') echo 'selected '; ?> value="SP">São Paulo</option>
                                                    <option <?php if ($entity_uf == 'SE') echo 'selected '; ?> value="SE">Sergipe</option>
                                                    <option <?php if ($entity_uf == 'TO') echo 'selected '; ?> value="TO">Tocantins</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Administradora</label>
                                <div class="col-lg-6">
                                    <select id="select-adm" name="select-adm" class="form-control populate" data-url="<?= site_url('admin/get_admnistadoras'); ?>" <?php if ($readonly != '') echo 'disabled="true"';?>>
                                        <option></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Gestor <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <select id="select-sindico" name="select-sindico" class="form-control populate" data-url="<?= site_url('admin/get_sindicos'); ?>" <?php if ($readonly != '') echo 'disabled="true"';?> required>
                                        <option><?php if (isset($entity->nome_sindico)) echo $entity->nome_sindico; ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Inicio e Fim do Mandato <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="inicio-condo" name="inicio-condo" value="<?php if (isset($entity->inicio)) echo $entity->inicio; ?>" placeholder="__/__/____" class="form-control vdate" <?= $readonly; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="fim-condo" name="fim-condo" value="<?php if (isset($entity->fim)) echo $entity->fim; ?>" placeholder="__/__/____" class="form-control vdate" <?= $readonly; ?> required>
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
                                                <input class="require-one" type="checkbox" id="agua-entity" name="agua-entity" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($entity->m_agua) and $entity->m_agua == 1) echo 'checked'; ?>>
                                                <label for="agua-entity">Água</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-success">
                                                <input class="require-one" type="checkbox" id="gas-entity" name="gas-entity" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($entity->m_gas) and $entity->m_gas > 0) echo 'checked'; ?>>
                                                <label for="gas-entity">Gás</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-warning">
                                                <input class="require-one" type="checkbox" id="energia-entity" name="energia-entity" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($entity->m_energia) and $entity->m_energia == 1) echo 'checked'; ?>>
                                                <label for="energia-entity">Energia</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-info">
                                                <input class="require-one" type="checkbox" id="nivel-entity" name="nivel-entity" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($entity->m_nivel) and $entity->m_nivel == 1) echo 'checked'; ?>>
                                                <label for="nivel-entity">Nível</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row ramais" <?php if (isset($entity->m_agua) and $entity->m_agua == 0) echo 'style="display:none;"'; ?>>
                                <label class="col-lg-3 control-label text-lg-right pt-2">Ramais <span class="required">*</span></label>
                                <div class="col-lg-6 <?= $readonly; ?>">
                                    <input id="ramais-condo" name="ramais-condo" data-role="tagsinput" data-tag-class="badge badge-info" class="form-control ramais-input <?php if (isset($entity->m_gas) and $entity->m_agua == 0) echo 'no-validate'; ?>" value="<?php if (isset($ramais)) echo $ramais; ?>" <?php if ($readonly != '') echo 'disabled="true"';?> required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Dados Proprietários</label>
                                <div class="col-lg-6 align-self-center <?= $readonly; ?>">
                                    <div class="checkbox-custom checkbox-default">
                                        <input type="checkbox" id="proprietarios-condo" name="proprietarios-condo" <?php if($readonly != '') echo ' disabled'; ?> <?php if (isset($entity->d_proprietarios) and $entity->d_proprietarios == 1) echo 'checked'; ?>>
                                        <label for="fracao-condo">Cadastrar nome e email dos proprietários?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Frações Ideais</label>
                                <div class="col-lg-6 align-self-center <?= $readonly; ?>">
                                    <div class="checkbox-custom checkbox-default">
                                        <input type="checkbox" id="fracao-condo" name="fracao-condo" <?php if(count($groups) == 0 or $readonly != '') echo ' disabled'; ?> <?php if (isset($entity->fracao_ideal) and $entity->fracao_ideal == 1) echo 'checked'; ?>>
                                        <label for="fracao-condo">Cadastar as Frações Ideais das unidades para permitir rateio de valores da inadimplência?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Centrais Easymeter</label>
                                <div class="col-lg-6 <?= $readonly; ?>">
                                    <input id="centrais-condo" name="centrais-condo" class="form-control" value="<?php if (isset($centrais)) echo $centrais; ?>" <?php if ($readonly != '') echo 'disabled="true"';?>/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Observações</label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" rows="3" id="textareaAutosize" data-plugin-textarea-autosize <?= $readonly; ?>><?= service('uri')->getSegment(4); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Ativo</label>
                                <div class="col-lg-6">
                                    <div class="switch switch-sm switch-primary">
                                        <input type="checkbox" name="switch" data-plugin-ios-switch checked="checked" <?= $readonly; ?>/>
                                    </div>
                                </div>
                            </div>

                            <?php if($readonly == '') : ?>
                                <div class="row">
                                    <div class="col-lg-9 text-end">
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
                        <div class="tab-form unidades d-none<?php if(count($groups) == 0) echo ' inactive'; ?>">
                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Bloco</label>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <select id="sel-bloco" name="sel-bloco" class="form-control">
                                            <?php foreach ($groups as $bl) { ?>
                                                <option value="<?= $bl->id; ?>"><?= ($bl->nome == '') ? 'Não possui' : $bl->nome; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if ($readonly == '') { ?>
                                            <button class="btn btn-warning btn-bloco-edit" type="button" title="Editar" <?php if(count($groups) == 0 or $readonly != '') echo 'disabled'; ?>><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-success btn-bloco-add" type="button"  title="Adicionar"><i class="fas fa-plus"></i></button>
                                            <button class="btn btn-danger btn-bloco-delete" type="button"  title="Excluir" <?php if(count($groups) == 0 or $readonly != '') echo 'disabled'; ?>><i class="fas fa-times"></i></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php if (count($groups) == 0 and is_null($readonly)) : ?>
                                        <div class="alert alert-warning unidades">
                                            <strong>Atenção</strong> Antes de incluir unidades você deve configurar os blocos do condomínio.
                                        </div>
                                    <?php endif; ?>
                                    <div class="unidades-container">
                                        <?php if (count($groups) != 0 or is_null($readonly)) : ?>
                                            <div class="mb-2 text-end">
                                                <button class="btn btn-success btn-inclui-unidade mr-2" type="button" <?= (count($groups) == 0 or is_null($readonly)) ? 'disabled' : ''; ?>><i class="fa fa-plus"></i> Incluir Unidade</button>
                                                <button class="btn btn-primary btn-filter" type="button" <?= (count($groups) == 0 or is_null($readonly)) ? 'disabled' : ''; ?>><i class="fa fa-filter"></i></button>
                                            </div>
                                        <?php endif; ?>
                                        <table class="table table-bordered table-striped" id="dt-unidades">
                                            <thead>
                                            <tr role="row">
                                                <th width="4%">Apto</th>
                                                <th width="4%">Andar</th>
                                                <th width="4%">PIN</th>
                                                <th width="4%">Tipo</th>
                                                <th width="18%">Proprietário</th>
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
                                        $centrais = model('admin_model')->get_centrais($entity->id);
                                        foreach($centrais as $c) {
                                            echo "<tr>";
                                            echo "<td width='20'><a href='".site_url('admin/centrais/'.$c->nome)."' target='_blank'>{$c->nome}</a></td>";
                                            $x = model('admin_model')->get_central_leituras($c->nome, $entity->tabela);
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
                            <form>
                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Config 1 <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="conf1-condo" name="conf1-condo" type="text" value="" class="form-control" placeholder="Explicação" <?= $readonly; ?> required>
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
                                    <div class="col-lg-9 text-end">
                                        <button class="btn btn-primary btn-salvar mr-3">Salvar</button>
                                        <button type="reset" class="btn btn-back">Voltar</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            </form>

                        </div>
                        <!-- tab-form configurações -->

                        <!-- tab-form fechamentos -->
                        <div class="tab-form consumo d-none">

                            <ul class="nav nav-pills nav-pills-primary">
                                <?php if (isset($entity->m_agua) and $entity->m_agua) : ?>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#pill-agua" data-toggle="tab"><i class="fas fa-tint"></i> Água</a>
                                    </li>
                                <?php endif; ?>
                                <?php if (isset($entity->m_agua) and $entity->m_gas) : ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($entity->m_agua) ? "" : "active"; ?>" href="#pill-gas" data-toggle="tab"><i class="fas fa-fire"></i> Gás</a>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <div class="tab-content my-4" style="background-color: transparent;box-shadow: none;padding: 0; border: 0;">
                                <?php if (isset($entity->m_agua) and $entity->m_agua) : ?>
                                    <div id="pill-agua" class="tab-pane active">
                                        <div class="row mb-3">
                                            <div class="col text-end">
                                                <button class="btn btn-success btn-tarifar text-end">Tarifar</button>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-striped " id="dt-fechamentos" data-url="<?= site_url('admin/get_fechamentos'); ?>">
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

                                <?php if (isset($entity->m_agua) and $entity->m_gas) : ?>
                                    <div id="pill-gas" class="tab-pane <?= ($entity->m_agua) ? "" : "active"; ?>">
                                        <table class="table table-bordered table-striped table-hover table-click" id="dt-leituras" data-url="<?= site_url('admin/get_leituras'); ?>">
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
                                <div class="text-end">
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
                <div class="col-md-12 text-end">
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
echo view('Admin/modals/gestor');
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