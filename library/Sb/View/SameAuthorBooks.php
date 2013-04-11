<?php

namespace Sb\View;

class SameAuthorBooks extends \Sb\View\AbstractView {

    private $books;

    function __construct($books) {
        parent::__construct();
        $this->books = $books;
    }

    public function get() {
        $tpl = new \Sb\Templates\Template("sameAuthorBooks");
        $tpl->setVariables(array("books" => $this->books));
        return $tpl->output();
    }

}