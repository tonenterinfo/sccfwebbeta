<?php

class Application_Model_DbTable_Parcelapagamento extends Zend_Db_Table_Abstract {

    protected $_name = 'parcela_pagamento';
    
    public function fetchPeriodo($inicial, $final) {
        $select = $this->select()
                ->from(array('p' => 'parcela_pagamento'), array('*'))
                ->join(array('r' => 'parcela_pagar'), 'r.ID = p.ID_PARCELA_PAGAR', array('ID_LANCAMENTO_PAGAR' => 'ID_LANCAMENTO_PAGAR'))
                ->where('p.DATA_PAGAMENTO >= ?',$inicial)
                ->where('p.DATA_PAGAMENTO <= ?',$final)
                ->order('p.DATA_PAGAMENTO ASC')
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}

