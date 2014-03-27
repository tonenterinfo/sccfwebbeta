<?php

abstract class Application_Model_Abstract {
    
    protected $_dbTable;
    
    public function insert(array $data) {
//        foreach($_SESSION['empresa'] as $value) {
//            $data['usuarios_id'] = $value->id;
//        }  
//        $data['empresas_id'] = $_SESSION['empresaSel'][1];
        return $this->_dbTable->insert($data);
    }
        
    public function update(array $data, $where) {
        return $this->_dbTable->update($data, $this->_dbTable->getAdapter()
                                ->quoteInto("id = ?", $where));
    }
    
    public function delete($id) {
        return $this->_dbTable->delete($this->_dbTable->getAdapter()
                                ->quoteInto("id = ?", $id));
    }
    
    public function search(array $params) {
        $str = isset($params['str']) ? $params['str'] : "";
        $filtro = isset($params['filtro']) ? $params['filtro'] : "";
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $perage = isset($params['limit']) ? (int) $params['limit'] : 20;

        if (!empty($str)) {
            $where = $filtro . " like '%" . $str . "%'";
            $registros = $this->_dbTable->fetchAll($where);
            $paginator = Zend_Paginator::factory($registros);
        }
        else{
            $registros = $this->_dbTable->fetchAll();
            $paginator = Zend_Paginator::factory($registros);
        }

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($perage);

        $result = array();
        
        foreach ($paginator as $v) {
            $result[] = $v->toArray();
        }
        
        $result['total'] = count($registros);
        
        return $result;
    }
    
    public function fetchPair($table) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $this->_dbTable->select()
            ->from("$table", array('id', 'nome')); 
        return $db->fetchPairs($select);
    }
    
    public function findAnything($table, $colum, $id) {
        $select = $this->_dbTable->select()
                ->from("$table")
                ->where("$colum = ?",$id);
        return $this->_dbTable->fetchAll($select);
    }
}