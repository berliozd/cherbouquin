<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class BookCoverFlip extends AbstractView {

    private $books;
    private $title;
    private $coverFlipId;
    private $css;

    function __construct($books, $title, $coverFlipId, $css) {
        parent::__construct();
        $this->books = $books;
        $this->title = $title;
        $this->coverFlipId = $coverFlipId;
        $this->css = $css;
    }

    public function get() {

        $tpl = new Template("bookCoverFlip");

        // Set variables
        $tpl->setVariables(array(
            "books" => $this->books,
            "title" => $this->title,
            "defaultImage" => $this->getContext()->getDefaultImage(),
            "coverFlipId" => $this->coverFlipId,
            "css" => $this->css)
        );

        return $tpl->output();
    }

}