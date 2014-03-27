<?php

class Admin_EntradaprodutoController extends Zend_Controller_Action {

    protected $_produtos;
    protected $produtos;
    protected $nfDetalhe;
    protected $nfCabecalho;
    protected $fornecedorProduto;

    public function init() {
        $this->_produtos = new Application_Model_Produto();
        $this->produtos = new Application_Model_DbTable_Produto();
        $this->nfDetalhe = new Application_Model_DbTable_NFedetalhe();
        $this->nfCabecalho = new Application_Model_DbTable_NFecabecalho();
        $this->fornecedorProduto = new Application_Model_DbTable_Fornecedorproduto();
    }

    public function indexAction() {
        $select = $this->nfCabecalho->select();
        $select->from('nfe_cabecalho')->order('DATA_EMISSAO DESC');
        $this->view->nfeCabecalho = $this->nfCabecalho->fetchAll($select);
        echo $_SERVER['HTTP_HOST'];
    }

    public function newAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if ($_FILES['xml'] != "") {
                $imageAdapter = new Zend_File_Transfer_Adapter_Http();
                $imageAdapter->setDestination('xml_nfe_entrada');
                if (is_uploaded_file($_FILES['xml']['tmp_name'])) {
                    if (!$imageAdapter->receive('xml')) {
                        $messages = $imageAdapter->getMessages['url'];
                    } else {
                        $filename = $imageAdapter->getFileName('xml');
                        unset($data['submit']);
                        unset($data['MAX_FILE_SIZE']);
                        $data['xml'] = $_FILES["xml"]["name"];
                        $this->view->arquivo = $data['xml'];
                        $xml = simplexml_load_file('http://' . $_SERVER['HTTP_HOST'] . '/xml_nfe_entrada/' . $data['xml']);
                        $this->view->chaveacesso = $xml->protNFe->infProt->chNFe;
                        foreach ($xml->NFe as $key => $item) :
                            foreach ($item->infNFe as $val) {
                                $this->view->dataEmissao = $val->ide->dEmi;
                                $this->view->dataSaidaEntrada = $val->ide->dSaiEnt;
                                switch ($val->ide->tpNF) {
                                    case 1:
                                        $this->view->tipoNF = "Saída";
                                        break;
                                    case 0:
                                        $this->view->tipoNF = "Entrada";
                                        break;
                                }
                                switch ($val->ide->indPag) {
                                    case 0:
                                        $this->view->indPag = "Pagamento à vista";
                                        break;
                                    case 1:
                                        $this->view->indPag = "Pagamento à prazo";
                                        break;
                                    case 2:
                                        $this->view->indPag = "Outros";
                                        break;
                                }
                                $this->view->natOP = $val->ide->natOp;

                                $this->view->cnpjEmit = $val->emit->CNPJ;
                                $this->view->xNome = $val->emit->xNome;
                                $this->view->xMun = $val->emit->enderEmit->xMun;

                                $this->view->produtos = $val->det;

                                $this->view->cobranca = $val->cobr->dup;
                                //var_dump($val->cobr->dup);
                                //item->infNFe->det
                            }
                        endforeach;
                    }
                } else {
                    //O Arquivo NÃ£o Foi Enviado Corretamente
                }
            }
        }
    }

    public function entraAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            $data['xml'] = $data['arquivo'];
            $xml = simplexml_load_file('http://' . $_SERVER['HTTP_HOST'] . '/xml_nfe_entrada/' . $data['xml']);
            foreach ($xml->NFe as $key => $item) :
                foreach ($item->infNFe as $val) {
                    $codigoNumerico = $val->ide->cNF;
                    $naturezaOperacao = $val->ide->natOp;
                    $indPag = $val->ide->indPag;
                    $modelo = $val->ide->mod;
                    $serie = $val->ide->mod;
                    $numero = $val->ide->nNF;
                    $dataEmissao = $val->ide->dEmi;
                    $dataEntradaSaida = $val->ide->dSaiEnt;
                    $tipoNF = $val->ide->tpNF;
                    $cdMun = $val->ide->cMunFG;
                    $tpEmis = $val->ide->tpEmis;
                    $finalidade = $val->ide->finNFe;
                    $baseCalculoICMS = $val->total->ICMSTot->vBC;
                    $valorICMS = $val->total->ICMSTot->vICMS;
                    $baseCalculoICMSST = $val->total->ICMSTot->vBCST;
                    $valorST = $val->total->ICMSTot->vICMS;
                    $totalProdutos = $val->total->ICMSTot->vProd;
                    $valorFrete = $val->total->ICMSTot->vFrete;
                    $valorSeguro = $val->total->ICMSTot->vSeg;
                    $valorDesconto = $val->total->ICMSTot->vDesc;
                    $despesasAcessorias = $val->total->ICMSTot->vOutro;
                    $valorTotalNF = $val->total->ICMSTot->vNF;
                    $cobranca = $val->cobr->dup;
                    $fatura = $val->cobr->fat->vOrig;
                    //SE NÃO EXISTIR O FORNECEDOR
                    if ($data['fornecedor'] == 0) {
                        //PESSOA
                        $dataPessoa = array(
                            'ID' => null,
                            'ID_EMPRESA' => 1,
                            'NOME' => $val->emit->xNome,
                            'TIPO' => 'J',
                            'CLASSE' => 2
                        );
                        $pessoa = new Application_Model_DbTable_Pessoa();
                        $idPessoa = $pessoa->insert($dataPessoa);

                        //PESSOA JURÍDICA
                        $dataJuridica = array(
                            'ID_PESSOA' => $idPessoa,
                            'CNPJ' => $val->emit->CNPJ,
                            'RAZAO_SOCIAL' => $val->emit->xNome
                        );
                        $fornecedorJur = new Application_Model_DbTable_Pessoajuridica();
                        $fornecedorJur->insert($dataJuridica);

                        //FORNECEDOR
                        $dataFornecedor = array(
                            'ID_PESSOA' => $idPessoa,
                            'ID_ATIVIDADE_FOR_CLI' => 1,
                            'ID_SITUACAO_FOR_CLI' => 1,
                            'DATA_CADASTRO' => date('Y-m-d')
                        );
                        $fornecedor = new Application_Model_DbTable_Fornecedor();
                        $idFornecedor = $fornecedor->insert($dataFornecedor);

                        //ENDERECO
                        $dataEntereco = array(
                            'ID_EMPRESA' => 1,
                            'ID_PESSOA' => $idPessoa,
                            'LOGRADOURO' => $val->emit->enderEmit->xLgr,
                            'NUMERO' => $val->emit->enderEmit->nro,
                            'BAIRRO' => $val->emit->enderEmit->xBairro,
                            'CEP' => $val->emit->enderEmit->CEP,
                            'ID_MUNICIPIO' => $val->emit->enderEmit->cMun,
                            'UF' => $municipio['0']->ID_UF,
                            'FONE' => $val->emit->enderEmit->fone
                        );
                        $endereco = new Application_Model_DbTable_Endereco();
                        $endereco->insert($dataEntereco);
                    }
                    $produtos_xml = $val->det;
                    $duplicatas = $val->cobr->dup;
                }
            endforeach;
            if ($data['fornecedor'] == 0) {
                $fornecedorId = $idPessoa;
            } else {
                $fornecedorId = $data['fornecedor'];
            }

            $dataNFeCabacalho = array(
                'ID' => null,
                'ID_EMPRESA' => 1,
                'ID_FORNECEDOR' => $fornecedorId,
                'CODIGO_NUMERICO' => $codigoNumerico,
                'NATUREZA_OPERACAO' => $naturezaOperacao,
                'INDICADOR_FORMA_PAGAMENTO' => $indPag,
                'CODIGO_MODELO' => $modelo,
                'SERIE' => $serie,
                'NUMERO' => $numero,
                'DATA_EMISSAO' => $dataEmissao,
                'DATA_ENTRADA_SAIDA' => $dataEntradaSaida,
                'TIPO_OPERACAO' => $tipoNF,
                'CODIGO_MUNICIPIO' => $cdMun,
                'TIPO_EMISSAO' => $tpEmis,
                'CHAVE_ACESSO' => $xml->protNFe->infProt->chNFe,
                'FINALIDADE_EMISSAO' => $finalidade,
                'BASE_CALCULO_ICMS' => $baseCalculoICMS,
                'VALOR_ICMS' => $valorICMS,
                'BASE_CALCULO_ICMS_ST' => $baseCalculoICMSST,
                'VALOR_ICMS_ST' => $valorST,
                'VALOR_TOTAL_PRODUTOS' => $totalProdutos,
                'VALOR_FRETE' => $valorFrete,
                'VALOR_SEGURO' => $valorSeguro,
                'VALOR_DESCONTO' => $valorDesconto,
                'VALOR_DESPESAS_ACESSORIAS' => $despesasAcessorias, 
                'VALOR_TOTAL' => $valorTotalNF
            );
            $idNfeCabecalho = $this->nfCabecalho->insert($dataNFeCabacalho);


            //PRODUTOS
            //VERIFICA ATRAVÉS DO EAN PRODUTOS DO XML QUE TEM NO BANCO
            $jaFoi = 0;
            $prodts = array();
            foreach ($produtos_xml as $value) {
                ($value->prod->cEAN == "") ? $value->prod->cEAN = 'NC' : $value->prod->cEAN = $value->prod->cEAN;
                $produto = $this->_produtos->findAnything('produto', 'CODIGO_BARRAS', $value->prod->cEAN);
                if (isset($produto[0])) {
                    //atualiza produtos
                    ($value->prod->cEAN == 'NC') ? $ean = '' : $ean = $value->prod->cEAN; // Para não inserir 'NC' no banco de dados
                    $this->trataProdutos($produto['0']->ID, $value->prod->qCom, $value->prod->vUnCom, $fornecedorId, $ean, 0, 0, 0, 0, $prodts);
                    //insere na tabela nfe_detalhe
                    $dataNFeDetalhe = array(
                        'ID_PRODUTO' => $produto['0']->ID,
                        'ID_NFE_CABECALHO' => $idNfeCabecalho,
                        'NOME_PRODUTO' => $value->prod->xProd,
                        'NCM' => $value->prod->NCM,
                        'CFOP' => $value->prod->CFOP,
                        'UNIDADE_COMERCIAL' => $value->prod->uCom,
                        'QUANTIDADE_COMERCIAL' => $value->prod->qCom,
                        'VALOR_UNITARIO_COMERCIAL' => $value->prod->vUnCom
                    );
                    $this->nfDetalhe->insert($dataNFeDetalhe);
                    
                    //faz o relacionamento fornecedor_produto
                    $res = $this->fornecedorProduto->findFornProd($fornecedorId, $produto['0']->ID);
                    if(!isset($res[0])) {
                        $dataFornecedorProduto = array (
                            'ID_FORNECEDOR' => $fornecedorId,
                            'ID_PRODUTO' => $produto['0']->ID
                        );
                        $this->fornecedorProduto->insert($dataFornecedorProduto);
                    }
                } else {

                }
            }
            //VERIFICA OS ITENS DO SELECT
            $jaFoi = 0;
            foreach ($data['PRODUTOS'] as $v) {
                $partes = explode("-", $v);
                $partes[4] = str_replace("-", " ", $partes[4]); // se no nome do produto tiver '-' trocar por espaço em branco 
                //se id do produto > 0, chama o trataProdutos e atualiza
                if ($partes[1] > 0) {
                    $this->trataProdutos($partes[1], $partes[2], $partes[3], $fornecedorId, 0, 0, 0, 0, 0, $prodts);
                    $dataNFeDetalhe = array(
                        'ID_PRODUTO' => $partes[1],
                        'ID_NFE_CABECALHO' => $idNfeCabecalho,
                        'NOME_PRODUTO' => $partes[4],
                        'NCM' => $partes[5],
                        'CFOP' => $partes[6],
                        'UNIDADE_COMERCIAL' => $partes[7],
                        'QUANTIDADE_COMERCIAL' => $partes[2],
                        'VALOR_UNITARIO_COMERCIAL' => $partes[3]
                    );
                    $this->nfDetalhe->insert($dataNFeDetalhe);
                    $res = $this->fornecedorProduto->findFornProd($fornecedorId, $partes[1]);
                    if(!isset($res[0])) {
                        $dataFornecedorProduto = array(
                            'ID_FORNECEDOR' => $fornecedorId,
                            'ID_PRODUTO' => $partes[1]
                        );
                        $this->fornecedorProduto->insert($dataFornecedorProduto);
                    }
                } 
                else {
                    $this->trataProdutos(0, $partes[2], $partes[3], $fornecedorId, $partes[0], $partes[5], $partes[4], $partes[6], $idNfeCabecalho, $prodts);
                    
                }
            }

            //CONTAS A PAGAR
            $dataLancamentoPagar = array(
                'ID_FORNECEDOR' => $fornecedorId,
                'ID_NATUREZA_FINANCEIRA' => 4,
                'ID_DOCUMENTO_ORIGEM' => 1,
                'VALOR_TOTAL' => $fatura,
                'VALOR_A_PAGAR' => $fatura,
                'DATA_LANCAMENTO' => date('Y-m-d')
            );
            $lancamentoPagar = new Application_Model_DbTable_Lancamentopagar();
            $idLancamentoPagar = $lancamentoPagar->insert($dataLancamentoPagar);

            foreach ($cobranca as $value) {
                $parcela_pagar = array (
                    'ID_STATUS_PARCELA_PAGAR' => 1,
                    'ID_LANCAMENTO_PAGAR' => $idLancamentoPagar,
                    'DATA_EMISSAO' => $dataEmissao,
                    'DATA_VENCIMENTO' => $value->dVenc,
                    'VALOR' => $value->vDup
                );
                $parcelaPagar = new Application_Model_DbTable_Parcelapagar();
                $parcelaPagar->insert($parcela_pagar);
            }

            $this->_redirect('/admin/entradaproduto');
        }
    }

    private function trataProdutos($id, $qtd, $valor, $fornecedor, $ean, $ncm, $nome, $cfop, $idNfeCabecalho, array $produtos) {
        ($ean == 'NC') ? $ean = '' : $ean = $ean;
        if ($id > 0) {
            $produto = $this->produtos->find($id)->current();
            $novaQtd = $produto->QUANTIDADE_ESTOQUE + $qtd;
            $novaQtdR = $produto->QUANTIDADE_ESTOQUE_REAL + $qtd;
            $newDataProduts = array(
                'CODIGO_BARRAS' => $ean,
                'QUANTIDADE_ESTOQUE' => $novaQtd,
                'QUANTIDADE_ESTOQUE_REAL' => $novaQtdR,
                'VALOR_COMPRA' => $valor
            );
            $this->produtos->update($newDataProduts, 'ID = ' . $id);
            //add fornecedor
            $res = $this->fornecedorProduto->findFornProd($fornecedor, $id);
            if (!isset($res[0])) {
                $dataFornecedorProduto = array(
                    'ID_FORNECEDOR' => $fornecedor,
                    'ID_PRODUTO' => $id
                );
                $this->fornecedorProduto->insert($dataFornecedorProduto);
            }
        } else {           
            $dataProd = array(
                        'ID' => null,
                        'CODIGO_BARRAS' => $ean,
                        'NCM' => $ncm,
                        'NOME' => $nome,
                        'VALOR_COMPRA' => $valor,
                        'VALOR_VENDA' => $valor * 1.3,
                        'QUANTIDADE_ESTOQUE' => $qtd,
                        'QUANTIDADE_ESTOQUE_REAL' => $qtd,
                        'DATA_CADASTRO' => date('Y-m-d')
                    );
                    $idProduto = $this->produtos->insert($dataProd);
                    $res = $this->fornecedorProduto->findFornProd($fornecedor, $idProduto);
                    if (!isset($res[0])) {
                        $dataFornecedorProduto = array(
                            'ID_FORNECEDOR' => $fornecedor,
                            'ID_PRODUTO' => $idProduto
                        );
                        $this->fornecedorProduto->insert($dataFornecedorProduto);
                    }
                    $dataNFeDetalhe = array(
                        'ID_PRODUTO' => $idProduto,
                        'ID_NFE_CABECALHO' => $idNfeCabecalho,
                        'NOME_PRODUTO' => $nome,
                        'NCM' => $ncm,
                        'CFOP' => $cfop,
                        'QUANTIDADE_COMERCIAL' => $qtd,
                        'VALOR_UNITARIO_COMERCIAL' => $valor
                    );
                    $this->nfDetalhe->insert($dataNFeDetalhe);
            
        }
    }

    public function editAction() {

        $rgrupo = $this->_grupos->find($this->_request->getParam('id'))->current();
        $this->view->nome = $rgrupo->NOME;

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->getPost();
            if ($data['NOME'] != "") {
                unset($data['submit']);
                $this->_grupos->update($data, 'ID = ' . (int) $this->_getParam('id'));
                $this->_redirect('/admin/grupoproduto');
            }
        }
    }

    public function deletarAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $rgrupo = $this->_grupos->find($this->_request->getParam('id'))->current();
        $rgrupo->delete();
        $this->_redirect('/admin/grupoproduto');
    }

}

