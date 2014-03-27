<?php

class Application_Model_DbTable_Pessoajuridica extends Zend_Db_Table_Abstract
{

    protected $_name = 'pessoa_juridica';
    
    
    /**
    * Reference map
    */
//    protected $_referenceMap = array
//    (
//        array(
//            'Empresa' => array(
//                    'refTableClass' => 'pessoa',
//                    'refColumns' => 'ID_PESSOA',
//                    'columns' => 'ID'
//                )
//        )
//    );
    
    public function findByIdPessoa($id) {
      $select =  $this->select()
                ->from('pessoa_juridica')
                ->where('ID_PESSOA = ?', $id);
        return $this->fetchAll($select);
    }

}

