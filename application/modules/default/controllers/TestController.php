<?php

use Sb\Db\Dao\UserDao;

class Default_TestController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('ajax', 'html')->initContext();
    }

    public function indexAction() {

//        $user20 = UserDao::getInstance()->get(20);
//        \Sb\Trace\Trace::addItem($user20->getUserName());
//        Doctrine\Common\Util\Debug::dump($user20);
//
//        $user3 = UserDao::getInstance()->get(3);
//        Doctrine\Common\Util\Debug::dump($user3);
        
    }

    public function getId(Sb\Db\Model\Model $model) {
        return $model->getId();
    }

    public function ajaxAction() {
        // action body
    }

    public function ajax2Action() {
        // action body
    }

}

