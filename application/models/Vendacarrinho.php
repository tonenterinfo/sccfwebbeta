<?php

class Application_Model_Vendacarrinho extends Application_Model_Abstract
{

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Vendacarrinho();
    }
    
    public function findCompra () {
        $select = $this->_dbTable->select()
                ->from('venda_orcamento_carrinho')
                ->where('SESSAO = ?', session_id())
                ->where('DATA = ?', date('y-m-d'));
        return $this->_dbTable->fetchAll($select);
    }
}

