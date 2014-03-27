<?php

class Application_Model_DbTable_Vendacondicoesparcela extends Zend_Db_Table_Abstract
{

    protected $_name = 'venda_condicoes_parcelas';
    
    public function findCondicao($idCondicao) {
        $select = $this->select()
                ->from('venda_condicoes_parcelas')
                ->where('ID_VENDA_CONDICOES_PAGAMENTO = ?', $idCondicao);
        return $this->fetchAll($select);
    }
    
}

