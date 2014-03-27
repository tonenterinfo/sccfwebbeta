<?php

class Application_Model_DbTable_Cliente extends Zend_Db_Table_Abstract
{

    protected $_name = 'cliente';
    
    
    /**
    * Reference map
    */
//    protected $_referenceMap = array
//    (
//        'Pessoa' => array(
//            'refTableClass' => 'pessoa',
//            'refColumns' => 'ID_PESSOA',
//            'columns' => 'ID'
//        ),
//        'Situacao' => array(
//            'refTableClass' => 'situacao_for_cli',
//            'refColumns' => 'ID_SITUACAO_FOR_CLI',
//            'columns' => 'ID'
//        )
//    );
    
    public function findByIdPessoa($id) {
      $select =  $this->select()
                ->from('cliente')
                ->where('ID_PESSOA = ?', $id);
        return $this->fetchAll($select);
    }
    
}

