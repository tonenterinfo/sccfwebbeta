<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
 protected function _initAutoLoader() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('SCCF');
        return $autoloader;
    }

    protected function _initViews() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_TRANSITIONAL');
        $view->addHelperPath ('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper' );
        $view->headTitle()->setSeparator(' - ')->headTitle('Sccf');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        Zend_Registry::set('view',$view);
    }

    protected function _initPlugins(){
        $bootstrap = $this->getApplication();
        if($bootstrap instanceof Zend_Application) {
            $bootstrap = $this;
        }

        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
        $front->registerPlugin(new SCCF_Plugins_Layout());
        $front->registerPlugin(new SCCF_Plugins_CheckAuth());

    }
    
    protected function _initZFDebug() {
        $zfdebugConfig = $this->getOption('zfdebug');
        if ($zfdebugConfig['enabled'] != 1) {
          return;
        }
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');
        $this->bootstrap('db');
        $db = $this->getPluginResource('db')->getDbAdapter();
        $options = array(
            'plugins' => array('Variables',
                'Database' => array('adapter' => array('standard' => $db)),
                'File' => array('basePath' => '/'),
                'Memory',
                'Time',
                'Registry',
                'Exception')
        );
        $debug = new ZFDebug_Controller_Plugin_Debug($options);
        $this->bootstrap('frontController');
        $frontController = $this->getResource('frontController');
        $frontController->registerPlugin($debug);
    }

}

