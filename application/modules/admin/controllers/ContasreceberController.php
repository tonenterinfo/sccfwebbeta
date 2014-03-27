<?php

class Admin_ContasreceberController extends Zend_Controller_Action
{

    protected $cliente;
    protected $_cliente;
    protected $_lancamentoReceber;
    protected $_parcelaReceber;
    protected $_naturezaFinanceira;
    protected $_statusParcela;
    protected $_tipoRecebimento;
    protected $_parcelaRecebimento;

    public function init()
    {
        $this->cliente = new Application_Model_Pessoa();
        $this->_cliente = new Application_Model_DbTable_Pessoa();
        $this->_lancamentoReceber = new Application_Model_DbTable_Lancamentoreceber();
        $this->_parcelaReceber = new Application_Model_DbTable_Parcelareceber();
        $this->_naturezaFinanceira = new Application_Model_DbTable_Naturezafinanceirapagar();
        $this->_statusParcela = new Application_Model_DbTable_Statusparcelareceber();
        $this->_tipoRecebimento = new Application_Model_DbTable_Tiporecebimento();
        $this->_parcelaRecebimento = new Application_Model_DbTable_Parcelarecebimento();
    }

    public function indexAction()
    {
        $select = $this->_parcelaReceber->select();
        $select->from('parcela_receber')->order('DATA_VENCIMENTO ASC');
        $this->view->parcelas = $this->_parcelaReceber->fetchAll($select);
       
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
        $this->view->clientes = $this->cliente->find(1);
        $this->view->naturezafinanceira = $this->_naturezaFinanceira->fetchAll();
        $this->view->status = $this->_statusParcela->fetchAll();
        $this->view->tipoRecebimento = $this->_tipoRecebimento->fetchAll();
        
        $rParcela = $this->_parcelaReceber->find($this->_getParam('id'))->current();
        
        $rlancamento = $this->_lancamentoReceber->find($rParcela->ID_LANCAMENTO_RECEBER)->current();
        $rcliente = $this->_cliente->find($rlancamento->ID_CLIENTE)->current();
        $this->view->idCliente = $rlancamento->ID_CLIENTE;
        $this->view->cliente = $rcliente->NOME;
        
        $rNatureza = $this->_naturezaFinanceira->find($rlancamento->ID_NATUREZA_FINANCEIRA)->current();
        $this->view->idNatureza = $rlancamento->ID_NATUREZA_FINANCEIRA;
        $this->view->natureza = $rNatureza->DESCRICAO;

        $this->view->total = $rlancamento->VALOR_TOTAL;
        $this->view->parcela = $rParcela->VALOR;

        $rstatus = $this->_statusParcela->find($rParcela->ID_STATUS_PARCELA)->current();
        $this->view->idStatus = $rParcela->ID_STATUS_PARCELA;
        $this->view->stat = $rstatus->DESCRICAO;

        if($rParcela->ID_STATUS_PARCELA == 2 or $rParcela->ID_STATUS_PARCELA == 3) {
            $select = $this->_parcelaRecebimento->select();
            $select->from('parcela_recebimento')->where('ID_PARCELA_RECEBER = ' . (int)$this->_getParam('id'));
            $rdata = $this->_parcelaRecebimento->fetchAll($select);
            
            $this->view->recebimentos = $rdata;
            
//            $this->view->dataRecebimento = $rdata[0]->DATA_RECEBIMENTO;
//            $this->view->idTipoRecebimento = $rdata[0]->ID_TIPO_RECEBIMENTO;
//            $rtipoRecebimento = $this->_tipoRecebimento->find($rdata[0]->ID_TIPO_RECEBIMENTO)->current();
//            $this->view->tpRecebimento = $rtipoRecebimento->DESCRICAO;
//            $this->view->valPg = $rdata[0]->VALOR_RECEBIDO;
//            $this->view->idPgmto =$rdata[0]->ID;
        } else {
            $this->view->dataRecebimento;
            $this->view->idTipoRecebimento;
            $this->view->tpRecebimento;
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
            
            $dataLancamentoReceber = array(
                'ID_CLIENTE' => $data['ID_CLIENTE'],
                'ID_NATUREZA_FINANCEIRA' => $data['ID_PLANO_NATUREZA_FINANCEIRA'],
                'VALOR_TOTAL' => $data['VALOR_TOTAL'],
                'VALOR_A_RECEBER' => $data['VALOR_TOTAL'],
                'DATA_LANCAMENTO' => date('Y-m-d')
            );
            $this->_lancamentoReceber->update($dataLancamentoReceber, 'ID = '.$rParcela->ID_LANCAMENTO_RECEBER);
            if($data['ID_STATUS_PARCELA'] != 2) {
                $parcela_receber = array(
                    'ID_STATUS_PARCELA' => $data['ID_STATUS_PARCELA'],
                    'DATA_EMISSAO' => date('Y-m-d'),
                    'DATA_VENCIMENTO' => $newDate,
                    'VALOR' => $data['VALOR']
                );
            }
            if($data['ID_STATUS_PARCELA'] == 2) {
                $parcela_receber = array(
                    'ID_STATUS_PARCELA' => 2,
                    'DATA_EMISSAO' => date('Y-m-d'),
                    'DATA_VENCIMENTO' => $newDate,
                    'VALOR' => $data['VALOR']
                );
            }
            $this->_parcelaReceber->update($parcela_receber, 'ID = ' . (int) $this->_getParam('id'));
            for ($i = 0; $i <= (sizeof($data['DATA_RECEBIMENTO']) - 1); $i++) {
                if ($data['ID_STATUS_PARCELA'] == 2 or $data['ID_STATUS_PARCELA'] == 3 and $data['ID_STATUS_PARCELA'] != '' and $data['ID_TIPO_RECEBIMENTO'][$i] > 0 and $data['ID_TIPO_RECEBIMENTO'][$i] != '' and $data['IDPG'][$i] == 0) {
                    $partesData = explode("/", $data['DATA_RECEBIMENTO'][$i]);
                    $new = $partesData[2] . '-' . $partesData[1] . '-' . $partesData[0];
                    $data['VALOR_RECEBIDO'][$i] = str_replace("R$", "", $data['VALOR_RECEBIDO'][$i]);
                    $data['VALOR_RECEBIDO'][$i] = str_replace(".", "", $data['VALOR_RECEBIDO'][$i]);
                    $data['VALOR_RECEBIDO'][$i] = str_replace(",", ".", $data['VALOR_RECEBIDO'][$i]);
                    $data['VALOR_RECEBIDO'][$i] = trim($data['VALOR_RECEBIDO'][$i]);
                    $dataParcelaPagamento = array(
                        'ID_PARCELA_RECEBER' => $this->_getParam('id'),
                        'ID_TIPO_RECEBIMENTO' => $data['ID_TIPO_RECEBIMENTO'][$i],
                        'DATA_RECEBIMENTO' => $new,
                        'VALOR_RECEBIDO' => $data['VALOR_RECEBIDO'][$i]
                    );
                    $this->_parcelaRecebimento->insert($dataParcelaPagamento);

                    $status = array(
                        'ID_STATUS_PARCELA' => $data['ID_STATUS_PARCELA']
                    );
                    $this->_parcelaReceber->update($status, 'ID = ' . (int) $this->_getParam('id'));
                }
                
                if ($data['ID_STATUS_PARCELA'] == 2 or $data['ID_STATUS_PARCELA'] == 3 and $data['ID_STATUS_PARCELA'] != '' and $data['ID_TIPO_RECEBIMENTO'][$i] > 0 and $data['ID_TIPO_RECEBIMENTO'][$i] != '' and $data['IDPG'][$i] > 0) {
                    $partesData = explode("/", $data['DATA_RECEBIMENTO'][$i]);
                    $new = $partesData[2] . '-' . $partesData[1] . '-' . $partesData[0];
                    $data['VALOR_RECEBIDO'][$i] = str_replace("R$", "", $data['VALOR_RECEBIDO'][$i]);
                    $data['VALOR_RECEBIDO'][$i] = str_replace(".", "", $data['VALOR_RECEBIDO'][$i]);
                    $data['VALOR_RECEBIDO'][$i] = str_replace(",", ".", $data['VALOR_RECEBIDO'][$i]);
                    $data['VALOR_RECEBIDO'][$i] = trim($data['VALOR_RECEBIDO'][$i]);
                    $dataParcelaPagamento = array(
                        'ID_PARCELA_RECEBER' => $this->_getParam('id'),
                        'ID_TIPO_RECEBIMENTO' => $data['ID_TIPO_RECEBIMENTO'][$i],
                        'DATA_RECEBIMENTO' => $new,
                        'VALOR_RECEBIDO' => $data['VALOR_RECEBIDO'][$i]
                    );
                    $this->_parcelaRecebimento->update($dataParcelaPagamento, 'ID = '.$data['IDPG'][$i]);

                    $status = array(
                        'ID_STATUS_PARCELA' => $data['ID_STATUS_PARCELA']
                    );
                    $this->_parcelaReceber->update($status, 'ID = ' . (int) $this->_getParam('id'));
                }
                else {
                    echo "Para efetuar o pagamento, você deve marcar como paga, informar a ";
                }
                if ($data['ID_STATUS_PARCELA'] == 2 or $data['ID_STATUS_PARCELA'] == 3 and $data['ID_STATUS_PARCELA'] != '' and $data['VALOR_RECEBIDO'][$i] == 0 or $data['VALOR_RECEBIDO'][$i] == '' and $data['IDPG'][$i] > 0) {
                    $this->_parcelaRecebimento->delete('ID = '.$data['IDPG'][$i]);
                }
                
            }

            $this->_redirect('/admin/contasreceber');
        }
        
    }
    
    public function verificacontasAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $contas  = $this->_parcelaReceber->fetchAll();
        foreach($contas as $value) {
            
            if($value->ID_STATUS_PARCELA == 1){
                if($value->DATA_VENCIMENTO < date('Y-m-d')) {
                    $status = array (
                        'ID_STATUS_PARCELA' => 4
                    );
                    $this->_parcelaReceber->update($status, 'ID = '.$value->ID);
                }
            }
            
        }
        
        $this->_redirect('/admin/contasreceber');
        
    }

    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $rParcela = $this->_parcelaReceber->find($this->_getParam('id'))->current();
//        $this->_lancamentoReceber->delete('ID = '.(int)$rParcela->ID_LANCAMENTO_RECEBER);
        $this->_parcelaReceber->delete('ID = '.(int)$this->_request->getParam('id'));   
        $this->_parcelaRecebimento->delete('ID_PARCELA_RECEBER = '.(int)$this->_getParam('id'));  
        $this->_redirect('/admin/contasreceber');
    }
}

