<?php

class Admin_ProdutosController extends Zend_Controller_Action
{
    
    protected $_produtos;
    protected $_produtoFornecedores;


    public function init()
    {
        $this->_produtos = new Application_Model_DbTable_Produto();
        $this->_produtoFornecedores = new Application_Model_DbTable_Fornecedorproduto();
        $fornecedores = new Application_Model_Pessoa();
        $this->view->fornecedores = $fornecedores->find(2);
    }

    public function indexAction()
    {
        $select = $this->_produtos->select();
        $select->from('produto')->where('DESTINO = 0')->order('NOME ASC');
        $dados = $this->_produtos->fetchAll($select);
        $this->view->prods = $this->_produtos->fetchAll($select);
        $pagina = intval($this->_getParam('page', 1));
               
        $paginator = Zend_Paginator::factory($dados);
        // Seta a quantidade de registros por página
        $paginator->setItemCountPerPage(50);
        // numero de paginas que serão exibidas
        $paginator->setPageRange(13);
        // Seta a página atual
        $paginator->setCurrentPageNumber($pagina);
        // Passa o paginator para a view
        $this->view->produtos = $paginator;
    }
    
    public function newAction() {
        $grupo = new Application_Model_DbTable_Grupoproduto();
        $this->view->grupo = $grupo->fetchAll();
        
        
        
        $cfop = new Application_Model_DbTable_CFOP();
        $this->view->cfop = $cfop->fetchAll();
        
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['NOME'] != "") {
               unset($data['submit']);
               $data['QUANTIDADE_ESTOQUE'] = str_replace(".", "", $data['QUANTIDADE_ESTOQUE']);
               $data['QUANTIDADE_ESTOQUE'] = str_replace(",", ".", $data['QUANTIDADE_ESTOQUE']);
               
               $data['VALOR_COMPRA'] = str_replace("R$", "", $data['VALOR_COMPRA']);
               $data['VALOR_COMPRA'] = str_replace(".", "", $data['VALOR_COMPRA']);
               $data['VALOR_COMPRA'] = str_replace(",", ".", $data['VALOR_COMPRA']);
               $data['VALOR_COMPRA'] = trim($data['VALOR_COMPRA']);
               
               $data['VALOR_VENDA'] = str_replace("R$", "", $data['VALOR_VENDA']);
               $data['VALOR_VENDA'] = str_replace(".", "", $data['VALOR_VENDA']);
               $data['VALOR_VENDA'] = str_replace(",", ".", $data['VALOR_VENDA']);
               $data['VALOR_VENDA'] = trim($data['VALOR_VENDA']);
               $forn = $data['ID_FORNECEDOR'];
               unset($data['ID_FORNECEDOR']);
               $idproduto = $this->_produtos->insert($data);
               $dataFornProd = array(
                   'ID_FORNECEDOR' => $forn,
                   'ID_PRODUTO' => $idproduto
               );
               $this->_produtoFornecedores->insert($dataFornProd);
               
               $this->_redirect('/admin/produtos');
            }
        }
    }
    
    public function editAction() {
        $grupo = new Application_Model_DbTable_Grupoproduto();
        $this->view->grupo = $grupo->fetchAll();
        
        $cfop = new Application_Model_DbTable_CFOP();
        $this->view->cfop = $cfop->fetchAll();
        
        $fornecedores = new Application_Model_DbTable_Fornecedorproduto();
        $this->view->fornsprod = $fornecedores->findByProd((int)$this->_getParam('id'));
        
        $rproduto = $this->_produtos->find($this->_request->getParam('id'))->current();
        $this->view->id = $rproduto->ID;
        $this->view->codigo_barras = $rproduto->CODIGO_BARRAS;
        $this->view->nome = $rproduto->NOME;
        $this->view->grupo_id = $rproduto->ID_GRUPO;
        $this->view->qtd = $rproduto->QUANTIDADE_ESTOQUE;
        $this->view->qtdReal = $rproduto->QUANTIDADE_ESTOQUE_REAL;
        $this->view->valor_compra = $rproduto->VALOR_COMPRA;
        $this->view->valor_venda = $rproduto->VALOR_VENDA;
        $this->view->ncm = $rproduto->NCM;
        $this->view->cst = $rproduto->CST_NF;
        $this->view->cfopx = $rproduto->ID_CFOP;
        $this->view->destino = $rproduto->DESTINO;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if($data['NOME'] != "") {
               unset($data['submit']);
               $data['QUANTIDADE_ESTOQUE'] = str_replace(".", "", $data['QUANTIDADE_ESTOQUE']);
               $data['QUANTIDADE_ESTOQUE'] = str_replace(",", ".", $data['QUANTIDADE_ESTOQUE']);
               
               $data['QUANTIDADE_ESTOQUE_REAL'] = str_replace(".", "", $data['QUANTIDADE_ESTOQUE_REAL']);
               $data['QUANTIDADE_ESTOQUE_REAL'] = str_replace(",", ".", $data['QUANTIDADE_ESTOQUE_REAL']);
               
               $data['VALOR_COMPRA'] = str_replace("R$", "", $data['VALOR_COMPRA']);
               $data['VALOR_COMPRA'] = str_replace(".", "", $data['VALOR_COMPRA']);
               $data['VALOR_COMPRA'] = str_replace(",", ".", $data['VALOR_COMPRA']);
               $data['VALOR_COMPRA'] = trim($data['VALOR_COMPRA']);
               
               $data['VALOR_VENDA'] = str_replace("R$", "", $data['VALOR_VENDA']);
               $data['VALOR_VENDA'] = str_replace(".", "", $data['VALOR_VENDA']);
               $data['VALOR_VENDA'] = str_replace(",", ".", $data['VALOR_VENDA']);
               $data['VALOR_VENDA'] = trim($data['VALOR_VENDA']);
               $forn = $data['ID_FORNECEDOR'];
               $data['CODIGO_BARRAS'] = trim($data['CODIGO_BARRAS']);

               unset($data['ID_FORNECEDOR']);
               
              $this->_produtos->update($data, 'ID = '.(int)$this->_getParam('id'));
              
              if($forn > 0) {
              $dataFornProd = array(
                   'ID_FORNECEDOR' => $forn,
                   'ID_PRODUTO' => (int)$this->_getParam('id')
               );
               $this->_produtoFornecedores->insert($dataFornProd);
              }
              $this->_redirect('/admin/produtos');
            }
        }
    }
    
    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $rproduto = $this->_produtos->find($this->_request->getParam('id'))->current();
        $rproduto->delete();     
        $this->_produtoFornecedores->delete('ID_PRODUTO = '.(int)$this->_getParam('id'));
        $this->_redirect('/admin/produtos');
    }
    
    public function buscaAction() {
        
        $this->_helper->viewRenderer->setNoRender(true);
        
        if($_POST['produtos'] !=0) {
            
            $this->_redirect('/admin/produtos/edit/id/'.$_POST['produtos']);
            
        } else {
            
            $this->_redirect('/admin/produtos/');
        }
        
    }

        public function exportAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $produtos = $this->_produtos->fetchAll();
        
        $xlsTbl = $this->exportContacts($this->_getParam('destino'));
        header("Content-Type: application/vnd.ms-excel");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("content-disposition: attachment;filename=balanco_EnterInfo-" . time() . ".xls");
 
        echo "<table>$xlsTbl</table>";
        
        
        foreach ($produtos as $value) {
            
            
            
        }
        
        
    }
    
    public function exportContacts($destino) {
 
		$produtos = new Application_Model_DbTable_Produto();
                $select = $produtos->select();
                $select->from('produto')->where('QUANTIDADE_ESTOQUE > 0 AND DESTINO = '.$destino);
                $prods = $produtos->fetchAll($select);
                
                
			$xlsTbl = "<tr><th>EAN</th><th>Descricao</th><th>NCM</th><th>Unidade</th><th>Quantidade</th><th>Valor</th></tr>";
		foreach($prods as $val){
			$xlsTbl .= "<tr>";
			$xlsTbl .= "<td>" . $val->CODIGO_BARRAS . "</td>";
			$xlsTbl .= "<td>" . utf8_decode($val->NOME) . "</td>";
			$xlsTbl .= "<td>" . $val->NCM . "</td>";
                        $xlsTbl .= "<td>UN</td>";
			$xlsTbl .= "<td>" . number_format($val->QUANTIDADE_ESTOQUE,2,'.','') . "</td>";
			$xlsTbl .= "<td>" . number_format($val->VALOR_COMPRA,2,'.','') . "</td>";

			$xlsTbl .= "</tr>";
		}
 
		return $xlsTbl;
	}


}

