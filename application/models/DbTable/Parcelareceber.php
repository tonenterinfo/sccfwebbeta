<?php

class Application_Model_DbTable_Parcelareceber extends Zend_Db_Table_Abstract {

    protected $_name = 'parcela_receber';

    public function fetch() {
        $select = $this->select()
                ->from(array('p' => 'parcela_receber'), array('*'))
                ->join(array('l' => 'lancamento_receber'), 'l.ID = p.ID_LANCAMENTO_RECEBER', array('ID_CLIENTE' => 'ID_CLIENTE'))
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }

}

