<?php

namespace Sb\View\Components;

/**
 *
 * @author Didier
 */
class NewsReader extends \Sb\View\AbstractView {

    private $pressReviews;

    function __construct($pressReviews) {

        $this->pressReviews = $pressReviews;
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/newsReader";
        $tpl = new \Sb\Templates\Template($baseTpl);
        $tpl->setVariables(array(
                "pressReviews" => $this->pressReviews
        ));
        return $tpl->output();
    }

}