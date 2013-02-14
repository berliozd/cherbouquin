<?php

namespace Sb\View;

class LastUsersWhoReadThatBook extends \Sb\View\AbstractView {

    private $userbooks;

    function __construct($userbooks) {
        parent::__construct();

        $this->userbooks = $userbooks;
    }

    public function get() {

        $baseTpl = "lastUsersWhoReadThatBook";
        $tpl = new \Sb\Templates\Template($baseTpl);

        $params = array();
        $params["defImage"] = $this->defImg;
        $params["userbooks"] = $this->userbooks;

        $tpl->setVariables($params);

        return $tpl->output();
    }

}