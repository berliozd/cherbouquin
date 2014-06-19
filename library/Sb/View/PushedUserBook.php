<?php

namespace Sb\View;

/**
 *
 * @author Didier
 */
class PushedUserBook extends \Sb\View\AbstractView {

    private $userBook;
    private $book;
    private $user;
    private $showingConnectedUserBook;

    function __construct($showingConnectedUserBook, \Sb\Db\Model\UserBook $userBook = null) {
        parent::__construct();
        if ($userBook) {
            $this->userBook = $userBook;
            $this->book = $userBook->getBook();
            $this->user = $userBook->getUser();
        }
        $this->showingConnectedUserBook = $showingConnectedUserBook;
    }

    public function get() {

        $tplBook = new \Sb\Templates\Template("pushedUserBooks/pushedUserBook");

        // Prepare variables
        $rating = $this->userBook->getRating();
        $boh = $this->userBook->getIsBlowOfHeart();
        $ratingCss = "rating-" . $rating;
        $viewBookLink = \Sb\Helpers\HTTPHelper::Link($this->book->getLink());

        $img = \Sb\Helpers\BookHelper::getMediumImageTag($this->book, $this->defImg);
        $bookTitle = $this->book->getTitle();
        $bookDescription = mb_substr($this->book->getDescription(), 0, 250, "utf-8") . "...";
        $bookPublication = $this->book->getPublicationInfo();
        if ($this->book->getContributors())
            $bookAuthors = sprintf(__("Auteur(s) : %s", "s1b"), $this->book->getOrderableContributors());
        if ($this->userBook->getReadingState())
            $readingStateLabel = $this->userBook->getReadingState()->getLabel();

        $isOwned = $this->userBook->getIsOwned();
        $isWished = $this->userBook->getIsWished();

        // Set variables
        $tplBook->setVariables(array("rating" => $rating,
            "ratingCss" => $ratingCss,
            "isBlowOfHeart" => $boh,
            "bookTitle" => $bookTitle,
            "bookDescription" => $bookDescription,
            "bookPublication" => $bookPublication,
            "bookAuthors" => $bookAuthors,
            "viewBookLink" => $viewBookLink,
            "image" => $img,
            "readingStateLabel" => $readingStateLabel,
            "isOwned" => $isOwned,
            "isWished" => $isWished,
            "showingConnectedUserBook" => $this->showingConnectedUserBook));

        return $tplBook->output();
    }

}