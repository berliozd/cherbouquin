<?php

use \Sb\Db\Dao\BookDao;
use \Sb\Db\Service\TagSvc;
use \Sb\Db\Service\BookSvc;

class BooksController extends Zend_Controller_Action {

    private $selectedTagId;

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function lastAddedAction() {

        // Get all books
        $books = BookSvc::getInstance()->getLastlyAddedForPage();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        $books = $this->filterBooks($books);        
        $this->setPageList($books);        
        
    }

    public function blowOfHeartsAction() {
        
        // Get all books
        $books = BookSvc::getInstance()->getBOHPageBOH();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        $books = $this->filterBooks($books);        
        $this->setPageList($books);
        
    }

    /**
     * Action for showing a list of top books
     */
    public function topsAction() {
       
        // Get all books
        $books = BookSvc::getInstance()->getTopsPageTops();
        
        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        $books = $this->filterBooks($books);
        $this->setPageList($books);
    }

    private function filterBooks($books) {
        $result = $books;
        $tid = $this->_getParam('tid', -1);
        if ($tid > 0) {
            $this->view->selectedTagId = $tid;
            $this->selectedTagId = $tid;
            $result = array_filter($books, array(&$this, "bookHasSelectedTag"));
        }
        return $result;
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

    private function bookHasSelectedTag(\Sb\Db\Model\Book $book) {
        $bookTags = TagSvc::getInstance()->getTagsForBooks(array($book));
        
        foreach ($bookTags as $tag) {
            if ($tag->getId() == $this->selectedTagId)
                    return true;            
        }
        return false;
    }
}