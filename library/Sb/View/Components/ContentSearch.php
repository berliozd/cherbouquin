<?php

namespace Sb\View\Components;

/**
 *
 * @author Didier
 */
class ContentSearch extends \Sb\View\AbstractView {

    private $action, $tags, $selectedTagId, $searchLabel, $pageKey, $searchTerm, $initUrl;

    function __construct($action, $tags, $selectedTagId, $searchLabel, $pageKey, $searchTerm, $initUrl) {

        $this->action = $action;
        $this->tags = $tags;
        $this->selectedTagId = $selectedTagId;
        $this->searchLabel = $searchLabel;
        $this->pageKey = $pageKey;
        $this->searchTerm = $searchTerm;
        $this->initUrl = $initUrl;
        parent::__construct();
    }

    public function get() {

        $tpl = new \Sb\Templates\Template("components/contentSearch");
        $tpl->setVariables(array(
                "action" => $this->action,
                "tags" => $this->tags,
                "selectedTagId" => $this->selectedTagId,
                "searchLabel" => $this->searchLabel,
                "pageKey" => $this->pageKey,
                "searchTerm" => $this->searchTerm,
                "initUrl" => $this->initUrl
        ));
        
        return $tpl->output();
    }

}