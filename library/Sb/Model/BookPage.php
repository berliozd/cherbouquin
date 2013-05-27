<?php

namespace Sb\Model;

use Sb\Db\Model\Book;
use Sb\Db\Model\PressReview;

/**
 *
 * @author Didier
 */
class BookPage {
    
    /* @var $book Book */
    private $book;

    private $relatedChronicles;

    private $videoPressReview;

    private $pressReviews;

    private $lastlyReadUserbooks;

    private $reviewedUserBooks;

    private $booksWithSameAuthor;

    private $booksAlsoLiked;

    private $booksWithSameTags;

    /**
     *
     * @return Book $book
     */
    public function getBook() {

        return $this->book;
    }

    /**
     *
     * @param Book $book
     */
    public function setBook($book) {

        $this->book = $book;
    }

    /**
     *
     * @return array of Chronicle $relatedChronicles
     */
    public function getRelatedChronicles() {

        return $this->relatedChronicles;
    }

    /**
     *
     * @param array of Chronicle $relatedChronicles
     */
    public function setRelatedChronicles($relatedChronicles) {

        $this->relatedChronicles = $relatedChronicles;
    }

    /**
     * Get press reviews of type video
     * @return PressReview $videoPressReview
     */
    public function getVideoPressReview() {

        return $this->videoPressReview;
    }

    /**
     *
     * @param PressReview $videoPressReview
     */
    public function setVideoPressReview($videoPressReview) {

        $this->videoPressReview = $videoPressReview;
    }

    /**
     *
     * @return array of PressReview $pressReviews
     */
    public function getPressReviews() {

        return $this->pressReviews;
    }

    /**
     *
     * @param array of PressReview $pressReviews
     */
    public function setPressReviews($pressReviews) {

        $this->pressReviews = $pressReviews;
    }

    /**
     *
     * @return array of UserBook $lastlyReadUserbooks
     */
    public function getLastlyReadUserbooks() {

        return $this->lastlyReadUserbooks;
    }

    /**
     *
     * @param array of UserBook $lastlyReadUserbooks
     */
    public function setLastlyReadUserbooks($lastlyReadUserbooks) {

        $this->lastlyReadUserbooks = $lastlyReadUserbooks;
    }

    /**
     *
     * @return array of UserBook $reviewedUserBooks
     */
    public function getReviewedUserBooks() {

        return $this->reviewedUserBooks;
    }

    /**
     *
     * @param array of UserBook $reviewedUserBooks
     */
    public function setReviewedUserBooks($reviewedUserBooks) {

        $this->reviewedUserBooks = $reviewedUserBooks;
    }

    /**
     *
     * @return array of book $booksWithSameAuthor
     */
    public function getBooksWithSameAuthor() {

        return $this->booksWithSameAuthor;
    }

    /**
     *
     * @param array of book $booksWithSameAuthor
     */
    public function setBooksWithSameAuthor($booksWithSameAuthor) {

        $this->booksWithSameAuthor = $booksWithSameAuthor;
    }

    /**
     *
     * @return array of book $booksAlsoLiked
     */
    public function getBooksAlsoLiked() {

        return $this->booksAlsoLiked;
    }

    /**
     *
     * @param array of book $booksAlsoLiked
     */
    public function setBooksAlsoLiked($booksAlsoLiked) {

        $this->booksAlsoLiked = $booksAlsoLiked;
    }

    /**
     *
     * @return array of book $booksWithSameTags
     */
    public function getBooksWithSameTags() {

        return $this->booksWithSameTags;
    }

    /**
     *
     * @param array of book $booksWithSameTags
     */
    public function setBooksWithSameTags($booksWithSameTags) {

        $this->booksWithSameTags = $booksWithSameTags;
    }

}
