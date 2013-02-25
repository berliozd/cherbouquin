<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initRouter() {

        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        //$router = $front->getRouter();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
        $routing = new Zend_Controller_Router_Rewrite();
        $routing->addConfig($config, 'routes');
        $front->setRouter($routing);
    }

    protected function _initFrontControllerOutput() {

        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        $response = new Zend_Controller_Response_Http;
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
        $front->setResponse($response);
    }
}