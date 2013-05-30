<?php

namespace Sb\View\Components;

/**
 *
 * @author Didier
 */
class GooglePlus extends \Sb\View\AbstractView {

    function __construct() {

        parent::__construct();
    }

    public function get() {
        
        // Renders the google plus only on production
        if ($this->getConfig()
            ->getIsProduction()) {
            $tpl = new \Sb\Templates\Template("components/googlePlus");
            
            return $tpl->output();
        } 

        else
            return "";
    }

}