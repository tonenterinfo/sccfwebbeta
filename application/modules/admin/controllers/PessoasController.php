<?php

class Admin_PessoasController extends Zend_Controller_Action {

    protected $_dbTable;
    protected $_model;
    protected $_cliente;
    protected $_fornecedor;
    protected $_pessoafisica;
    protected $_pessoajuridica;
    protected $_enderecos;
    protected $_idpessoa;

    public function init() {
        $this->_dbTable = new Application_Model_DbTable_Pessoa();
        $this->_model = new Application_Model_Pessoa();
        $this->_cliente = new Application_Model_DbTable_Cliente();
        $this->_fornecedor = new Application_Model_DbTable_Fornecedor();
        $this->_pessoafisica = new Application_Model_DbTable_Pessoafisica();
        $this->_pessoajuridica = new Application_Model_DbTable_Pessoajuridica();
        $this->_enderecos = new Application_Model_DbTable_Endereco();
        $situacaoForCli = new Application_Model_DbTable_Situacaoforcli();
        $this->view->classe = $this->_getParam('classe');
        $this->_idpessoa = $this->_getParam('id');
        $this->view->nextId = $this->_model->status()->Auto_increment;
        $this->view->id_pessoa = $this->_getParam('id');
    }

    public function indexAction() {
        $this->view->pessoas = $this->_model->find($this->_getParam('classe'));
    }

    public function newAction() {
        $situacaoForCli = new Application_Model_DbTable_Situacaoforcli();
        $this->view->situacaoForCli = $situacaoForCli->fetchAll();

        $estado = new Application_Model_DbTable_Estado();
        $this->view->estado = $estado->fetchAll();



        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if ($data['NOME'] != "") {
                unset($data['submit']);

                $dataPessoa = array(
                    'ID' => null,
                    'ID_EMPRESA' => 1,
                    'NOME' => $data['NOME'],
                    'TIPO' => $data['TIPO'],
                    'EMAIL' => $data['EMAIL'],
                    'SITE' => $data['SITE'],
                    'CLASSE' => $data['CLASSE']
                );

                $idPessoa = $this->_dbTable->insert($dataPessoa);

                //PESSOA FÍSICA
                if ($data['CPF'] !== "") {
                    $dataFisica = array(
                        'ID_PESSOA' => $idPessoa,
                        'CPF' => $data['CPF'],
                        'RG' => $data['RG'],
                        'DATA_NASCIMENTO' => date('Y-m-d', strtotime($data['DATA_NASCIMENTO']))
                    );
                    $this->_pessoafisica->insert($dataFisica);
                }

                //PESSOA JURÍDICA
                if ($data['CNPJ'] !== "") {
                    $data['CNPJ'] = str_replace('.', '', $data['CNPJ']);
                    $data['CNPJ'] = str_replace('/', '', $data['CNPJ']);
                    $data['CNPJ'] = str_replace('-', '', $data['CNPJ']);
                    $data['CNPJ'] = trim($data['CNPJ']);
                    $dataJuridica = array(
                        'ID_PESSOA' => $idPessoa,
                        'CNPJ' => $data['CNPJ'],
                        'RAZAO_SOCIAL' => $data['RAZAO_SOCIAL'],
                        'INSCRICAO_MUNICIPAL' => $data['INSCRICAO_MUNICIPAL']
                    );
                    $this->_pessoajuridica->insert($dataJuridica);
                }

                //CLIENTE
                if (isset($data['DESDE']) and $data['DESDE'] !== "") {
                    $partesData = explode("/", $data['DESDE']); 
                    $newDate = $partesData[2].'-'.$partesData[1].'-'.$partesData[0];
                    $dataCliente = array(
                        'ID_PESSOA' => $idPessoa,
                        'ID_ATIVIDADE_FOR_CLI' => 1,
                        'ID_SITUACAO_FOR_CLI' => $data['ID_SITUACAO_FOR_CLI'],
                        'DESDE' => $newDate,
                        'DATA_CADASTRO' => date('Y-m-d'),
                        'OBSERVACOES' => $data['OBSERVACOES']
                    );
                    $this->_cliente->insert($dataCliente);
                }

                //FORNECEDOR
                if (isset($data['DESDE_FORNECEDOR']) and $data['DESDE_FORNECEDOR'] !== "") {
                    
                    $dataFonecedor = array(
                        'ID_PESSOA' => $idPessoa,
                        'ID_ATIVIDADE_FOR_CLI' => 1,
                        'ID_SITUACAO_FOR_CLI' => $data['ID_SITUACAO_FOR_CLI'],
                        'DESDE' => date('Y-m-d', strtotime($data['DESDE_FORNECEDOR'])),
                        'OPTANTE_SIMPLES_NACIONAL' => $data['OPTANTE_SIMPLES'],
                        'DATA_CADASTRO' => date('Y-m-d'),
                        'SOFRE_RETENCAO' => $data['SOFRE_RETENCAO'],
                        'OBSERVACOES' => $data['OBSERVACOES']
                    );
                    $this->_fornecedor->insert($dataCliente);
                }

                $this->_redirect('/admin/pessoas/index/classe/' . $data['CLASSE']);
            }
        }
    }

    public function editAction() {
        $situacaoForCli = new Application_Model_DbTable_Situacaoforcli();
        $this->view->situacaoForCli = $situacaoForCli->fetchAll();

        $estado = new Application_Model_DbTable_Estado();
        $this->view->estado = $estado->fetchAll();

        $enderecos = new Application_Model_DbTable_Endereco();
        $this->view->enderecos = $enderecos->findByIdPessoa($this->_getParam('id'));

        $rpessoa = $this->_dbTable->find((int) $this->_getParam('id'))->current();
        $this->view->tipo = $rpessoa->TIPO;
        $this->view->nome = $rpessoa->NOME;
        $this->view->email = $rpessoa->EMAIL;
        $this->view->site = $rpessoa->SITE;

        if($this->_getParam('classe') == 1) {
            $rcliente = $this->_cliente->findByIdPessoa((int) $this->_getParam('id'))->current();
            $this->view->id_situacao_for_cli = $rcliente->ID_SITUACAO_FOR_CLI;
            $this->view->desde = $rcliente->DESDE;
            $this->view->data_cadastro = $rcliente->DATA_CADASTRO;
            $this->view->observacoes = $rcliente->OBSERVACOES;
        }
        
        if($this->_getParam('classe') == 2) {
            $rfornecedor = $this->_fornecedor->findByIdPessoa((int) $this->_getParam('id'))->current();
            if($rfornecedor != null) {
                $this->view->id_situacao_for_cli = $rfornecedor->ID_SITUACAO_FOR_CLI;
                $this->view->desde = $rfornecedor->DESDE;
                $this->view->optante_simples = $rfornecedor->OPTANTE_SIMPLES_NACIONAL;
                $this->view->data_cadastro = $rfornecedor->DATA_CADASTRO;
                $this->view->sofre_retencao = $rfornecedor->SOFRE_RETENCAO;
                $this->view->observacoes = $rfornecedor->OBSERVACOES;
            }
        }

        $findFisica2 = $this->_pessoafisica->findByIdPessoa((int) $this->_getParam('id'));
        if (isset($findFisica2['0'])) {
            $this->view->cpf = $findFisica2['0']->CPF;
            $this->view->rg = $findFisica2['0']->RG;
            $this->view->data_nascimento = $findFisica2['0']->DATA_NASCIMENTO;
        }
        $findJuridica2 = $this->_pessoajuridica->findByIdPessoa((int) $this->_getParam('id'));
        if (isset($findJuridica2['0'])) {
            $this->view->razao_social = $findJuridica2['0']->RAZAO_SOCIAL;
            $this->view->cnpj = $findJuridica2['0']->CNPJ;
            $this->view->inscricao_municipal = $findJuridica2['0']->INSCRICAO_MUNICIPAL;
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if ($data['NOME'] != "") {
                unset($data['submit']);

                $dataPessoa = array(
                    'ID_EMPRESA' => 1,
                    'NOME' => $data['NOME'],
                    'TIPO' => $data['TIPO'],
                    'EMAIL' => $data['EMAIL'],
                    'SITE' => $data['SITE'],
                    'CLASSE' => $data['CLASSE']
                );

                $this->_dbTable->update($dataPessoa, 'ID = ' . (int) $this->_getParam('id'));
            }
            //PESSOA FÍSICA
            if ($data['CPF'] !== "") {
                $dataFisica = array(
                    'ID_PESSOA' => (int) $this->_getParam('id'),
                    'CPF' => $data['CPF'],
                    'RG' => $data['RG'],
                    'DATA_NASCIMENTO' => date('Y-m-d', strtotime($data['DATA_NASCIMENTO']))
                );
                $findFisica = $this->_pessoafisica->findByIdPessoa((int) $this->_getParam('id'));

                if (isset($findFisica['0'])) {
                    $this->_pessoafisica->update($dataFisica, 'ID = ' . $findFisica['0']->ID);
                } else {
                    $this->_pessoafisica->insert($dataFisica);
                }
            }

            //PESSOA JURIDICA
            if ($data['CNPJ'] !== "") {
                $data['CNPJ'] = str_replace('.', '', $data['CNPJ']);
                $data['CNPJ'] = str_replace('/', '', $data['CNPJ']);
                $data['CNPJ'] = str_replace('-', '', $data['CNPJ']);
                $data['CNPJ'] = trim($data['CNPJ']);
                $dataJuridica = array(
                    'ID_PESSOA' => (int) $this->_getParam('id'),
                    'CNPJ' => $data['CNPJ'],
                    'RAZAO_SOCIAL' => $data['RAZAO_SOCIAL'],
                    'INSCRICAO_MUNICIPAL' => $data['INSCRICAO_MUNICIPAL']
                );
                $findJuridica = $this->_pessoajuridica->findByIdPessoa((int) $this->_getParam('id'));

                if (isset($findJuridica['0'])) {
                    $this->_pessoajuridica->update($dataJuridica, 'ID = ' . $findJuridica['0']->ID);
                } else {
                    $this->_pessoajuridica->insert($dataJuridica);
                }
            }

            //CLIENTE
            if ($data['DESDE'] !== "") {

                $dataCliente = array(
                    'ID_PESSOA' => (int) $this->_getParam('id'),
                    'ID_ATIVIDADE_FOR_CLI' => 1,
                    'ID_SITUACAO_FOR_CLI' => $data['ID_SITUACAO_FOR_CLI'],
                    'DESDE' => date('Y-m-d', strtotime($data['DESDE'])),
                    'OBSERVACOES' => $data['OBSERVACOES']
                );
                $findCliente = $this->_cliente->findByIdPessoa((int) $this->_getParam('id'));

                if (isset($findCliente['0'])) {
                    $this->_cliente->update($dataCliente, 'ID = ' . $findCliente['0']->ID);
                } else {
                    $this->_cliente->insert($dataCliente);
                }
            }
            
             //FORNECEDOR
                if ($data['DESDE_FORNECEDOR'] !== "") {
                    $data['DESDE_FORNECEDOR'] = str_replace("/", "-", $data['DESDE_FORNECEDOR']);
                    $dataFornecedor = array(
                        'ID_PESSOA' => (int) $this->_getParam('id'),
                        'ID_ATIVIDADE_FOR_CLI' => 1,
                        'ID_SITUACAO_FOR_CLI' => $data['ID_SITUACAO_FOR_CLI'],
                        'DESDE' => date('Y-m-d', strtotime($data['DESDE_FORNECEDOR'])),
                        'OPTANTE_SIMPLES_NACIONAL' => $data['OPTANTE_SIMPLES'],
                        'DATA_CADASTRO' => date('Y-m-d'),
                        'SOFRE_RETENCAO' => $data['SOFRE_RETENCAO'],
                        'OBSERVACOES' => $data['OBSERVACOES']
                    );
                   $findFornecedor = $this->_fornecedor->findByIdPessoa((int) $this->_getParam('id'));

                    if (isset($findFornecedor['0'])) {
                        $this->_fornecedor->update($dataFornecedor, 'ID = ' . $findFornecedor['0']->ID);
                    } else {
                        $this->_fornecedor->insert($dataFornecedor);
                    }
                }

                

            $this->_redirect('/admin/pessoas/index/classe/' . $data['CLASSE']);
        }
    }

    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $rpessoa = $this->_dbTable->find($this->_request->getParam('id'))->current();
        $rpessoa->delete();
        $this->_redirect('/admin/pessoas/index/classe/' . $this->_request->getParam('classe'));
    }

    public function enderecoAction() {
        $this->_helper->layout->disableLayout();
        if ($this->getRequest()->isPost()) {

            if ($_POST['logradouro'] != "") {
                if ($_POST['idpessoa'] > 0) {
                    $idPessoa = $_POST['idpessoa'];
                } elseif ($_POST['idpessoa'] == 0) {
                    $idPessoa = $this->_model->status()->Auto_increment;
                }

                $dataEntereco = array(
                    'ID_EMPRESA' => 1,
                    'ID_PESSOA' => $idPessoa,
                    'LOGRADOURO' => $_POST['logradouro'],
                    'NUMERO' => $_POST['numero'],
                    'COMPLEMENTO' => $_POST['complemento'],
                    'BAIRRO' => $_POST['bairro'],
                    'CEP' => $_POST['cep'],
                    'ID_MUNICIPIO' => $_POST['idmunicipio'],
                    'UF' => $_POST['uf'],
                    'FONE' => $_POST['fone'],
                    'FAX' => $_POST['fax'],
                    'PRINCIPAL' => $_POST['principal'],
                    'ENTREGA' => $_POST['entrega'],
                    'COBRANCA' => $_POST['cobranca'],
                    'CORRESPONDENCIA' => $_POST['correspondencia']
                );

                $enderecos = new Application_Model_DbTable_Endereco();
                if ($_POST['idendereco'] > 0) {
                    $enderecos->update($dataEntereco, 'ID = ' . $_POST['idendereco']);
                } elseif ($_POST['idendereco'] == 0) {
                    $enderecos->insert($dataEntereco);
                }

                if ($_POST['idpessoa'] > 0) {
                    $this->view->end = $this->_enderecos->findByIdPessoa($_POST['idpessoa']);
                } elseif ($_POST['idpessoa'] == 0) {
                    $this->view->end = $this->_enderecos->findByIdPessoa($this->_model->status()->Auto_increment);
                }
            }
        }
    }

    public function deleteenderecoAction() {
        $rend = $this->_enderecos->find($this->_getParam('id'))->current();
        $rend->delete();
        $this->_redirect('/admin/pessoas/edit/id/' . $this->_request->getParam('pessoa') . '/classe/' . $this->_request->getParam('classe'));
    }

    public function combocidadeAction() {
        $this->_helper->layout->disableLayout();
        $cidades = new Application_Model_DbTable_Municipio();
        $cidade = $cidades->findCidade(trim($_POST['uf']));
        $this->view->cidadesCombo = $cidade;
    }

    public function editaenderecoAction() {
        $this->_helper->layout->disableLayout();
        $estado = new Application_Model_DbTable_Estado();
        $this->view->estado = $estado->fetchAll();

        $rendereco = $this->_enderecos->find($_POST['id'])->current();
        $this->view->id = $rendereco->ID;
        $this->view->id_pessoa = $rendereco->ID_PESSOA;
        $this->view->logradouro = $rendereco->LOGRADOURO;
        $this->view->numero = $rendereco->NUMERO;
        $this->view->complemento = $rendereco->COMPLEMENTO;
        $this->view->bairro = $rendereco->BAIRRO;
        $this->view->cep = $rendereco->CEP;
        $this->view->id_municipio = $rendereco->ID_MUNICIPIO;
        $cidades = new Application_Model_DbTable_Municipio();
        if ($rendereco->ID_MUNICIPIO > 0) {
            $rcidade = $cidades->find($rendereco->ID_MUNICIPIO)->current();
            $this->view->cidade = $rcidade->NOME;
        } else {
            $this->view->cidade = "";
        }
        $this->view->uf = $rendereco->UF;
        $this->view->fone = $rendereco->FONE;
        $this->view->fax = $rendereco->FAX;
        $this->view->principal = $rendereco->PRINCIPAL;
        $this->view->entrega = $rendereco->ENTREGA;
        $this->view->cobranca = $rendereco->COBRANCA;
        $this->view->correspondencia = $rendereco->CORRESPONDENCIA;
    }

    public function historicoAction() {
        $cliente = $this->_dbTable->find($this->_getParam('id'))->current();
        $this->view->cliente = $cliente->NOME;

        $lancamentos = new Application_Model_DbTable_Lancamentoreceber();
        $select = $lancamentos->select();
        $select->from('lancamento_receber')->where('ID_CLIENTE = '.$this->_getParam('id'))->order('DATA_LANCAMENTO DESC');
        $this->view->lancamentos = $lancamentos->fetchAll($select);
    }

    public function parcelasAction() {
        $this->_helper->layout->disableLayout();

        $parcelas = new Application_Model_DbTable_Parcelareceber();
        $select = $parcelas->select();
        $select->from('parcela_receber')->where('ID_LANCAMENTO_RECEBER = '.$_POST['id']);
        $this->view->parcelas = $parcelas->fetchAll($select);
        
    }

}

