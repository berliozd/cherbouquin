<?php

namespace Sb\View\Components;

/**
 * Description of \Sb\View\Book
 * Vues d'un livre : detail
 *
 * @author Didier
 */
class SearchForm extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {
        $tpl = new \Sb\Templates\Template("components/searchForm");

        $formAction = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::BOOK_SEARCH, array("page" => \Sb\Entity\LibraryPages::SEARCH_SUBMIT));
        $searchTermDef = __("Titre, auteur, ISBN", "s1b");
        $searchTerm = \Sb\Helpers\ArrayHelper::getSafeFromArray($_REQUEST, "searchTerm", $searchTermDef);
        $isConnected = false;
        if ($this->getContext()->getConnectedUser())
            $isConnected = true;

        $tpl->setVariables(array("formAction" => $formAction,
            "searchTerm" => $searchTerm,
            "searchTermDef" => $searchTermDef,
            "isConnected" => $isConnected));
        return $tpl->output();
    }

}