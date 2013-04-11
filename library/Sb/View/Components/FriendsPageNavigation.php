<?php

namespace Sb\View\Components;

class FriendsPageNavigation extends \Sb\View\AbstractView {

    private $activeItem;

    function __construct($activeItem) {
        parent::__construct();
        $this->activeItem = $activeItem;
    }

    public function get() {

        $tpl = new \Sb\Templates\Template("components/friendsPageNavigation");
        $tpl->setVariables(array("activeItem" => $this->activeItem));
        return $tpl->output();
    }

}