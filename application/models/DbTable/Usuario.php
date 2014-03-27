<?php

class Application_Model_DbTable_Usuario extends Zend_Db_Table_Abstract
{

    protected $_name = 'usuarios';
    
    /**
    * Dependent tables
    */
    protected $_dependentTables = array('orcamentos');

    
}

