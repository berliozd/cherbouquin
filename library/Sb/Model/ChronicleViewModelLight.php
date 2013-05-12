<?php

namespace Sb\Model;

/**
 * Represent a chronicle with minimum data to be displayed as an item in a list (for example for same author chronicles on detail page)
 * @author Didier
 */
class ChronicleViewModelLight {

    private $chronicleId;

    private $title;

    private $shortenText;

    private $link;

    private $image;

    private $detailLink;

    private $creationDate;

    private $nbViews;

    /**
     *
     * @return int $chronicleId
     */
    public function getChronicleId() {

        return $this->chronicleId;
    }

    /**
     *
     * @param int $chronicleId
     */
    public function setChronicleId($chronicleId) {

        $this->chronicleId = $chronicleId;
    }

    /**
     *
     * @return String $title
     */
    public function getTitle() {

        return $this->title;
    }

    /**
     *
     * @param String $title
     */
    public function setTitle($title) {

        $this->title = $title;
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
     * @return String $text
     */
    public function getShortenText() {

        return $this->shortenText;
    }

    /**
     *
     * @param String $shortenText
     */
    public function setShortenText($shortenText) {

        $this->shortenText = $shortenText;
    }

    /**
     *
     * @return String $image
     */
    public function getImage() {

        return $this->image;
    }

    /**
     *
     * @param String $image
     */
    public function setImage($image) {

        $this->image = $image;
    }

    /**
     *
     * @return String $detailLink
     */
    public function getDetailLink() {

        return $this->detailLink;
    }

    /**
     *
     * @param String $detailLink
     */
    public function setDetailLink($detailLink) {

        $this->detailLink = $detailLink;
    }

    /**
     *
     * @return DateTime $creationDate
     */
    public function getCreationDate() {

        return $this->creationDate;
    }

    /**
     *
     * @param DateTime $creationDate
     */
    public function setCreationDate($creationDate) {

        $this->creationDate = $creationDate;
    }

    /**
     *
     * @return int $nbViews
     */
    public function getNbViews() {

        return $this->nbViews;
    }

    /**
     *
     * @param int $nbViews
     */
    public function setNbViews($nbViews) {

        $this->nbViews = $nbViews;
    }

}
