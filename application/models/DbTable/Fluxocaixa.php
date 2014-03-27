<?php

class Application_Model_DbTable_Fluxocaixa extends Zend_Db_Table_Abstract
{

    protected $_name = 'fluxo_caixa';
    
    public function fetchPeriodo($inicial, $final) {
        $select = $this->select()
                ->from('fluxo_caixa')
                ->where('DATA >= ?',$inicial)
                ->where('DATA <= ?',$final)
                ->order('DATA ASC')
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }

}

