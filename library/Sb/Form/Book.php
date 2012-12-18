<?php

namespace Sb\Form;

class Book {

    private $id;
    private $isbn10;
    private $isbn13;
    private $asin;

    function __construct($post) {

        if (array_key_exists('book_ISBN10', $post)) {
            $this->isbn10 = $post['book_ISBN10'];
        }
        if (array_key_exists('book_ISBN13', $post)) {
            $this->isbn13 = $post['book_ISBN13'];
        }
        if (array_key_exists('book_ASIN', $post)) {
            $this->asin = $post['book_ASIN'];
        }
        if (array_key_exists('book_Id', $post)) {
            $this->id = $post['book_Id'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getIsbn10() {
        return $this->isbn10;
    }

    public function getIsbn13() {
        return $this->isbn13;
    }

    public function getAsin() {
        return $this->asin;
    }

}