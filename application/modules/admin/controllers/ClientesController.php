<?php

class Admin_ClientesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //$clientes = new Application_Model_DbTable_Cliente();
        $clientes = new Application_Model_Cliente();
        $this->view->clientes = $clientes->fetchAll();
    }
    
    public function newAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['nome'] != "") {
               unset($data['submit']);
               $empresa = new Application_Model_DbTable_Cliente();
               $empresa->insert($data);
               $this->_redirect('/admin/clientes');
            }
        }
    }
    
    public function editAction() {
        $cliente = new Application_Model_DbTable_Cliente();
        $rcliente = $cliente->find($this->_request->getParam('id'))->current();
        $this->view->nome = $rcliente->nome;
        $this->view->telefone = $rcliente->telefone;
        $this->view->endereco = $rcliente->endereco;
        $this->view->usuarios_id = $rcliente->usuarios_id;
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['nome'] != "") {
               unset($data['submit']);
               $cliente->update($data, 'id = '.(int)$this->_getParam('id'));
               $this->_redirect('/admin/clientes');
            }
        }
    }
    
    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $cliente = new Application_Model_DbTable_Cliente();
        $rcliente = $cliente->find($this->_request->getParam('id'))->current();
        $rcliente->delete();
        $this->_redirect('/admin/clientes');
    }


}

