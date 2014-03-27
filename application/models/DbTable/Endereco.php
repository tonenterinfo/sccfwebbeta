<?php

class Application_Model_DbTable_Endereco extends Zend_Db_Table_Abstract
{

    protected $_name = 'endereco';
    
    protected $_referenceMap = array(
    'Pessoa' => array(
        'columns' => array('ID_PESSOA'),
        'refTableClass' => 'pessoa',
        'refColumns' => array('ID'),
        'onDelete' => self::CASCADE
    )
    );
    
    public function insert(array $data) {
        
        parent::insert($data);
    }
    
    public function findByIdPessoa($id) {
      $select =  $this->select()
                ->from('endereco')
                ->where('ID_PESSOA = ?', $id);
        return $this->fetchAll($select);
    }

}

