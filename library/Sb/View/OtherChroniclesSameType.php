<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class OtherChroniclesSameType extends \Sb\View\AbstractView {

    private $chronicles;

    function __construct($chronicles = null) {

        parent::__construct();
        $this->chronicles = $chronicles;
    }

    public function get() {

        $tpl = new Template("otherChroniclesSameType");
        
        $tpl->setVariables(array(
                "chronicles" => $this->chronicles,
                "title" => __("Chroniques similaires", "s1b")
        ));
        
        return $tpl->output();
    }

}
