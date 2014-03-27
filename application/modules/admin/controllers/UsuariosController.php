<?php

class Admin_UsuariosController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $usuarios = new Application_Model_DbTable_Usuario();
        $this->view->usuarios = $usuarios->fetchAll();
    }
    
    public function newAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['email'] and $data['senha'] != "") {
               $data['senha'] = sha1($data['senha']);
               unset($data['submit']);
               $usuario = new Application_Model_DbTable_Usuario();
               $usuario->insert($data);
               $this->_redirect('/admin/usuarios');
            }
        }
    }

    public function editAction() {
        $usuarios = new Application_Model_DbTable_Usuario();
        $rusuarios = $usuarios->find($this->_request->getParam('id'))->current();
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['email'] and $data['senha'] != "") {
               $data['senha'] = sha1($data['senha']);
               unset($data['submit']);
               $usuario = new Application_Model_DbTable_Usuario();
               $usuario->update($data, 'id = '.(int)$this->_getParam('id'));
               $this->_redirect('/admin/usuarios');
            } else {
                echo "<script> alert('Os campos de email e senha não podem estar vazios'); </script>";
            }
        }
        $this->view->nome = $rusuarios->nome;
        $this->view->email = $rusuarios->email;
        $this->view->status = $rusuarios->status;
        $this->view->nivel = $rusuarios->nivel;
    }

    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $usuario = new Application_Model_DbTable_Usuario();
        $rusuario = $usuario->find($this->_request->getParam('id'))->current();
        $rusuario->delete();
        $this->_redirect('/admin/usuarios');
    }
}

