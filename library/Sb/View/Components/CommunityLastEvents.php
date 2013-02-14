<?php

namespace Sb\View\Components;

use Sb\Templates\Template;

class CommunityLastEvents extends \Sb\View\AbstractView {

    private $events;
    
    function __construct($events) {
        parent::__construct();
        $this->events = $events;
    }

    public function get() {

        $baseTpl = "components/communityLastEvents";
        $tpl = new Template($baseTpl);

        $params = array();
        $params["events"] = $this->events;

        $tpl->setVariables($params);

        return $tpl->output();
    }
}