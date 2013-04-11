<?php

namespace Sb\View\Components;

/**
 * Description of \Sb\View\Book
 * Vues d'un livre : detail
 *
 * @author Didier
 */
class SearchForm extends \Sb\View\AbstractView {

    private $showSearchTermDef = true;
    
    function __construct($showSearchTermDef = true) {
        $this->showSearchTermDef = $showSearchTermDef;
        parent::__construct();
    }

    public function get() {
        $tpl = new \Sb\Templates\Template("components/searchForm");

        $formAction = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::BOOK_SEARCH, array("page" => \Sb\Entity\LibraryPages::SEARCH_SUBMIT));
        $searchTermDef = "Titre, auteur, ISBN";
        $searchTerm = \Sb\Helpers\ArrayHelper::getSafeFromArray($_REQUEST, "searchTerm", $searchTermDef);
        $isConnected = false;
        if ($this->getContext()->getConnectedUser())
            $isConnected = true;

        $tpl->setVariables(array("formAction" => $formAction,
            "searchTerm" => $searchTerm,
            "searchTermDef" => $searchTermDef,
            "isConnected" => $isConnected,
            "showSearchTermDef" => $this->showSearchTermDef));
        
        return $tpl->output();
    }

}