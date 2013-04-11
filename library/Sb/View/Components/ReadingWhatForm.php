<?php

namespace Sb\View\Components;

/**
 *
 * @author Didier
 */
class ReadingWhatForm extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/readingWhatForm";
        $tpl = new \Sb\Templates\Template($baseTpl);
        $tpl->setVariables(array("coverImage" => $this->getContext()->getBaseUrl() . "Resources/images/nocover.png"));
        return $tpl->output();
    }

}