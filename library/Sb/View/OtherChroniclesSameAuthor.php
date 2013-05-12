<?php

namespace Sb\View;

use Sb\Templates\Template;
use Sb\Adapter\ChronicleListAdapter;

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

        $tpl = new Template("otherChroniclesSameAuthor");

        $tpl->setVariables(array(
                "chronicles" => $this->chronicles,
                "title" => __("<strong>Chroniques</strong> du même auteur", "s1b")
        ));

        return $tpl->output();
    }

}
