<?php

namespace Sb\View;

use Sb\Db\Service\BookSvc;
use Sb\View\BookShelf;

class Book extends \Sb\View\AbstractView {

    private $book;
    private $addButtons;
    private $addReviews;
    private $addHiddenFields;
    private $isInForm;
    private $addRecommendations;

    function __construct(\Sb\Db\Model\Book $book, $addButtons, $addReviews, $addHiddenFields, $isInForm = true, $addRecommendations = false) {
        parent::__construct();
        $this->book = $book;
        $this->addButtons = $addButtons;
        $this->addReviews = $addReviews;
        $this->addHiddenFields = $addHiddenFields;
        $this->isInForm = $isInForm;
        $this->addRecommendations = $addRecommendations;
    }

    public function get() {
        $tpl = new \Sb\Templates\Template("book");

        $isInLibrary = false;

        $averageRating = $this->book->getAverageRating();
        if ($averageRating)
            $ratingCss = "rating-" . floor($averageRating);
        $nbRatings = $this->book->getNbRatedUserBooks();

        $rating = null;
        $isBlowOfHeart = null;
        $readingStateLabel  = null;
        $ratingCss = null;
        $lendingText = null;
        $lendingLink = null;
        $editBookLink = null;
        $owned = null;
        $requestBorrowLink = null;
        
        // testing if book is view while a user is connected
        if ($this->getContext()->getConnectedUser()) {
            $isConnected = true;
            // testing if the connected user has the book and if some additionnal informations can be shown
            $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->getByBookIdAndUserId($this->getContext()->getConnectedUser()->getId(), $this->book->getId());
            
            if ($userBook && !$userBook->getIs_deleted()) {

                $isInLibrary = true;
                $rating = $userBook->getRating();

                $isBlowOfHeart = $userBook->getIsBlowOfHeart();

                if ($userBook->getReadingState())
                    $readingStateLabel = $userBook->getReadingState()->getLabel();

                if ($rating)
                    $ratingCss = "rating-" . $rating;

                $lendingLink = "";
                if ($userBook->getIsOwned())
                    $lendingLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::LENDING_EDIT, "ubid" => $userBook->getId()));

                $lendingText = __("Prêter à un ami", "s1b");
                if ($userBook->getActiveLending())
                    $lendingText = __("Prêt", "s1b");

                $editBookLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_EDIT, "ubid" => $userBook->getId()));
                
                $owned = $userBook->getIsOwned();
                $requestBorrowLink = "";
            } else
                $requestBorrowLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::LENDING_BORROWFROMFRIENDS, "bid" => $this->book->getId()));
        } else {
            $isConnected = false;
        }

        $image = \Sb\Helpers\BookHelper::getMediumImageTag($this->book, $this->defImg);
        $bookTitle = $this->book->getTitle();
        $bookDescription = $this->book->getDescription();
        $bookPublication = $this->book->getPublicationInfo();
        $bookAuthors = $this->book->getOrderableContributors();

        $titleEsc= "";
        $authorEsc = "";
        $isbn10 = "";
        $isbn13 = "";
        $asin = "";
        $id = "";
        $smallImg = "";
        $img = "";
        $largeImg = "";
        $pubEsc = "";
        $pubDtStr = "";
        $amazonUrl = "";
        $booksUsersAlsoLikedShelf = "";
        $booksWithSameTagsShelf = "";
        $descEsc = "";
        if ($this->addHiddenFields) {
            $titleEsc = urlencode($this->book->getTitle()); // encodé
            $authorEsc = urlencode($this->book->getOrderableContributors());  // encodé
            $id = $this->book->getId();
            $isbn10 = $this->book->getISBN10();
            $isbn13 = $this->book->getISBN13();
            $asin = $this->book->getASIN();
            $descEsc = urlencode($this->book->getDescription());  // encodé
            $smallImg = $this->book->getSmallImageUrl();
            $img = $this->book->getImageUrl();
            $largeImg = $this->book->getLargeImageUrl();
            if ($this->book->getPublisher())
                $pubEsc = urlencode($this->book->getPublisher()->getName());  // encodé
            $pubDtStr = "";
            if ($this->book->getPublishingDate())
                $pubDtStr = $this->book->getPublishingDate()->format("Y-m-d H:i:s");
            $amazonUrl = $this->book->getAmazonUrl();
        }

        // book reviews
        $userBooks = $this->book->getNotDeletedUserBooks();
        $reviewedUserBooks = array_filter($userBooks, array(&$this, "isReviewd"));
        $reviews = "";
        if ($reviewedUserBooks) {
            $paginatedList = new \Sb\Lists\PaginatedList($reviewedUserBooks, 5);
            $reviewsView = new \Sb\View\BookReviews($paginatedList, $this->book->getId());
            $reviews = $reviewsView->get();
        }

        if ($this->addRecommendations) {
            // Books users also liked
            $booksUsersAlsoLikedShelf = "";
            $booksUsersAlsoLiked = BookSvc::getInstance()->getBooksAlsoLiked($id);
            if (count($booksUsersAlsoLiked) > 0) {
                $booksUsersAlsoLikedShelfView = new BookShelf($booksUsersAlsoLiked, __("<strong>Les membres</strong> qui ont lu ce livre <strong>ont aussi aimé</strong>", "s1b"));
                $booksUsersAlsoLikedShelf = $booksUsersAlsoLikedShelfView->get();
            }

            // Books with same tags
            $booksWithSameTagsShelf = "";
            $booksWithSameTags = BookSvc::getInstance()->getBooksWithSameTags($id);
            if (count($booksWithSameTags) > 0) {
                $booksWithSameTagsShelfView = new BookShelf($booksWithSameTags, __("Les livres <strong>dans la même catégorie</strong>", "s1b"));
                $booksWithSameTagsShelf = $booksWithSameTagsShelfView->get();
            }
        }

        $tpl->setVariables(array("isConnected" => $isConnected,
            "isInLibrary" => $isInLibrary,
            "rating" => $rating,
            "nbRatings" => $nbRatings,
            "averageRating" => $averageRating,
            "isBlowOfHeart" => $isBlowOfHeart,
            "readingStateLabel" => $readingStateLabel,
            "ratingCss" => $ratingCss,
            "lendingText" => $lendingText,
            "lendingLink" => $lendingLink,
            "editBookLink" => $editBookLink,
            "requestBorrowLink" => $requestBorrowLink,
            "image" => $image,
            "bookTitle" => $bookTitle,
            "bookDescription" => $bookDescription,
            "bookPublication" => $bookPublication,
            "bookAuthors" => $bookAuthors,
            "owned" => $owned,
            "addReviews" => $this->addReviews,
            "addButtons" => $this->addButtons,
            "reviews" => $reviews,
            "addHiddenFields" => $this->addHiddenFields,
            "titleEsc" => $titleEsc,
            "authorEsc" => $authorEsc,
            "id" => $id,
            "isbn10" => $isbn10,
            "isbn13" => $isbn13,
            "asin" => $asin,
            "descEsc" => $descEsc,
            "smallImg" => $smallImg,
            "img" => $img,
            "largeImg" => $largeImg,
            "pubEsc" => $pubEsc,
            "pubDtStr" => $pubDtStr,
            "amazonUrl" => $amazonUrl,
            "isInForm" => $this->isInForm,
            "booksUsersAlsoLikedShelf" => $booksUsersAlsoLikedShelf,
            "booksWithSameTagsShelf" => $booksWithSameTagsShelf
        ));

        return $tpl->output();
    }

    private function isReviewd(\Sb\Db\Model\UserBook $userBook) {
        if ($userBook->getReview()) {
            return true;
        }
    }

}

