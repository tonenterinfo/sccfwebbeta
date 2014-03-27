<?php

class Application_Model_Usuario extends Application_Model_Abstract
{

    public function __construct() {
        $this->_dbTable = new Application_Model_DbTable_Usuario();
    }
}

