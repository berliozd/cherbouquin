<?php

namespace Sb\View\Components;

use Sb\Db\Model\User;
use Sb\Templates\Template;

class UserLastEvents extends \Sb\View\AbstractView {

    private $user;
    private $events;
    
    function __construct(User $user, $events) {
        parent::__construct();
        $this->user = $user;
        $this->events = $events;
    }

    public function get() {

        $baseTpl = "components/userLastEvents";
        $tpl = new Template($baseTpl);

        $params = array();
        $params["events"] = $this->events;
        $params["user"] = $this->user;

        $tpl->setVariables($params);

        return $tpl->output();
    }
}