<?php

class Application_Model_Vendacabecalho extends Application_Model_Abstract
{

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Vendacabecalho();
    }
    
}

