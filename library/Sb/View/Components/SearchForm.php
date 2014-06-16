<?php

namespace Sb\View\Components;

use Sb\Entity\Urls,
    Sb\Templates\Template,
    Sb\Helpers\HTTPHelper,
    Sb\Helpers\ArrayHelper;

/**
 * @author Didier
 */
class SearchForm extends \Sb\View\AbstractView {

    private $showSearchTermDef = true;
    
    function __construct($showSearchTermDef = true) {
        $this->showSearchTermDef = $showSearchTermDef;
        parent::__construct();
    }

    public function get() {
        $tpl = new Template("components/searchForm");

        $formAction = HTTPHelper::Link(Urls::BOOK_SEARCH_SUBMIT);
        $searchTermDef = "Titre, auteur, ISBN";
        $searchTerm = ArrayHelper::getSafeFromArray($_REQUEST, "searchTerm", $searchTermDef);
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