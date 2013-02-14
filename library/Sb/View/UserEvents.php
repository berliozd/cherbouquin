<?php

namespace Sb\View;

class UserEvents extends \Sb\View\AbstractView {

    private $userEvents;
    private $showOwner;

    function __construct($userEvents, $showOwner) {
        $this->userEvents = $userEvents;
        $this->showOwner = $showOwner;
        parent::__construct();
    }

    public function get() {
        $tpl = new \Sb\Templates\Template("userEvents");
        $tpl->setVariables(array(
            "events" => $this->userEvents,
            "showOwner" => $this->showOwner)
        );
        return $tpl->output();
    }

}
