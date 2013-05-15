<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class ChroniclesMoreSeen extends \Sb\View\AbstractView {

    private $chronicles;

    function __construct($chronicles = null) {
        parent::__construct();
        $this->chronicles = $chronicles;
    }

    public function get() {

        $tpl = new Template("chroniclesBlock");

        $tpl->setVariables(array(
                "chronicles" => $this->chronicles,
                "title" => __("<strong>Chroniques</strong> les plus en vues", "s1b")
        ));

        return $tpl->output();
    }

}
