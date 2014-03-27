<?php

class Admin_SaidaprodutoController extends Zend_Controller_Action {

    protected $_pessoas;
    protected $_produtos;
    protected $_vendasCabecalho;
    protected $vendasCabecalho;
    protected $vendaCarrinho;
    protected $_vendaCarrinho;
    protected $_condicoesPagamento;
    protected $_condicaoParcelas;
    protected $_vendaFaturamento;
    protected $vendaFaturamento;
    protected $_lancamentoReceber;
    protected $_parcelaReceber;

    public function init() {
        $this->_pessoas = new Application_Model_Pessoa();
        $this->_produtos = new Application_Model_DbTable_Produto();
        $this->_vendasCabecalho = new Application_Model_DbTable_Vendacabecalho();
        $this->vendasCabecalho = new Application_Model_Vendacabecalho();
        $this->vendaCarrinho = new Application_Model_Vendacarrinho();
        $this->_vendaCarrinho = new Application_Model_DbTable_Vendacarrinho();
        $this->_condicoesPagamento = new Application_Model_DbTable_Vendacondicoespagamento();
        $this->_condicaoParcelas = new Application_Model_DbTable_Vendacondicoesparcela();
        $this->_vendaFaturamento = new Application_Model_DbTable_Vendafaturamento();
        $this->vendaFaturamento = new Application_Model_Vendafaturamento();
        $this->_lancamentoReceber = new Application_Model_DbTable_Lancamentoreceber();
        $this->_parcelaReceber = new Application_Model_DbTable_Parcelareceber();
    }

    public function indexAction() {
        $select = $this->_vendasCabecalho->select();
        $select->from('venda_orcamento_cabecalho')->order('DATA_CADASTRO DESC');
        $this->view->vendas = $this->_vendasCabecalho->fetchAll($select);
    }

    public function newAction() {
        $pessoa = new Application_Model_DbTable_Pessoa;
        $select = $pessoa->select();
        $select->from('pessoa')->where('CLASSE = 1')->order('NOME ASC');
        $this->view->clientes = $pessoa->fetchAll($select);
        $select = $this->_produtos->select();
        $select->from('produto')->where('DESTINO = 0 OR DESTINO = 3 AND QUANTIDADE_ESTOQUE > 0')->order('NOME ASC');
        $this->view->produtos = $this->_produtos->fetchAll($select);
        $this->view->condicoesPagamento = $this->_condicoesPagamento->fetchAll();
    }

    public function cadastrasaidaAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            $partesData = explode("/", $data['DATA_CADASTRO']); 
            $newDate = $partesData[2].'-'.$partesData[1].'-'.$partesData[0];
            
            if (isset($_SESSION['admin'])) {
                foreach ($_SESSION['admin'] as $value) {
                    $idVendedor = $value->id;
                }
            }
            $dataVendaOrcamentoCabecalho = array(
                'ID' => NULL,
                'ID_VENDEDOR' => $idVendedor,
                'ID_CLIENTE' => $data['ID_CLIENTE'],
                'TIPO' => $data['TIPO'],
                'DATA_CADASTRO' => $newDate,
                'VALOR_SUBTOTAL' => $data['VALOR_SUBTOTAL'],
                'TAXA_DESCONTO' => $data['TAXA_DESCONTO'],
                'VALOR_DESCONTO' => $data['VALOR_DESCONTO'],
                'VALOR_TOTAL' => $data['VALOR_TOTAL'],
                'STATUS_PEDIDO' => 0,
                'OBSERVACAO' => $data['OBSERVACAO']
            );
            $idVendaOrcamentoCabecalho = $this->vendasCabecalho->insert($dataVendaOrcamentoCabecalho);
            
            $dataCarrinho = array(
              'ID_VENDA_ORCAMENTO_CABECALHO' => $idVendaOrcamentoCabecalho  
            );
            $where = array(
                'SESSAO = ?' => session_id(),
                'DATA = ? ' => date('Y-m-d')
            );
            $carrinho = new Application_Model_DbTable_Vendacarrinho();
            $carrinho->update($dataCarrinho, $where);
            
            if($data['TIPO'] == 'S') {
                $car = $carrinho->fetchAll($where);
                //ATUALIZAR ESTOQUE REAL
                foreach ($car as $value) {
                    $produto = $this->_produtos->find($value->ID_PRODUTO)->current();
                    $novaQtd = $produto->QUANTIDADE_ESTOQUE_REAL - $value->QUANTIDADE;
                    $newDataProduts = array(
                        'QUANTIDADE_ESTOQUE_REAL' => $novaQtd
                    );
                    $this->_produtos->update($newDataProduts, 'ID = '.$value->ID_PRODUTO);
                }
            }            
            
            //FATURAMENTO
            $dataLancamentoReceber = array (
                    'ID' => NULL,
                    'ID_CLIENTE' => $data['ID_CLIENTE'],
                    'ID_NATUREZA_FINANCEIRA' => 1,
                    'DATA_LANCAMENTO' => date('Y-m-d'),
                    'VALOR_TOTAL' => $data['VALOR_TOTAL']
                );
            $idLancamentoReceber = $this->_lancamentoReceber->insert($dataLancamentoReceber);
            
            for($i = 0; $i <= (sizeof($data['acrescimo'])-1); $i++) {
                echo $data['forma_pagamento'][$i];
                $dataFaturamento = array (
                    'ID_VENDA_ORCAMENTO_CABECALHO' => $idVendaOrcamentoCabecalho,
                    'ID_VENDA_FORMA_PAGAMENTO' => $data['forma_pagamento'][$i],
                    'ID_VENDA_CONDICAO_PAGAMENTO' => $data['condicao_pagamento'][$i],
                    'ACRESCIMO' => $data['acrescimo'][$i],
                    'PARCELAS' => $data['parcelas'][$i],
                    'TOTAL' => $data['total_parcelas'][$i],
                    'DATA' => date('Y-m-d'),
                    'ID_CLIENTE' => $data['ID_CLIENTE']
                );
                $this->_vendaFaturamento->insert($dataFaturamento);
                
                $parcela = explode('x', $data['parcelas'][$i]);
                
                if ($data['condicao_pagamento'][$i] != 1) {
                    $select = $this->_condicaoParcelas->select();
                    $select->from('venda_condicoes_parcelas')->where('ID_VENDA_CONDICOES_PAGAMENTO = ' . $data['condicao_pagamento'][$i]);
                    $rCondicao = $this->_condicaoParcelas->fetchAll($select);
                    $x = count($rCondicao) - 1;

                    for ($i = 0; $i <= count($rCondicao) - 1; $i++) {
                        $dataVencimento = date('Y-m-d', strtotime("+" . $rCondicao[$x]->DIAS . " days"));
                        $dataParcelaPreceber = array(
                            'ID_LANCAMENTO_RECEBER' => $idLancamentoReceber,
                            'ID_CLIENTE' => $data['ID_CLIENTE'],
                            'ID_VENDA_ORCAMENTO_CABECALHO' => $idVendaOrcamentoCabecalho,
                            'ID_STATUS_PARCELA' => 1,
                            'DATA_EMISSAO' => date('Y-m-d'),
                            'DATA_VENCIMENTO' => $dataVencimento,
                            'VALOR' => $parcela[1],
                            'TAXA_JURO' => $data['acrescimo'][$i]
                        );
                        $this->_parcelaReceber->insert($dataParcelaPreceber);
                        $x--;
                    }
                }
                
            }
            session_regenerate_id();

            $this->_redirect('/admin/saidaproduto');
            
        }
    }
    
    public function baixaAction() {
        $this->_helper->viewRenderer->setNoRender(true);
         $this->_helper->layout->disableLayout();
        $novaQtd;
        $carrinho = $this->vendaCarrinho->findAnything('venda_orcamento_carrinho', 'ID_VENDA_ORCAMENTO_CABECALHO', $this->_getParam('id'));
        foreach ($carrinho as $value) {
            $produto = $this->_produtos->find($value->ID_PRODUTO)->current();
            $novaQtd = $produto->QUANTIDADE_ESTOQUE - $value->QUANTIDADE;
            $newDataProduts = array(
                'QUANTIDADE_ESTOQUE' => $novaQtd
            );
            $this->_produtos->update($newDataProduts, 'ID = '.$value->ID_PRODUTO);
        }
        $dataStatus = array (
            'STATUS_PEDIDO' => 1
        );
        $this->_vendasCabecalho->update($dataStatus, 'ID = '.$this->_getParam('id'));
        $this->_redirect('/admin/saidaproduto');
    }
    
//    public function baixarealAction() {
//        $this->_helper->viewRenderer->setNoRender(true);
//        $this->_helper->layout->disableLayout();
//        $novaQtd;
//        $carrinho = new Application_Model_DbTable_Vendacarrinho();
//        $carrinho = $carrinho->fetchAll();
//        foreach ($carrinho as $value) {
//            $produto = $this->_produtos->find($value->ID_PRODUTO)->current();
//            $novaQtd = $produto->QUANTIDADE_ESTOQUE - $value->QUANTIDADE;
//            $newDataProduts = array(
//                'QUANTIDADE_ESTOQUE_REAL' => $novaQtd
//            );
//            $this->_produtos->update($newDataProduts, 'ID = '.$value->ID_PRODUTO);
//        }
//        
//        $this->_redirect('/admin/saidaproduto');
//    }
    
//    public function igualAction() {
//        $this->_helper->viewRenderer->setNoRender(true);
//        $this->_helper->layout->disableLayout();
//        $novaQtd;
//        $prods = new Application_Model_DbTable_Produto();
//        $carrinho = $prods->fetchAll();
//        foreach ($carrinho as $value) {
//            $novaQtd = $value->QUANTIDADE_ESTOQUE;
//            $newDataProduts = array(
//                'QUANTIDADE_ESTOQUE_REAL' => $novaQtd
//            );
//            $this->_produtos->update($newDataProduts, 'ID = '.$value->ID);
//        }
//        
//        $this->_redirect('/admin/saidaproduto');
//    }

    public function carrinhoAction() {
        $this->_helper->layout->disableLayout();
        foreach ($_SESSION['admin'] as $value) {
            if ($this->getRequest()->isPost()) {
                if (isset($_POST['id'])) {
                    $valores = $this->vendaCarrinho->findAnything('venda_orcamento_carrinho', 'ID', $_POST['id']);
                    if ($_POST['coluna'] == 'QUANTIDADE') {
                        if ($valores[0]->DESCONTO > 0) {
                            $total = $valores[0]->VALOR_UNITARIO * $_POST['valor'] * (1 - ($valores[0]->DESCONTO / 100));
                        } else {
                            $total = $valores[0]->VALOR_UNITARIO * $_POST['valor'];
                        }
                        $dataCarrinhoUpdate = array(
                            'QUANTIDADE' => $_POST['valor'],
                            'TOTAL' => $total
                        );
                    } elseif ($_POST['coluna'] == 'VALOR_UNITARIO') {
                        if ($valores[0]->DESCONTO > 0) {
                            $total = $_POST['valor'] * $valores[0]->QUANTIDADE * (1 - ($valores[0]->DESCONTO / 100));
                        } else {
                            $total = $_POST['valor'] * $_POST['valor'];
                        }
                        $dataCarrinhoUpdate = array(
                            'VALOR_UNITARIO' => $_POST['valor'],
                            'TOTAL' => $total
                        );
                    }
                    elseif ($_POST['coluna'] == 'DESCONTO') {
                        $total = $valores[0]->VALOR_UNITARIO * $valores[0]->QUANTIDADE * (1 - ($_POST['valor'] / 100));
                        $dataCarrinhoUpdate = array(
                            'DESCONTO' => $_POST['valor'],
                            'TOTAL' => $total
                        );
                    }
                    $vendas = new Application_Model_DbTable_Vendacarrinho();
                    $vendas->update($dataCarrinhoUpdate, 'ID = ' . (int) $_POST['id']);
                    //$this->vendaCarrinho->update($dataCarrinhoUpdate, 'ID = '. $_POST['id']);
                } else {
                    if (isset($_POST['idProduto'])) {
                        $dataCarrinho = array(
                            'SESSAO' => session_id(),
                            'ID_PRODUTO' => $_POST['idProduto'],
                            'VALOR_UNITARIO' => $_POST['valorUnitario'],
                            'QUANTIDADE' => $_POST['quantidade'],
                            'VALOR_SUBTOTAL' => $_POST['valorUnitario'] * $_POST['quantidade'],
                            'DESCONTO' => $_POST['desconto'],
                            'TOTAL' => $_POST['total'],
                            'ID_USUARIO' => $value->id,
                            'DATA' => date('Y-m-d')
                        );
                        $this->vendaCarrinho->insert($dataCarrinho);
                    }
                }
            }
            $this->view->produtosCarrinho = $this->vendaCarrinho->findCompra();
        }
    }

    public function calculaparcelasAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $parcelas = $this->_condicaoParcelas->findCondicao($this->_getParam('condicao'));
        $totalPar = 0;
        if (count($parcelas) == 1 and $parcelas[0]->DIAS == 0) {
            echo "0 x " . $this->_getParam('totalcompra') . "|" . $this->_getParam('totalcompra');
        }
        if (count($parcelas) >= 1 and $parcelas[0]->DIAS > 0) {
            if ($this->_getParam('totalparcelas') >= 0) {
                $totalPar = $this->_getParam('totalcompra') - $this->_getParam('totalparcelas');
                $j = $this->_getParam('acrescimo');
                (count($parcelas) > 0) ? $n = count($parcelas) : $n = 1;
                $m = $totalPar * (1 + ($j / 100)) ^ $n;
                ($j > 0) ? $parcel = $m / $n : $parcel = $totalPar / count($parcelas);
                ($j == 0) ? $m = $totalPar : '';
                echo count($parcelas) . " x " . $parcel . "|" . $m;
            }
        }
    }
    
    public function imprimirAction() {
        $this->_helper->layout->disableLayout();
        $empresa = new Application_Model_DbTable_Empresa();
        $this->view->empresa = $empresa->fetchAll();
        $this->view->n = $this->_getParam('id');
        $this->view->venda = $this->vendasCabecalho->findAnything('venda_orcamento_cabecalho', 'ID', $this->_getParam('id'));
        $this->view->carrinho = $this->vendaCarrinho->findAnything('venda_orcamento_carrinho', 'ID_VENDA_ORCAMENTO_CABECALHO', $this->_getParam('id'));
        $this->view->faturamento = $this->vendaFaturamento->findAnything('venda_faturamento', 'ID_VENDA_ORCAMENTO_CABECALHO', $this->_getParam('id'));
    }

        public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_vendasCabecalho->delete('ID = ' . $this->_request->getParam('id'));
        $where = array(
            'ID_VENDA_ORCAMENTO_CABECALHO = ?' => $this->_getParam('id')
        );
        $carrinho = new Application_Model_DbTable_Vendacarrinho();
        $car = $carrinho->fetchAll($where);
        foreach ($car as $value) {
            $produto = $this->_produtos->find($value->ID_PRODUTO)->current();
            $novaQtd = $produto->QUANTIDADE_ESTOQUE_REAL + $value->QUANTIDADE;
            $newDataProduts = array(
                'QUANTIDADE_ESTOQUE_REAL' => $novaQtd
            );
            $this->_produtos->update($newDataProduts, 'ID = ' . $value->ID_PRODUTO);
        }
        $this->_vendaCarrinho->delete('ID_VENDA_ORCAMENTO_CABECALHO = ' . (int) $this->_getParam('id'));
        
        $this->_parcelaReceber->delete($where);
        $this->_vendaFaturamento->delete('ID_VENDA_ORCAMENTO_CABECALHO = ' . (int) $this->_getParam('id'));
        $this->_redirect('/admin/saidaproduto');
    }

}
