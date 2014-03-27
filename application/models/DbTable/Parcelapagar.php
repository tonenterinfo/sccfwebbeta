<?php

class Application_Model_DbTable_Parcelapagar extends Zend_Db_Table_Abstract {

    protected $_name = 'parcela_pagar';

    public function fetch() {
        $select = $this->select()
                ->from(array('p' => 'parcela_pagar'), array('*'))
                ->join(array('l' => 'lancamento_pagar'), 'l.ID = p.ID_LANCAMENTO_PAGAR', array('ID_FORNECEDOR' => 'ID_FORNECEDOR'))
                ->order('DATA_VENCIMENTO DESC')
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}

