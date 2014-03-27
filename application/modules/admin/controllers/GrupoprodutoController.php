<?php

class Admin_GrupoprodutoController extends Zend_Controller_Action
{

    protected $_grupos;


    public function init()
    {
        $this->_grupos = new Application_Model_DbTable_Grupoproduto();
    }

    public function indexAction()
    {
        $this->view->grupos = $this->_grupos->fetchAll();
    }
    
    public function newAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['NOME'] != "") {
               unset($data['submit']);
               $this->_grupos->insert($data);
               $this->_redirect('/admin/grupoproduto');
            }
        }
    }
    
    public function editAction() {

        $rgrupo = $this->_grupos->find($this->_request->getParam('id'))->current();
        $this->view->nome = $rgrupo->NOME;

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['NOME'] != "") {
               unset($data['submit']);
               $this->_grupos->update($data, 'ID = '.(int)$this->_getParam('id'));
               $this->_redirect('/admin/grupoproduto');
            }
        }
    }
    
    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $rgrupo = $this->_grupos->find($this->_request->getParam('id'))->current();
        $rgrupo->delete();
        $this->_redirect('/admin/grupoproduto');
    }


}

