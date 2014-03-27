<?php

class Admin_OrcamentosController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $orcamentos = new Application_Model_DbTable_Orcamento();
        $this->view->orcamentos = $orcamentos->Orcamentos();
    }

    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $orcamento = new Application_Model_DbTable_Orcamento();
        $rorcamento = $orcamento->find($this->_request->getParam('id'))->current();
        $rorcamento->delete();
        
        $ferro = new Application_Model_DbTable_Tempferros();
        $ferro->deleteSessao($this->_request->getParam('sessao'), $this->_request->getParam('data'));
        $lajes = new Application_Model_DbTable_Temlaje();
        $lajes->deleteSessao($this->_request->getParam('sessao'), $this->_request->getParam('data'));
        $esteiras = new Application_Model_DbTable_Temesteira();
        $esteiras->deleteSessao($this->_request->getParam('sessao'), $this->_request->getParam('data'));
        
        $this->_redirect('/admin/orcamentos');
    }

}

