<?php

namespace Sb\Model;

use Sb\Db\Model\Book;

/**
 * Represent a chronicle object with all data for display on detail page for example
 * @author Didier
 */
class ChronicleViewModel extends ChronicleViewModelLight {

    private $text;

    private $userName;

    private $userProfileLink;

    private $userImage;

    private $chronicleHasBook;

    private $book;

    private $linkCss;

    private $linkText;

    private $source;

    private $typeLabel;

    private $similarChronicles;

    private $sameAuthorChronicles;
    
    private $pressReviews;

    /**
     *
     * @return String $userName
     */
    public function getUserName() {

        return $this->userName;
    }

    /**
     *
     * @param String $userName
     */
    public function setUserName($userName) {

        $this->userName = $userName;
    }

    /**
     *
     * @return String $userProfileLink
     */
    public function getUserProfileLink() {

        return $this->userProfileLink;
    }

    /**
     *
     * @param String $userProfileLink
     */
    public function setUserProfileLink($userProfileLink) {

        $this->userProfileLink = $userProfileLink;
    }

    /**
     *
     * @return String $userImage
     */
    public function getUserImage() {

        return $this->userImage;
    }

    /**
     *
     * @param String $userImage
     */
    public function setUserImage($userImage) {

        $this->userImage = $userImage;
    }

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
     * @return String $chronicleHasBook
     */
    public function getChronicleHasBook() {

        return $this->chronicleHasBook;
    }

    /**
     *
     * @param String $chronicleHasBook
     */
    public function setChronicleHasBook($chronicleHasBook) {

        $this->chronicleHasBook = $chronicleHasBook;
    }

    /**
     *
     * @return String $text
     */
    public function getText() {

        return $this->text;
    }

    /**
     *
     * @param String $text
     */
    public function setText($text) {

        $this->text = $text;
    }

    /**
     *
     * @return String $link
     */
    public function getLink() {

        return $this->link;
    }

    /**
     *
     * @param String $link
     */
    public function setLink($link) {

        $this->link = $link;
    }

    /**
     *
     * @return String $linkCss
     */
    public function getLinkCss() {

        return $this->linkCss;
    }

    /**
     *
     * @param String $linkCss
     */
    public function setLinkCss($linkCss) {

        $this->linkCss = $linkCss;
    }

    /**
     *
     * @return String $linkText
     */
    public function getLinkText() {

        return $this->linkText;
    }

    /**
     *
     * @param String $linkText
     */
    public function setLinkText($linkText) {

        $this->linkText = $linkText;
    }

    /**
     *
     * @return String $source
     */
    public function getSource() {

        return $this->source;
    }

    /**
     *
     * @param String $source
     */
    public function setSource($source) {

        $this->source = $source;
    }

    /**
     *
     * @return String $typeLabel
     */
    public function getTypeLabel() {

        return $this->typeLabel;
    }

    /**
     *
     * @param String $typeLabel
     */
    public function setTypeLabel($typeLabel) {

        $this->typeLabel = $typeLabel;
    }

    /**
     *
     * @return array of ChronicleViewModelLight $similarChronicles
     */
    public function getSimilarChronicles() {

        return $this->similarChronicles;
    }

    /**
     *
     * @param array of ChronicleViewModelLight $similarChronicles
     */
    public function setSimilarChronicles($similarChronicles) {

        $this->similarChronicles = $similarChronicles;
    }

    /**
     *
     * @return array of ChronicleViewModelLight $sameAuthorChronicles
     */
    public function getSameAuthorChronicles() {

        return $this->sameAuthorChronicles;
    }

    /**
     *
     * @param array of ChronicleViewModelLight $sameAuthorChronicles
     */
    public function setSameAuthorChronicles($sameAuthorChronicles) {

        $this->sameAuthorChronicles = $sameAuthorChronicles;
    }
	/**
     * @return array of PressReview $pressReviews
     */
    public function getPressReviews() {

        return $this->pressReviews;
    }


	/**
     * @param array of PressReview $pressReviews
     */
    public function setPressReviews($pressReviews) {

        $this->pressReviews = $pressReviews;
    }


    
    

}
