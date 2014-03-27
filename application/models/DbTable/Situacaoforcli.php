<?php

class Application_Model_DbTable_Situacaoforcli extends Zend_Db_Table_Abstract
{

    protected $_name = 'situacao_for_cli';
    
    /**
    * Dependent tables
    */
    protected $_dependentTables = array('pessoa');
    
   
}

