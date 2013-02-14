<?php

namespace Sb\View\Components;

class MailboxWidget extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/mailboxWidget";
        $tpl = new \Sb\Templates\Template($baseTpl);

        $user = $this->getContext()->getConnectedUser();
        $nbMessagesToRead = count($user->getUnReadReceivedMessages());
        $nbPendingRequests = count($user->getPendingFriendShips());

        $params = array("nbPendingRequests" => $nbPendingRequests,
            "nbMessagesToRead" => $nbMessagesToRead);

        $tpl->setVariables($params);

        return $tpl->output();
    }

}