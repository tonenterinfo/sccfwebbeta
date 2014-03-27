<?php

class Application_Model_DbTable_Pessoa extends Zend_Db_Table_Abstract
{

    protected $_name = 'pessoa';
    
    /**
    * Dependent tables
    */
    protected $_dependentTables = array('Application_Model_DbTable_Endereco');
    
    /**
    * Reference map
    */
    protected $_referenceMap = array
    (
        array(
            'Empresa' => array(
                    'refTableClass' => 'empresa',
                    'refColumns' => 'ID_EMPRESA',
                    'columns' => 'ID_EMPRESA'
                )
        )
    );

}

