<?php

use Sb\Db\Service\TagSvc;
use Sb\Db\Service\BookSvc;
use Sb\Service\HeaderInformationSvc;

class Default_BooksController extends Zend_Controller_Action {

    private $selectedTagId;

    public function init() {
        /* Initialize action controller here */
    }

    /**
     * Action for showing a list of last added books
     */
    public function lastAddedAction() {

        // Get all books
        $books = BookSvc::getInstance()->getLastlyAddedForPage();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        // Get tag id
        $tid = $this->_getParam('tid', -1);
        $tagLabel = null;
        if ($tid > 0) {
            $books = $this->filterBooks($books, $tid);
            $tagLabel = $this->getTagLabel($tid);
        }
        $this->setPageList($books);

        $description = __("Cette sélection des derniers livres ajoutés par les membres sur Cherbouquin vous donnera sûrement des idées de lecture auquel vous n'aviez pas pensées.", "s1b");
        $title = __("Derniers livres ajoutés", "s1b");
        $this->view->title = $title;
        $this->view->description = $description;

        // Get Header information (title, meta desc, keywords)
        $headerInformation = HeaderInformationSvc::getInstance()->getForLastAddedPage($this->getPageNumber(), $tagLabel);
        $this->view->metaDescription = $headerInformation->getDescription();
        $this->view->tagTitle = $headerInformation->getTitle();
        $this->view->metaKeywords = $headerInformation->getKeywords();

        $this->view->action = $this->view->url(array(), 'lastAddedBooks');

        $this->render("list");
    }

    /**
     * Action for showing a list of blow of heart books
     */
    public function blowOfHeartsAction() {

        // Get all books
        $books = BookSvc::getInstance()->getBOHPageBOH();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        // Get tag id
        $tid = $this->_getParam('tid', -1);
        $tagLabel = null;
        if ($tid > 0) {
            $books = $this->filterBooks($books, $tid);
            $tagLabel = $this->getTagLabel($tid);
        }
        $this->setPageList($books);

        $description = __("Cette sélection des coups de coeur est le résultat d'un classement effectué sur tous les livres présents chez Cherbouquin sur la base des coups de coeur que vous et les autres membres avez attribués. L'idée de ce top est que vous puissiez y trouver l'inspiration pour vos prochaines lectures.", "s1b");
        $title = __("Coups de coeurs", "s1b");
        $this->view->title = $title;
        $this->view->description = $description;

        // Get Header information (title, meta desc, keywords)
        $headerInformation = HeaderInformationSvc::getInstance()->getForBohPage($this->getPageNumber(), $tagLabel);
        $this->view->metaDescription = $headerInformation->getDescription();
        $this->view->tagTitle = $headerInformation->getTitle();
        $this->view->metaKeywords = $headerInformation->getKeywords();

        $this->view->action = $this->view->url(array(), 'blowOfHeartsBooks');

        $this->render("list");
    }

    /**
     * Action for showing a list of top books
     */
    public function topsAction() {

        // Get all books
        $books = BookSvc::getInstance()->getTopsPageTops();

        // Get tags for combo
        $this->view->tags = TagSvc::getInstance()->getTagsForBooks($books);

        // Get tag id
        $tid = $this->_getParam('tid', -1);
        $tagLabel = null;
        if ($tid > 0) {
            $books = $this->filterBooks($books, $tid);
            $tagLabel = $this->getTagLabel($tid);
        }

        $this->setPageList($books);

        $description = __("Cette sélection des meilleurs livres est le résultat d'un classement effectué sur tous les livres présents chez Cherbouquin sur la base de la note que vous et les autres membres avez attribuée. L'idée de ce top est que vous puissiez y trouver l'inspiration pour vos prochaines lectures.", "s1b");
        $title = __("Tops des livres", "s1b");
        $this->view->title = $title;
        $this->view->description = $description;

        // Get Header information (title, meta desc, keywords)
        $headerInformation = HeaderInformationSvc::getInstance()->getForTopsPage($this->getPageNumber(), $tagLabel);
        $this->view->metaDescription = $headerInformation->getDescription();
        $this->view->tagTitle = $headerInformation->getTitle();
        $this->view->metaKeywords = $headerInformation->getKeywords();

        $this->view->action = $this->view->url(array(), 'topsBooks');

        $this->render("list");
    }

    private function filterBooks($books, $tid) {
        $this->view->selectedTagId = $tid;
        $this->selectedTagId = $tid;
        $result = array_filter($books, array(&$this, "bookHasSelectedTag"));
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

    /**
     * Get current page number
     * @return int get current page number
     */
    private function getPageNumber() {
        return $this->_getParam("pagenumber", 1);
    }

    private function getTagLabel($tagId) {
        $tag = Sb\Db\Dao\TagDao::getInstance()->get($tagId);
        return $tag->getLabel();
    }

}