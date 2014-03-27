<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //$form = new SON_Forms_Login();
        
        if($this->_request->isPost()){
            $data = $this->_request->getPost();
            if($data['email'] != '' and $data['senha'] != '') {


                $authAdapter = $this->getAuthAdapter();
                $authAdapter->setIdentity($data['email'])
                        ->setCredential($data['senha']);
                        
                $result = $authAdapter->authenticate();

                if($result->isValid()) {
                    $auth = Zend_Auth::getInstance ();
//                    if($authAdapter->getResultRowObject()->nivel == "administrador") {
//                        $auth->setStorage(new Zend_Auth_Storage_Session('admin'));
//                    } else {
//                        $auth->setStorage(new Zend_Auth_Storage_Session('orcamento'));
//                    }
                    $auth->setStorage(new Zend_Auth_Storage_Session('admin'));
                    $dataAuth = $authAdapter->getResultRowObject(null, 'senha');
                    $auth->getStorage()->write($dataAuth);
                    $auth->setStorage(new Zend_Auth_Storage_Session('orcamento'));
                    $dataAuth = $authAdapter->getResultRowObject(null, 'senha');
                    $auth->getStorage()->write($dataAuth);
                    $this->_redirect("/admin");
                } else {
                    $this->view->error = "Usuário ou senha inválidos";
                }
            } else {
                echo "<script>alert('Informe seus dados.');</script>";
            }
        }
    }
    
    private function getAuthAdapter() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $resource = $bootstrap->getPluginResource('db');
        $db = $resource->getDbAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('usuarios')->setIdentityColumn('email')
                ->setCredentialColumn('senha')
                ->setCredentialTreatment('SHA1(?) AND status = 1');
        return $authAdapter;
    }

    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('admin'));
        $auth->clearIdentity();
        session_destroy();
        session_regenerate_id();
        $this->_redirect("/");
    }


}

