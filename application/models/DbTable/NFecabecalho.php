<?php

class Application_Model_DbTable_NFecabecalho extends Zend_Db_Table_Abstract
{

    protected $_name = 'nfe_cabecalho';
    
    /**
    * Dependent tables
    */
    //protected $_dependentTables = array('produto');
    
    public function findByCh($chave) {
      $select =  $this->select()
                ->from('nfe_cabecalho')
                ->where('CHAVE_ACESSO = ?', $chave);
        return $this->fetchAll($select);
    }
   
}

