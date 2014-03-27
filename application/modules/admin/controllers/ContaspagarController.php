<?php

class Admin_ContaspagarController extends Zend_Controller_Action
{

    protected $fornecedor;
    protected $_naturezaFinanceira;
    protected $_documentoOrigem;
    protected $_statusParcela;
    protected $_fornecedor;
    protected $_lancamentoPagar;
    protected $_parcelaPagar;
    protected $_tipoPagamento;
    protected $_parcelaPagamento;

    public function init()
    {
        $this->fornecedor = new Application_Model_Pessoa();
        $this->_fornecedor = new Application_Model_DbTable_Pessoa();
        $this->_naturezaFinanceira = new Application_Model_DbTable_Naturezafinanceirapagar();
        $this->_documentoOrigem = new Application_Model_DbTable_Documentoorigem();
        $this->_statusParcela = new Application_Model_DbTable_Statusparcelapagar();
        $this->_lancamentoPagar = new Application_Model_DbTable_Lancamentopagar();
        $this->_parcelaPagar = new Application_Model_DbTable_Parcelapagar();
        $this->_tipoPagamento = new Application_Model_DbTable_Tipopagamento();
        $this->_parcelaPagamento = new Application_Model_DbTable_Parcelapagamento();
    }

    public function indexAction()
    {
        $parcelasPagar = new Application_Model_DbTable_Parcelapagar();
        $this->view->parcelas = $parcelasPagar->fetch();
       
    }
    
    public function newAction() {
        $this->view->fornecedores = $this->fornecedor->find(2);
        $this->view->naturezafinanceira = $this->_naturezaFinanceira->fetchAll();
        $this->view->documentoOrigem = $this->_documentoOrigem->fetchAll();
        $this->view->status = $this->_statusParcela->fetchAll();
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            //CONTAS A PAGAR
            $data['VALOR_TOTAL'] = str_replace("R$", "", $data['VALOR_TOTAL']);
            $data['VALOR_TOTAL'] = str_replace(".", "", $data['VALOR_TOTAL']);
            $data['VALOR_TOTAL'] = str_replace(",", ".", $data['VALOR_TOTAL']);
            $data['VALOR_TOTAL'] = trim($data['VALOR_TOTAL']);
            
            $data['VALOR'] = str_replace("R$", "", $data['VALOR']);
            $data['VALOR'] = str_replace(".", "", $data['VALOR']);
            $data['VALOR'] = str_replace(",", ".", $data['VALOR']);
            $data['VALOR'] = trim($data['VALOR']);
            
            $partesData = explode("/", $data['VENCIMENTO']); 
            $newDate = $partesData[2].'-'.$partesData[1].'-'.$partesData[0];
            
            $dataLancamentoPagar = array(
                'ID_FORNECEDOR' => $data['ID_FORNECEDOR'],
                'ID_NATUREZA_FINANCEIRA' => $data['ID_PLANO_NATUREZA_FINANCEIRA_PAGAR'],
                'ID_DOCUMENTO_ORIGEM' => $data['ID_DOCUMENTO_ORIGEM'],
                'VALOR_TOTAL' => $data['VALOR_TOTAL'],
                'VALOR_A_PAGAR' => $data['VALOR_TOTAL'],
                'DATA_LANCAMENTO' => date('Y-m-d')
            );
            $lancamentoPagar = new Application_Model_DbTable_Lancamentopagar();
            $idLancamentoPagar = $lancamentoPagar->insert($dataLancamentoPagar);

            $parcela_pagar = array(
                'ID_STATUS_PARCELA_PAGAR' => $data['ID_STATUS_PARCELA'],
                'ID_LANCAMENTO_PAGAR' => $idLancamentoPagar,
                'DATA_EMISSAO' => date('Y-m-d'),
                'DATA_VENCIMENTO' => $newDate,
                'VALOR' => $data['VALOR']
            );
            $parcelaPagar = new Application_Model_DbTable_Parcelapagar();
            $parcelaPagar->insert($parcela_pagar);
            
            $this->_redirect('/admin/contaspagar');
        }
    }

    public function editAction() {
        $this->view->fornecedores = $this->fornecedor->find(2);
        $this->view->naturezafinanceira = $this->_naturezaFinanceira->fetchAll();
        $this->view->documentoOrigem = $this->_documentoOrigem->fetchAll();
        $this->view->status = $this->_statusParcela->fetchAll();
        $this->view->tipoPagamento = $this->_tipoPagamento->fetchAll();
        
        $rParcela = $this->_parcelaPagar->find($this->_getParam('id'))->current();
        
        $rlancamento = $this->_lancamentoPagar->find($rParcela->ID_LANCAMENTO_PAGAR)->current();
        $rfornecedor = $this->_fornecedor->find($rlancamento->ID_FORNECEDOR)->current();
        $this->view->idFornecedor = $rlancamento->ID_FORNECEDOR;
        $this->view->fornecedor = $rfornecedor->NOME;
        
        $rNatureza = $this->_naturezaFinanceira->find($rlancamento->ID_NATUREZA_FINANCEIRA)->current();
        $this->view->idNatureza = $rlancamento->ID_NATUREZA_FINANCEIRA;
        $this->view->natureza = $rNatureza->DESCRICAO;
        
        $rDocumento = $this->_documentoOrigem->find($rlancamento->ID_DOCUMENTO_ORIGEM)->current();
        $this->view->idDocumento = $rlancamento->ID_DOCUMENTO_ORIGEM;
        $this->view->documento = $rDocumento->DESCRICAO;

        $this->view->total = $rlancamento->VALOR_TOTAL;
        $this->view->parcela = $rParcela->VALOR;

        $rstatus = $this->_statusParcela->find($rParcela->ID_STATUS_PARCELA_PAGAR)->current();
        $this->view->idStatus = $rParcela->ID_STATUS_PARCELA_PAGAR;
        $this->view->stat = $rstatus->DESCRICAO;

        if($rParcela->ID_STATUS_PARCELA_PAGAR == 2) {
            $select = $this->_parcelaPagamento->select();
            $select->from('parcela_pagamento')->where('ID_PARCELA_PAGAR = ' . (int)$this->_getParam('id'));
            $rdata = $this->_parcelaPagamento->fetchAll($select);
            $this->view->dataPagamento = $rdata[0]->DATA_PAGAMENTO;
            $this->view->idTipoPagamento = $rdata[0]->ID_TIPO_PAGAMENTO;
            $rtipoPagamento = $this->_tipoPagamento->find($rdata[0]->ID_TIPO_PAGAMENTO)->current();
            $this->view->tpPagamento = $rtipoPagamento->DESCRICAO;
            $this->view->valPg = $rdata[0]->VALOR_PAGO;
            $this->view->idPgmto =$rdata[0]->ID;
        } else {
            $this->view->dataPagamento;
            $this->view->idTipoPagamento;
            $this->view->tpPagamento;
            $this->view->valPg;
            $this->view->idPgmto;
        }
        
        $this->view->vencimento = $rParcela->DATA_VENCIMENTO;

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            //CONTAS A PAGAR
            $data['VALOR_TOTAL'] = str_replace("R$", "", $data['VALOR_TOTAL']);
            $data['VALOR_TOTAL'] = str_replace(".", "", $data['VALOR_TOTAL']);
            $data['VALOR_TOTAL'] = str_replace(",", ".", $data['VALOR_TOTAL']);
            $data['VALOR_TOTAL'] = trim($data['VALOR_TOTAL']);
            
            $data['VALOR'] = str_replace("R$", "", $data['VALOR']);
            $data['VALOR'] = str_replace(".", "", $data['VALOR']);
            $data['VALOR'] = str_replace(",", ".", $data['VALOR']);
            $data['VALOR'] = trim($data['VALOR']);
            
            $partesData = explode("/", $data['VENCIMENTO']); 
            $newDate = $partesData[2].'-'.$partesData[1].'-'.$partesData[0];
            
            $dataLancamentoPagar = array(
                'ID_FORNECEDOR' => $data['ID_FORNECEDOR'],
                'ID_NATUREZA_FINANCEIRA' => $data['ID_PLANO_NATUREZA_FINANCEIRA_PAGAR'],
                'ID_DOCUMENTO_ORIGEM' => $data['ID_DOCUMENTO_ORIGEM'],
                'VALOR_TOTAL' => $data['VALOR_TOTAL'],
                'VALOR_A_PAGAR' => $data['VALOR_TOTAL'],
                'DATA_LANCAMENTO' => date('Y-m-d')
            );
            $lancamentoPagar = new Application_Model_DbTable_Lancamentopagar();
            $lancamentoPagar->update($dataLancamentoPagar, 'ID = '.$rParcela->ID_LANCAMENTO_PAGAR);
            
            if($data['ID_STATUS_PARCELA'] != 2) {
                $parcela_pagar = array(
                    'ID_STATUS_PARCELA_PAGAR' => $data['ID_STATUS_PARCELA'],
                    'DATA_EMISSAO' => date('Y-m-d'),
                    'DATA_VENCIMENTO' => $newDate,
                    'VALOR' => $data['VALOR']
                );
            }
            if($data['ID_STATUS_PARCELA'] == 2) {
                $parcela_pagar = array(
                    'ID_STATUS_PARCELA_PAGAR' => 1,
                    'DATA_EMISSAO' => date('Y-m-d'),
                    'DATA_VENCIMENTO' => $newDate,
                    'VALOR' => $data['VALOR']
                );
            }
            $parcelaPagar = new Application_Model_DbTable_Parcelapagar();
            $parcelaPagar->update($parcela_pagar, 'ID = '.(int)$this->_getParam('id'));
            
            if($data['ID_STATUS_PARCELA'] == 2 and $data['ID_STATUS_PARCELA'] != '' and $data['ID_TIPO_PAGAMENTO'] > 0 and $data['ID_TIPO_PAGAMENTO'] != '' and $data['IDPG'] == 0) {
                $partesData = explode("/", $data['DATA_PAGAMENTO']); 
                $new = $partesData[2].'-'.$partesData[1].'-'.$partesData[0];
                $data['VALOR_PAGO'] = str_replace("R$", "", $data['VALOR_PAGO']);
                $data['VALOR_PAGO'] = str_replace(".", "", $data['VALOR_PAGO']);
                $data['VALOR_PAGO'] = str_replace(",", ".", $data['VALOR_PAGO']);
                $data['VALOR_PAGO'] = trim($data['VALOR_PAGO']);
                $dataParcelaPagamento = array (
                    'ID_PARCELA_PAGAR' => $this->_getParam('id'),
                    'ID_TIPO_PAGAMENTO' => $data['ID_TIPO_PAGAMENTO'],
                    'DATA_PAGAMENTO' => $new,
                    'VALOR_PAGO' => $data['VALOR_PAGO']
                );
                $this->_parcelaPagamento->insert($dataParcelaPagamento);
                
                $status = array (
                    'ID_STATUS_PARCELA_PAGAR' => 2
                );
                $this->_parcelaPagar->update($status, 'ID = '. (int)$this->_getParam('id'));
                $this->_redirect('/admin/contaspagar');
            }
            
            if($data['ID_STATUS_PARCELA'] == 2 and $data['ID_STATUS_PARCELA'] != '' and $data['ID_TIPO_PAGAMENTO'] > 0 and $data['ID_TIPO_PAGAMENTO'] != '' and $data['IDPG'] > 0) {
                $partesData = explode("/", $data['DATA_PAGAMENTO']); 
                $new = $partesData[2].'-'.$partesData[1].'-'.$partesData[0];
                $data['VALOR_PAGO'] = str_replace("R$", "", $data['VALOR_PAGO']);
                $data['VALOR_PAGO'] = str_replace(".", "", $data['VALOR_PAGO']);
                $data['VALOR_PAGO'] = str_replace(",", ".", $data['VALOR_PAGO']);
                $data['VALOR_PAGO'] = trim($data['VALOR_PAGO']);
                $dataParcelaPagamento = array (
                    'ID_PARCELA_PAGAR' => $this->_getParam('id'),
                    'ID_TIPO_PAGAMENTO' => $data['ID_TIPO_PAGAMENTO'],
                    'DATA_PAGAMENTO' => $new,
                    'VALOR_PAGO' => $data['VALOR_PAGO']
                );
                $this->_parcelaPagamento->update($dataParcelaPagamento, 'ID = '.$data['IDPG']);
                $status = array (
                    'ID_STATUS_PARCELA_PAGAR' => 2
                );
                $this->_parcelaPagar->update($status, 'ID = '. (int)$this->_getParam('id'));
                $this->_redirect('/admin/contaspagar');
            } else {
                echo '<div class="alert alert-block">Para efetuar o pagamento, você deve marcar como paga, informar a data, valor e forma de pagamento</div>';
            }
            
            
        }
        
    }
    
    public function verificacontasAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $contas  = $this->_parcelaPagar->fetchAll();
        foreach($contas as $value) {
            
            if($value->ID_STATUS_PARCELA_PAGAR == 1){
                if($value->DATA_VENCIMENTO < date('Y-m-d')) {
                    $status = array (
                        'ID_STATUS_PARCELA_PAGAR' => 4
                    );
                    $this->_parcelaPagar->update($status, 'ID = '.$value->ID);
                }
            }
            
        }
        
        $this->_redirect('/admin/contaspagar');
        
    }

    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $rParcela = $this->_parcelaPagar->find($this->_getParam('id'))->current();
//        $this->_lancamentoPagar->delete('ID = '.(int)$rParcela->ID_LANCAMENTO_PAGAR);
        $this->_parcelaPagar->delete('ID = '.(int)$this->_request->getParam('id'));   
        $this->_parcelaPagamento->delete('ID_PARCELA_PAGAR = '.(int)$this->_getParam('id'));  
        $this->_redirect('/admin/contaspagar');
    }
}

