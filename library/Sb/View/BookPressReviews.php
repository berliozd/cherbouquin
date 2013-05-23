<?php

namespace Sb\View;

use Sb\Templates\Template;

class BookPressReviews extends \Sb\View\AbstractView {

    private $pressReviews;

    function __construct($pressReviews) {

        parent::__construct();
        $this->pressReviews = $pressReviews;
    }

    public function get() {

        $tpl = new Template("bookPressReviews");
        $tpl->setVariables(array(
                "pressReviews" => $this->pressReviews
        ));
        return $tpl->output();
    }

}