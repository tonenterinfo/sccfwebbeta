<?php

class Application_Model_DbTable_Parcelarecebimento extends Zend_Db_Table_Abstract {

    protected $_name = 'parcela_recebimento';
    
    public function fetchPeriodo($inicial, $final) {
        $select = $this->select()
                ->from(array('p' => 'parcela_recebimento'), array('*'))
                ->join(array('r' => 'parcela_receber'), 'r.ID = p.ID_PARCELA_RECEBER', array('ID_CLIENTE' => 'ID_CLIENTE'))
                ->where('p.DATA_RECEBIMENTO >= ?',$inicial)
                ->where('p.DATA_RECEBIMENTO <= ?',$final)
                ->order('p.DATA_RECEBIMENTO ASC')
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}

