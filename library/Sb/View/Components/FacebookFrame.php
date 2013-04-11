<?php

namespace Sb\View\Components;

class FacebookFrame extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {

        $tpl = new \Sb\Templates\Template("components/facebookFrame");
        if ($this->getConfig()->getIsProduction())
            return $tpl->output();
        else
            return "";
    }

}