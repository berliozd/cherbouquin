<?php

namespace Sb\View\Components;

/**
 *
 * @author Didier
 */
class NoBooksWidget extends \Sb\View\AbstractView {

    private $label;

    function __construct($label) {
        parent::__construct();
        $this->label = $label;
    }

    public function get() {
        $baseTpl = "components/noBooksWidget";
        $tpl = new \Sb\Templates\Template($baseTpl);
        $tpl->setVariables(array("label" => $this->label, "coverImage" => $this->getContext()->getBaseUrl() . "Resources/images/nocover.png"));
        return $tpl->output();
    }

}