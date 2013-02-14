<?php

class Default_UserbookController extends Zend_Controller_Action {

    private function getConfig() {
        global $globalConfig;
        return $globalConfig;
    }

    private function getContext() {
        global $globalContext;
        return $globalContext;
    }

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add', 'html')
                ->initContext();
    }

    public function indexAction() {
        // action body
    }

    public function addAction() {
        $config = $this->getConfig();
        $return = \Sb\Db\Service\UserBookSvc::getInstance()->addFromPost($this->getContext()->getConnectedUser(), $config);
        $this->view->message = $return;
    }

}

