<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class OtherChroniclesSameAuthor extends \Sb\View\AbstractView {

    private $chronicles;

    function __construct($chronicles = null) {
        parent::__construct();
        $this->chronicles = $chronicles;
    }

    public function get() {

        $tpl = new Template("chroniclesBlock");

        $tpl->setVariables(array(
                "chronicles" => $this->chronicles,
                "title" => __("<strong>Chroniques</strong> du même auteur", "s1b")
        ));

        return $tpl->output();
    }

}
