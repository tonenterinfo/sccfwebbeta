<?php

class Admin_FluxodecaixaController extends Zend_Controller_Action {

    protected $_parcelaRecebimento;
    protected $_parcelaPagamento;
    protected $_fluxoCaixa;
    protected $_vendaFaturamento;

    public function init() {
        $this->_parcelaRecebimento = new Application_Model_DbTable_Parcelarecebimento();
        $this->_parcelaPagamento = new Application_Model_DbTable_Parcelapagamento();
        $this->_fluxoCaixa = new Application_Model_DbTable_Fluxocaixa();
        $this->_vendaFaturamento = new Application_Model_DbTable_Vendafaturamento();
    }

    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();

            $partesDataInicial = explode("/", $data['inicial']);
            $newDateInicial = $partesDataInicial[2] . '-' . $partesDataInicial[1] . '-' . $partesDataInicial[0];
            $this->view->dataInicial = $newDateInicial;

            $partesDataFinal = explode("/", $data['final']);
            $newDateFinal = $partesDataFinal[2] . '-' . $partesDataFinal[1] . '-' . $partesDataFinal[0];
            $this->view->dataFinal = $newDateFinal;

            if (isset($data['DATA']) and isset($data['VALOR'])) {
                $partesData = explode("/", $data['DATA']);
                $newDate = $partesData[2] . '-' . $partesData[1] . '-' . $partesData[0];

                $data['VALOR'] = str_replace("R$", "", $data['VALOR']);
                $data['VALOR'] = str_replace(".", "", $data['VALOR']);
                $data['VALOR'] = str_replace(",", ".", $data['VALOR']);
                $data['VALOR'] = trim($data['VALOR']);
                
                $dataFluxo = array(
                    'DATA' => $newDate,
                    'DESCRICAO' => $data['DESCRICAO'],
                    'OPERADOR' => $data['OPERADOR'],
                    'VALOR' => $data['VALOR'],
                    'DESTINO' => $data['DESTINO']
                );
                $this->_fluxoCaixa->insert($dataFluxo);
            }

            $this->view->recebimentos = $this->_parcelaRecebimento->fetchPeriodo($newDateInicial, $newDateFinal);

            $this->view->pagamentos = $this->_parcelaPagamento->fetchPeriodo($newDateInicial, $newDateFinal);

            $this->view->fluxo = $this->_fluxoCaixa->fetchPeriodo($newDateInicial, $newDateFinal);
            
            $this->view->venda = $this->_vendaFaturamento->fetchPeriodo($newDateInicial, $newDateFinal);
        }
    }

    public function newAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
        }
    }

    public function editAction() {

        if ($this->getRequest()->isPost()) {
            
        }
    }

    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_fluxoCaixa->delete('ID = '.(int)$this->_request->getParam('id'));
        $this->_redirect('/admin/fluxodecaixa');
    }

}

