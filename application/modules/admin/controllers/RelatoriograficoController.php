<?php

class Admin_RelatoriograficoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $vendas = new Application_Model_DbTable_Vendacabecalho();
       $selectVendas = $vendas->select();
       $selectVendas->from(
            'venda_orcamento_cabecalho',
            array(
                    'MONTH(DATA_CADASTRO) as mes',
                    'YEAR(DATA_CADASTRO) as ano',
                    'SUM(VALOR_TOTAL) as total'
                )
        )
       ->where('YEAR(DATA_CADASTRO) >= 2014')
       ->group('MONTH(DATA_CADASTRO), YEAR(DATA_CADASTRO)')
       ->order('DATA_CADASTRO ASC');
       $this->view->relatorioVendas = $vendas->fetchAll($selectVendas);


       //CONTAS PAGAS
       $contasPagas = new Application_Model_DbTable_Parcelapagamento();
       $selectContasPagas = $contasPagas->select();
       $selectContasPagas->from('parcela_pagamento',
            array(
                    'MONTH(DATA_PAGAMENTO) as mes',
                    'YEAR(DATA_PAGAMENTO) as ano',
                    'SUM(VALOR_PAGO) as total'
                )
        )
       ->where('YEAR(DATA_PAGAMENTO) >= 2014')
       ->group('MONTH(DATA_PAGAMENTO), YEAR(DATA_PAGAMENTO)')
       ->order('DATA_PAGAMENTO ASC');
       $this->view->contasPagas = $contasPagas->fetchAll($selectContasPagas);

       //CONTAS RECEBIDAS
       $contasRecebidas = new Application_Model_DbTable_Parcelarecebimento();
       $selectContasRecebidas = $contasRecebidas->select();
       $selectContasRecebidas->from('parcela_recebimento',
            array(
                    'MONTH(DATA_RECEBIMENTO) as mes',
                    'YEAR(DATA_RECEBIMENTO) as ano',
                    'SUM(VALOR_RECEBIDO) as total'
                )
        )
       ->where('YEAR(DATA_RECEBIMENTO) >= 2014')
       ->group('MONTH(DATA_RECEBIMENTO), YEAR(DATA_RECEBIMENTO)')
       ->order('DATA_RECEBIMENTO ASC');
       $this->view->contasRecebidas = $contasRecebidas->fetchAll($selectContasRecebidas);

       //RECEBIMENTOS A VISTA
       $aVista = new Application_Model_DbTable_Vendafaturamento();
       $selectAVista= $aVista->select();
       $selectAVista->from('venda_faturamento',
            array(
                    'MONTH(DATA) as mes',
                    'YEAR(DATA) as ano',
                    'SUM(TOTAL) as total'
                )
        )
       ->where('YEAR(DATA) >= 2014 AND ID_VENDA_CONDICAO_PAGAMENTO = 1')
       ->group('MONTH(DATA), YEAR(DATA)')
       ->order('DATA ASC');
       $this->view->aVista = $aVista->fetchAll($selectAVista);


       //ENTRADAS
       /*
       $entradas = new Application_Model_DbTable_Vendafaturamento();
       $selectEntradas = $entradas->select()
       ->from(array('v'=>'venda_faturamento'), array('*'))
       ->join(array('r'=>'parcela_recebimento'), 'SUM(v.TOTAL) + SUM(r.VALOR_RECEBIDO) ', array('*'))
       ->setIntegrityCheck(false)
       ->where('YEAR(v.DATA) >= 2014 AND YEAR(r.DATA_RECEBIMENTO) >= 2014 AND v.ID_VENDA_CONDICAO_PAGAMENTO = 1')
       ->group('MONTH(r.DATA_RECEBIMENTO), YEAR(r.DATA_RECEBIMENTO)')
       ->order('r.DATA_RECEBIMENTO ASC');
       $this->view->entradas = $entradas->fetchAll($selectEntradas);
      */
       
       //SAÍDAS
       $fluxoCaixa = new Application_Model_DbTable_Fluxocaixa();
       $selectFluxo = $fluxoCaixa->select();
       $selectFluxo->from('fluxo_caixa',
            array(
                    'MONTH(DATA) as mes',
                    'YEAR(DATA) as ano',
                    'SUM(VALOR) as total'
                )
        )
       ->where('YEAR(DATA) >= 2014 AND OPERADOR = "-"')
       ->group('MONTH(DATA), YEAR(DATA)')
       ->order('DATA ASC');
       $this->view->saidas = $fluxoCaixa->fetchAll($selectFluxo);      
      
       //COMPRAS
       $compras = new Application_Model_DbTable_NFecabecalho();
       $selectCompras= $compras->select();
       $selectCompras->from('nfe_cabecalho',
            array(
                    'MONTH(DATA_ENTRADA_SAIDA) as mes',
                    'YEAR(DATA_ENTRADA_SAIDA) as ano',
                    'SUM(VALOR_TOTAL_PRODUTOS) as totalProdutos',
                    'SUM(VALOR_FRETE) as totalFrete',
                    'SUM(VALOR_DESPESAS_ACESSORIAS) as totalDespesas'
                )
        )
       ->where('YEAR(DATA_ENTRADA_SAIDA) >= 2014')
       ->group('MONTH(DATA_ENTRADA_SAIDA), YEAR(DATA_ENTRADA_SAIDA)')
       ->order('DATA_ENTRADA_SAIDA ASC');
       $this->view->compras = $compras->fetchAll($selectCompras);

    }
    
    public function newAction() {
        
    }
    

}

