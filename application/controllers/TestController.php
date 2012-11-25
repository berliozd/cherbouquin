<?php

class TestController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        
        $booksLikedByUsers = \Sb\Db\Dao\BookDao::getInstance()->getListLikedByUsers(array(30, 16));
        \Doctrine\Common\Util\Debug::dump($booksLikedByUsers);
        
    }

    function getId(\Sb\Db\Model\Model $model) {
        return $model->getId();
    }

}

