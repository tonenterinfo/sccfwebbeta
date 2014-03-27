<?php

class Application_Model_DbTable_NFedetalhe extends Zend_Db_Table_Abstract
{

    protected $_name = 'nfe_detalhe';
    
    /**
    * Dependent tables
    */
    //protected $_dependentTables = array('produto');
    
    public function findProd ($nome, $idNfeCabecalho) {
        $select = $this->select()
                ->from('nfe_detalhe')
                ->where('ID_NFE_CABECALHO = ?', $idNfeCabecalho)
                ->where('NOME_PRODUTO = ?', $nome);
        return $this->fetchAll($select);
    }
    
    public function fetch($id_produto) {
        $select = $this->select()
                ->from(array('d' => 'nfe_detalhe'), array('ID_PRODUTO' => 'ID_PRODUTO', 'ID_NFE_CABECALHO'=>'ID_NFE_CABECALHO', 'VALOR_UNITARIO_COMERCIAL'=>'VALOR_UNITARIO_COMERCIAL', 'CFOP'=>'CFOP'))
                //->join(array('c' => 'nfe_cabecalho'), 'c.ID = d.ID_NFE_CABECALHO', array('ID_FORNECEDOR' => 'ID_FORNECEDOR', 'DATA_EMISSAO' => 'DATA_EMISSAO'))
                ->where('d.ID_PRODUTO = ?',$id_produto)
                ->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
   
}

