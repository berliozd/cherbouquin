<?php

namespace Sb\View;

/**
 * Description of \Sb\View\BookReviews
 *
 * @author Didier
 */
class BookReviews extends \Sb\View\AbstractView {

    private $userBooks;
    private $bookId;

    function __construct($userBooks = null, $bookId) {
        parent::__construct();
        $this->userBooks = $userBooks;
        $this->bookId = $bookId;
    }

    public function get() {

        if ($this->userBooks) {
            $baseTpl = "book/bookReviews/reviews";
            $tplReviews = new \Sb\Templates\Template($baseTpl);
            $reviewedUserBooks = array_filter($this->userBooks, array(&$this, "isReviewd"));


            if ($reviewedUserBooks && count($reviewedUserBooks) > 0) {
                // preparing pagination for 5 reviews per page
                $paginatedList = new \Sb\Lists\PaginatedList($reviewedUserBooks, 5);
                $firstItemIdx = $paginatedList->getFirstPage();
                $lastItemIdx = $paginatedList->getLastPage();
                $nbItemsTot = $paginatedList->getTotalPages();
                $navigation = $paginatedList->getNavigationBar();
                $reviewedUserBooks = $paginatedList->getItems();
            }

            $connectedUser = $this->getContext()->getConnectedUser();
            $tplReviews->setVariables(array("bookId" => $this->bookId,
                "userBooks" => $reviewedUserBooks,
                "connectedUser" => $connectedUser,
                "navigation" => $navigation,
                "firstItemIdx" => $firstItemIdx,
                "lastItemIdx" => $lastItemIdx,
                "nbItemsTot" => $nbItemsTot));
            return $tplReviews->output();
        }
        return "";
    }

    private function isReviewd(\Sb\Db\Model\UserBook $userBook) {
        if ($userBook->getReview()) {
            return true;
        }
    }

}