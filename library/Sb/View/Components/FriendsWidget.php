<?php

namespace Sb\View\Components;

class FriendsWidget extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/friendsWidget";
        $tpl = new \Sb\Templates\Template($baseTpl);

        $user = $this->getContext()->getConnectedUser();
        $nbFriends = count($user->getAcceptedFriends());
        $nbPendingFriendRequests = count($user->getPendingFriendShips());

        $params = array("nbFriends" => $nbFriends,
            "nbPendingFriendRequests" => $nbPendingFriendRequests);
        $tpl->setVariables($params);

        return $tpl->output();
    }

}