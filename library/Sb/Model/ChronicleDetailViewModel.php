<?php

namespace Sb\Model;

class ChronicleDetailViewModel {

    private $userName;
    private $userProfileLink;
    private $userImage;
    private $bookTitle;
    private $chronicleHasBook;
    private $bookAuthors;
    private $bookLink;
    private $bookImage;
    private $title;
    private $text;
    private $link;
    private $linkCss;
    private $linkText;
    private $source;
    private $creationDate;
    private $typeLabel;

    /**
     * @return String $userName
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * @param String $userName
     */
    public function setUserName($userName) {
        $this->userName = $userName;
    }

    /**
     * @return String $userProfileLink
     */
    public function getUserProfileLink() {
        return $this->userProfileLink;
    }

    /**
     * @param String $userProfileLink
     */
    public function setUserProfileLink($userProfileLink) {
        $this->userProfileLink = $userProfileLink;
    }

    /**
     * @return String $userImage
     */
    public function getUserImage() {
        return $this->userImage;
    }

    /**
     * @param String $userImage
     */
    public function setUserImage($userImage) {
        $this->userImage = $userImage;
    }

    /**
     * @return String $bookTitle
     */
    public function getBookTitle() {
        return $this->bookTitle;
    }

    /**
     * @param String $bookTitle
     */
    public function setBookTitle($bookTitle) {
        $this->bookTitle = $bookTitle;
    }

    /**
     * @return String $chronicleHasBook
     */
    public function getChronicleHasBook() {
        return $this->chronicleHasBook;
    }

    /**
     * @param String $chronicleHasBook
     */
    public function setChronicleHasBook($chronicleHasBook) {
        $this->chronicleHasBook = $chronicleHasBook;
    }

    /**
     * @return String $bookAuthors
     */
    public function getBookAuthors() {
        return $this->bookAuthors;
    }

    /**
     * @param String $bookAuthors
     */
    public function setBookAuthors($bookAuthors) {
        $this->bookAuthors = $bookAuthors;
    }

    /**
     * @return String $bookLink
     */
    public function getBookLink() {
        return $this->bookLink;
    }

    /**
     * @param String $bookLink
     */
    public function setBookLink($bookLink) {
        $this->bookLink = $bookLink;
    }

    /**
     * @return String $bookImage
     */
    public function getBookImage() {
        return $this->bookImage;
    }

    /**
     * @param String $bookImage
     */
    public function setBookImage($bookImage) {
        $this->bookImage = $bookImage;
    }

    /**
     * @return String $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param String $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return String $text
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param String $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @return String $link
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param String $link
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * @return String $linkCss
     */
    public function getLinkCss() {
        return $this->linkCss;
    }

    /**
     * @param String $linkCss
     */
    public function setLinkCss($linkCss) {
        $this->linkCss = $linkCss;
    }

    /**
     * @return String $linkText
     */
    public function getLinkText() {
        return $this->linkText;
    }

    /**
     * @param String $linkText
     */
    public function setLinkText($linkText) {
        $this->linkText = $linkText;
    }

    /**
     * @return String $source
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @param String $source
     */
    public function setSource($source) {
        $this->source = $source;
    }
    /**
     * @return String $creationDate
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * @param String $creationDate
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    /**
     * @return String $typeLabel
     */
    public function getTypeLabel() {
        return $this->typeLabel;
    }

    /**
     * @param String $typeLabel
     */
    public function setTypeLabel($typeLabel) {
        $this->typeLabel = $typeLabel;
    }

}
