<?php

namespace Sb\View\Components;

use Sb\Templates\Template;

class CreateChroniclesLinks extends \Sb\View\AbstractView {

    private $groupUsers;

    function __construct($groupUsers) {

        parent::__construct();
        $this->groupUsers = $groupUsers;
    }

    public function get() {

        $baseTpl = "components/createChroniclesLinks";
        $tpl = new Template($baseTpl);
        
        $params = array();
        $params["groupUsers"] = $this->groupUsers;
        
        $tpl->setVariables($params);
        
        return $tpl->output();
    }

}