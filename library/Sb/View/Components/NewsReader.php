<?php

namespace Sb\View\Components;

/**
 *
 * @author Didier
 */
class NewsReader extends \Sb\View\AbstractView {

    private $pressReviews;
    private $title;

    function __construct($pressReviews, $title) {

        $this->pressReviews = $pressReviews;
        $this->title = $title;
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/newsReader";
        $tpl = new \Sb\Templates\Template($baseTpl);
        $tpl->setVariables(array(
                "pressReviews" => $this->pressReviews,
                "title" => $this->title
        ));
        return $tpl->output();
    }

}