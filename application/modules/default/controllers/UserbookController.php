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

        if ($this->getContext()->getConnectedUser()) {
            $config = $this->getConfig();
            $return = \Sb\Db\Service\UserBookSvc::getInstance()->addFromPost($this->getContext()->getConnectedUser(), $config);
            $this->view->message = $return;
        } else
            $this->view->message = __("Vous devez être connecté pour ajouter un livre.","s1b");
    }

}

