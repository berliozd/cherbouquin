<?php

namespace Sb\View;

class PushedBooks extends \Sb\View\AbstractView {

    private $books;
    private $nbBooksShownByDefault;
    private $boh;

    function __construct($books, $nbBooksShownByDefault, $boh) {
        parent::__construct();
        $this->books = $books;    
        $this->nbBooksShownByDefault = $nbBooksShownByDefault;
        $this->boh = $boh;
    }

    public function get() {

        if ($this->books) {

            $templatesBooks = "";
            foreach ($this->books as $book) {
                $pushedBookView = new \Sb\View\PushedBook($book, $this->boh);
                $templatesBooks .= $pushedBookView->get();
            }

            $nbBooks = count($this->books);
            if ($nbBooks <= $this->nbBooksShownByDefault) {
                $bottomLink = "";
            } else {
                $tplBottomLink = new \Sb\Templates\Template("components/seeMore");
                $bottomLink = $tplBottomLink->output();
            }

            return $templatesBooks . $bottomLink;
        }else
            return "";
    }
}