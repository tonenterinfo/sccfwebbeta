<?php

class Application_Model_Pessoa extends Application_Model_Abstract
{

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Pessoa();
    }
    
//    public function find($classe) {
//        $select = $this->_dbTable->select()
//                ->from(array('p'=>'pessoa'),array('NOME'=> 'NOME', 'ID_PESS'=>'ID'))
//                //->join(array('e'=>'endereco'), 'p.ID = e.ID_PESSOA', array('*'))
//                ->joinLeft('endereco', 'p.ID = endereco.ID_PESSOA', array('*'))
//                ->where('p.CLASSE = ?', $classe)
//                ->setIntegrityCheck(false);
//        return $this->_dbTable->fetchAll($select);
//    }
    
    public function find($classe) {
        $select = $this->_dbTable->select()
                ->from('pessoa')
                ->where('CLASSE = ?', $classe)
                ->order('NOME ASC')
                ->setIntegrityCheck(false);
        return $this->_dbTable->fetchAll($select);
    }
    
    public function status() {
        $stmt = $this->_dbTable->getAdapter()->query("SHOW TABLE STATUS LIKE 'pessoa';");
        return $tableStatus = $stmt->fetchObject();
    }


//    public function fetchAll() {
//        $select = $this->_dbTable->select()
//                ->from(array('p'=>'produtos_ferro'),array('*'))
//                ->join(array('t'=>'tipos_produtos'), 't.id = p.tipo_ferro', array('tipo'=>'nome'))
//                ->setIntegrityCheck(false);
//        return $this->_dbTable->fetchAll($select);
//    }
            

}

