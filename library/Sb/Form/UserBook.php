<?php

namespace Sb\Form;

class UserBook {

    private $id;
    private $review;
    private $rating;
    private $isBlowOfHeart;
    private $isOwned;
    private $isWished;
    private $readingStateId;
    private $readingDate;
    private $tags;
    private $hyperLink;
    private $nb_of_pages;
    private $nb_of_pages_read;

    function __construct($post) {

        $this->id = \Sb\Helpers\ArrayHelper::getSafeFromArray($post, "Id", null);

        $this->review = \Sb\Helpers\ArrayHelper::getSafeFromArray($post, "Review", null);

        if (array_key_exists('Rating', $post)) {
            if ($post['Rating'] != "")
                $this->rating = $post['Rating'];
        }

        $this->isBlowOfHeart = \Sb\Helpers\ArrayHelper::getSafeFromArray($post, "IsBlowOfHeart", 0);

        if (array_key_exists('WishedOrOwned', $post)) {
            if ($post['WishedOrOwned'] == "1") { // le livre est possédé par le user
                $this->isOwned = true;
                $this->isWished = false;
            } else { // le livre est souhaité par le user
                $this->isOwned = false;
                $this->isWished = true;
            }
        } else {
            if (array_key_exists('IsOwned', $post)) {
                $this->isOwned = $post['IsOwned'];
            }
            if (array_key_exists('IsWished', $post)) {
                $this->isWished = $post['IsWished'];
            }
        }

        $this->readingStateId = \Sb\Helpers\ArrayHelper::getSafeFromArray($post, "ReadingStateId", null);


        if (array_key_exists('tag', $post)) {
            $this->tags = array_keys($_POST["tag"]);
        }

        if (array_key_exists('ReadingDate', $post)) {
            $this->readingDate = \Sb\Helpers\DateHelper::createDateBis($post['ReadingDate']);
        }
        
        if (array_key_exists('HyperLink', $post)) {
            $this->hyperLink = $post['HyperLink'];
        }
        
        $this->nb_of_pages = \Sb\Helpers\ArrayHelper::getSafeFromArray($post, "nb_of_pages", null);
        $this->nb_of_pages_read = \Sb\Helpers\ArrayHelper::getSafeFromArray($post, "nb_of_pages_read", null);
    }

    public function getId() {
        return $this->id;
    }

    public function getReadingStateId() {
        return $this->readingStateId;
    }

    public function getReadingDate() {
        return $this->readingDate;
    }

    public function getReview() {
        return $this->review;
    }

    public function getRating() {
        return $this->rating;
    }

    public function getIsBlowOfHeart() {
        return $this->isBlowOfHeart;
    }

    public function getIsOwned() {
        return $this->isOwned;
    }

    public function getIsWished() {
        return $this->isWished;
    }

    public function getTags() {
        return $this->tags;
    }
    
    public function getHyperLink() {
        return $this->hyperLink;
    }
    
    public function getNb_of_pages() {
        return $this->nb_of_pages;
    }

    public function setNb_of_pages($nb_of_pages) {
        $this->nb_of_pages = $nb_of_pages;
    }

    public function getNb_of_pages_read() {
        return $this->nb_of_pages_read;
    }

    public function setNb_of_pages_read($nb_of_pages_read) {
        $this->nb_of_pages_read = $nb_of_pages_read;
    }
}