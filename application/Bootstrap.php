<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initRouter() {

        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        $router = $front->getRouter();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
        $routing = new Zend_Controller_Router_Rewrite();
        $routing->addConfig($config, 'routes');
        $front->setRouter($routing);
    }

    protected function _initZFDebug() {
        // Setup autoloader with namespace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');

        // Ensure the front controller is initialized
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');

        // Only enable zfdebug if options have been specified for it
        if ($this->hasOption('zfdebug')) {
            // Create ZFDebug instance
            $zfdebug = new
                    ZFDebug_Controller_Plugin_Debug($this->getOption('zfdebug'));

            // Register ZFDebug with the front controller
            $front->registerPlugin($zfdebug);
        }
    }

}