<?php

class Default_StaticController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {

        $file = $this->_getParam('file');
        $this->view->staticFile = $file;
        
    }

}

