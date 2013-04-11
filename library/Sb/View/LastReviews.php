<?php

namespace Sb\View;

class LastReviews extends \Sb\View\AbstractView {

    private $lastReviews;
    private $title;

    function __construct($lastReviews, $title) {
        parent::__construct();
        $this->lastReviews = $lastReviews;
        $this->title = $title;
    }

    public function get() {
        $tpl = new \Sb\Templates\Template("lastReviews");
        $tpl->setVariables(array(
            "lastReviews" => $this->lastReviews,
            "defaultImage" => $this->defImg,
            "title" => $this->title));
        return $tpl->output();
    }

}
