<?php

namespace Sb\View\Components;

class FacebookFrame extends \Sb\View\AbstractView {

    
    function __construct() {
        parent::__construct();

    }

    public function get() {

        $tpl = new \Sb\Templates\Template("components/facebookFrame");        
        return $tpl->output();
    }

}