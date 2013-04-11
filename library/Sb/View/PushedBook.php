<?php

namespace Sb\View;

/**
 *
 * @author Didier
 */
class PushedBook extends \Sb\View\AbstractView {

    private $book;
    private $boh;

    function __construct(\Sb\Db\Model\Book $book = null, $isBoh) {
        parent::__construct();
        $this->book = $book;
        $this->boh = $isBoh;
    }

    public function get() {

        $tplBook = new \Sb\Templates\Template("pushedBooks/pushedBook");

        // préparation des champs pour le template
        // Prepare variables
        $avgRating = $this->book->getAverageRating();

        $roundedRating = floor($avgRating);
        $ratingCss = "rating-" . $roundedRating;
        //$viewBookLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::BOOK_VIEW, "bid" => $this->book->getId()));
        $viewBookLink = \Sb\Helpers\HTTPHelper::Link($this->book->getLink());

        $img = \Sb\Helpers\BookHelper::getMediumImageTag($this->book, $this->defImg);
        $bookTitle = $this->book->getTitle();
        $bookDescription = \Sb\Helpers\StringHelper::tronque($this->book->getDescription(), 250);
        $bookPublication = $this->book->getPublicationInfo();
        
        $bookAuthors = "";
        if ($this->book->getContributors())
            $bookAuthors = sprintf("Auteur(s) : %s", $this->book->getOrderableContributors());
            
        $nbRatings = $this->book->getNbRatedUserBooks();
        $nbBlowOfHearts = $this->book->getNbOfBlowOfHearts();

        // Set variables
        $tplBook->setVariables(array("averageRating" => round($avgRating,2),
            "ratingCss" => $ratingCss,
            "isBlowOfHeart" => $this->boh,
            "nbBlowOfHearts" => $nbBlowOfHearts,
            "roundedRating" => $roundedRating,
            "bookTitle" => $bookTitle,
            "bookDescription" => $bookDescription,
            "bookPublication" => $bookPublication,
            "bookAuthors" => $bookAuthors,
            "viewBookLink" => $viewBookLink,
            "image" => $img,
            "nbRatings" => $nbRatings));


        return $tplBook->output();
    }

}