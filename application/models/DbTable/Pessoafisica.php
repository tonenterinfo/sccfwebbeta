<?php

class Application_Model_DbTable_Pessoafisica extends Zend_Db_Table_Abstract
{

    protected $_name = 'pessoa_fisica';
    
    
    /**
    * Reference map
    */
    protected $_referenceMap = array
    (
        array(
            'Empresa' => array(
                    'refTableClass' => 'pessoa',
                    'refColumns' => 'ID_PESSOA',
                    'columns' => 'ID'
                )
        )
    );
    
    public function findByIdPessoa($id) {
      $select =  $this->select()
                ->from('pessoa_fisica')
                ->where('ID_PESSOA = ?', $id);
        return $this->fetchAll($select);
    }

}

