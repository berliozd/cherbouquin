<?php

namespace Sb\View;

/**
 * Description of BookList
 *
 * @author Didier
 */
class BookList extends \Sb\View\AbstractView {

    private $key;
    private $booksTableView;
    private $cssClass;

    function __construct($key, $booksTableView, $cssClass) {
        parent::__construct();

        $this->key = $key;
        $this->booksTableView = $booksTableView;
        $this->cssClass = $cssClass;
    }

    public function get() {

        // Préparation du template
        $booksTpl = new \Sb\Templates\Template("bookList");
        $booksTpl->set("bookList", $this->booksTableView->get());
        $booksTpl->set("listTitle", __("Tous", "s1b"));
        $booksTpl->set("listCssClass", $this->cssClass);
        $booksTpl->set("key", $this->key);

        // affichage du template
        return $booksTpl->output();
    }

}