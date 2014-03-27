<?php

class Application_Model_DbTable_Municipio extends Zend_Db_Table_Abstract
{

    protected $_name = 'municipio';
    
    public function findCidade($uf) {
        $select = $this->select()
                ->from('municipio')
                ->where('ID_UF = ?', $uf);
        return $this->fetchAll($select);
    }
    

}

