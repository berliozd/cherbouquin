<?php

namespace Sb\View\Components;

/**
 *
 * @author Didier
 */
class Ad extends \Sb\View\AbstractView {

    private $label, $code;

    function __construct($label, $code) {
        $this->label = $label;
        $this->code = $code;
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/ad";
        $tpl = new \Sb\Templates\Template($baseTpl);
        $tpl->setVariables(array("label" => $this->label, "code" => $this->code));
        // Renders the ads only on production
        if ($this->getConfig()->getIsProduction())
            return $tpl->output();
        else
            return "";
    }

}