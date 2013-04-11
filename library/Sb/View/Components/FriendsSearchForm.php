<?php

namespace Sb\View\Components;

class FriendsSearchForm extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {
        $tpl = new \Sb\Templates\Template("components/friendsSearchForm");
        return $tpl->output();
    }
}