<?php

class Application_Model_DbTable_Vendafaturamento extends Zend_Db_Table_Abstract
{

    protected $_name = 'venda_faturamento';
    
    public function fetchPeriodo($inicial, $final) {
        $select = $this->select()
                ->from('venda_faturamento')
                ->where('DATA >= ?',$inicial)
                ->where('DATA <= ?',$final)
                ->order('DATA ASC')
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }

}

