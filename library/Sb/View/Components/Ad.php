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
        
        // Renders the ads only on production
        if ($this->getConfig()
            ->getIsProduction()) {
            $tpl = new \Sb\Templates\Template("components/ad");
            $tpl->setVariables(array(
                    "label" => $this->label,
                    "code" => $this->code
            ));
            return $tpl->output();
        } 

        else
            return "";
    }

}