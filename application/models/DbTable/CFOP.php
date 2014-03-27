<?php

class Application_Model_DbTable_CFOP extends Zend_Db_Table_Abstract
{

    protected $_name = 'cfop';
    
    /**
    * Dependent tables
    */
    protected $_dependentTables = array('produto');
    
   
}

