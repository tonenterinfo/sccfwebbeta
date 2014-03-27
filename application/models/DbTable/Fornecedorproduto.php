<?php

class Application_Model_DbTable_Fornecedorproduto extends Zend_Db_Table_Abstract
{

    protected $_name = 'fornecedor_produto';
    
    /**
    * Dependent tables
    */
    protected $_dependentTables = array('pessoa');
    
    public function findByProd($id) {
        $seletc = $this->select()
                ->from('fornecedor_produto')
                ->where('ID_PRODUTO = ?', $id);
        return $this->fetchAll($seletc);
    }
    
    public function findFornProd ($fornecedor, $produto) {
        $select = $this->select()
                ->from('fornecedor_produto')
                ->where('ID_FORNECEDOR = ?', $fornecedor)
                ->where('ID_PRODUTO = ?', $produto);
        return $this->fetchAll($select);
    }
}

