<?php

class Application_Model_DbTable_Produto extends Zend_Db_Table_Abstract
{

    protected $_name = 'produto';
    
    /**
    * Dependent tables
    */
    //protected $_dependentTables = array('orcamentos');
    
    /**
    * Reference map
    */
    protected $_referenceMap = array
    (
        'Grupo' => array(
            'refTableClass' => 'produto_grupo',
            'refColumns' => 'ID_GRUPO',
            'columns' => 'ID_GRUPO',
        ),
        'Cfop' => array(
            'refTableClass' => 'cfop',
            'refColumns' => 'ID_CFOP',
            'columns' => 'ID_CFOP',
        )
    );
    
    public function findByEan($ean) {
      $select =  $this->select()
                ->from('produto')
                ->where('CODIGO_BARRAS = ?', $ean);
        return $this->fetchAll($select);
    }
}

