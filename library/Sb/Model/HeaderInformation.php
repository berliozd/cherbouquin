<?php

namespace Sb\Model;

/**
 * This class contains information for head part of pages : tag title, meta description, meta keywords
 * @author Didier
 */
class HeaderInformation {

    private $title;

    private $description;

    private $keywords;

    private $urlCanonical;

    public function getTitle() {

        return $this->title;
    }

    public function setTitle($title) {

        $this->title = $title;
    }

    public function getDescription() {

        return $this->description;
    }

    public function setDescription($description) {

        $this->description = $description;
    }

    public function getKeywords() {

        return $this->keywords;
    }

    public function setKeywords($keywords) {

        $this->keywords = $keywords;
    }

    /**
     *
     * @return String $urlCanonical
     */
    public function getUrlCanonical() {

        return $this->urlCanonical;
    }

    /**
     *
     * @param String $urlCanonical
     */
    public function setUrlCanonical($urlCanonical) {

        $this->urlCanonical = $urlCanonical;
    }

}