<?php

class TestController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('ajax', 'html')->initContext();
    }

    public function indexAction() {

//        $booksLikedByUsers = \Sb\Db\Dao\BookDao::getInstance()->getListLikedByUsers(array(30, 16));
//        \Doctrine\Common\Util\Debug::dump($booksLikedByUsers);
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

