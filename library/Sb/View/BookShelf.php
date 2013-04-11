<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class BookShelf extends AbstractView {

    private $books;
    private $title;

    function __construct($books, $title) {
        parent::__construct();
        $this->books = $books;
        $this->title = $title;
    }

    public function get() {

        $tpl = new Template("bookShelf");

        // Set variables
        $tpl->setVariables(array(
            "books" => $this->books,
            "title" => $this->title,
            "defaultImage" => $this->getContext()->getDefaultImage())
        );

        return $tpl->output();
    }

}