<?php

class Admin_ProdutosconsumoController extends Zend_Controller_Action
{
    
    protected $_produtos;
    protected $_produtoFornecedores;


    public function init()
    {
        $this->_produtos = new Application_Model_DbTable_Produto();
        $this->_produtoFornecedores = new Application_Model_DbTable_Fornecedorproduto();
        $fornecedores = new Application_Model_Pessoa();
        $this->view->fornecedores = $fornecedores->find(2);
    }

    public function indexAction()
    {
        $select = $this->_produtos->select();
        $select->from('produto')->where('DESTINO = 1')->order('NOME ASC');
        $this->view->produtos = $this->_produtos->fetchAll($select);
    }
    
    
}

