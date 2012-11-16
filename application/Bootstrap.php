<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initRouter() {
        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        $router = $front->getRouter();

        $route = new Zend_Controller_Router_Route('ztest', array('controller' => 'index', 'action' => 'index'));
        $router->addRoute('test_de_route', $route);
    }

}