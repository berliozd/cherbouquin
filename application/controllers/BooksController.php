<?php

use \Sb\Db\Dao\BookDao;
use \Sb\Db\Service\TagSvc;

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

    /**
     * Action for showing a list of top books
     */
    public function topsAction() {

        var_dump($this->getViewScript());
        
        // Get ll books to show
        $books = BookDao::getInstance()->getListTops(25);
        
        // Get all tags for all books
        $tags = TagSvc::getInstance()->getTagsForBooks($books);
        
        $this->setPageList($books);
//        
//        
//        $view = new Zend_View();
//        $view->setBasePath($path)
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

