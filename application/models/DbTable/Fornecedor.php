<?php

class Application_Model_DbTable_Fornecedor extends Zend_Db_Table_Abstract
{

    protected $_name = 'fornecedor';
    
    public function findByIdPessoa($id) {
      $select =  $this->select()
                ->from('fornecedor')
                ->where('ID_PESSOA = ?', $id);
        return $this->fetchAll($select);
    }
    
    public function findById ($id) {
        $select = $this->select()
                ->from(array('p'=>'pessoa'),array('NOME'=> 'NOME'))
                ->join(array('f'=>'fornecedor'), 'p.ID = f.ID_PESSOA', array('*'))
                ->where('f.ID = ?', $id)
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}

