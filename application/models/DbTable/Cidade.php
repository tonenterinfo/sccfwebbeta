<?php

class Application_Model_DbTable_Cidade extends Zend_Db_Table_Abstract
{

    protected $_name = 'cidade';
    
    public function findCidade($uf) {
        $select = $this->select()
                ->from('cidade')
                ->where('SIGLA = ?', $uf);
        return $this->fetchAll($select);
    }
    

}

