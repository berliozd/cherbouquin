<?php

use \Sb\Db\Dao\BookDao;

class BooksController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function lastAddedAction() {
        $books = BookDao::getInstance()->getLastlyAddedBooks(25);
        $this->setPageList($books);
    }

    public function blowOfHeartsAction() {
        $books = BookDao::getInstance()->getListBOH(25);
        $this->setPageList($books);
    }

    public function topsAction() {
        $books = BookDao::getInstance()->getListTops(25);
        $this->setPageList($books);
    }

    private function setPageList($books) {
        if ($books && count($books) > 0) {
            $paginatedList = new \Sb\Lists\PaginatedList($books, 10);
            $this->view->firstItemIdx = $paginatedList->getFirstPage();
            $this->view->lastItemIdx = $paginatedList->getLastPage();
            $this->view->nbItemsTot = $paginatedList->getTotalPages();
            $this->view->navigation = $paginatedList->getNavigationBar();
            $books = $paginatedList->getItems();
            $this->view->books = $books;
        }
    }

}

