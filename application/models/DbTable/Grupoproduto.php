<?php

class Application_Model_DbTable_Grupoproduto extends Zend_Db_Table_Abstract
{

    protected $_name = 'produto_grupo';
    
    /**
    * Dependent tables
    */
    protected $_dependentTables = array('produto');
    
   
}

