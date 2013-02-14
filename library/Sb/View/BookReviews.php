<?php

namespace Sb\View;

/**
 * Description of \Sb\View\BookReviews
 *
 * @author Didier
 */
class BookReviews extends \Sb\View\AbstractView {

    private $paginatedList;
    private $bookId;

    function __construct($paginatedList, $bookId, $pageNumber = 1) {
        parent::__construct();
        $this->paginatedList = $paginatedList;
        $this->bookId = $bookId;
        $this->pageNumber = $pageNumber;
    }

    public function get() {

        $baseTpl = "book/bookReviews/reviews";
        $tplReviews = new \Sb\Templates\Template($baseTpl);
        $connectedUser = $this->getContext()->getConnectedUser();
        $tplReviews->setVariables(array("bookId" => $this->bookId,
            "userBooks" => $this->paginatedList->getItems(),
            "connectedUser" => $connectedUser,
            "navigation" => $this->paginatedList->getNavigationBar(),
            "firstItemIdx" => $this->paginatedList->getFirstPage(),
            "lastItemIdx" => $this->paginatedList->getLastPage(),
            "nbItemsTot" => $this->paginatedList->getTotalPages(),
            "pageNumber" => $this->pageNumber));
        return $tplReviews->output();
    }
}