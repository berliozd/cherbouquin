<?php

namespace Sb\View;

use Sb\Db\Model\User;

class OtherUserLastFriends extends \Sb\View\AbstractView {

    private $otherUserFriendsAddedEvents;
    private $otherUser;

    function __construct(User $otherUser, $otherUserFriendsAddedEvents) {
        parent::__construct();

        if ($otherUserFriendsAddedEvents)
            $this->otherUserFriendsAddedEvents = array_slice($otherUserFriendsAddedEvents, 0, 5);
        $this->otherUser = $otherUser;
    }

    public function get() {

        $baseTpl = "otherUserLastFriends";
        $tpl = new \Sb\Templates\Template($baseTpl);

        $params = array();
        $params["defImage"] = $this->defImg;
        $params["otherUserFriendsAddedEvents"] = $this->otherUserFriendsAddedEvents;
        $params["otherUser"] = $this->otherUser;

        $tpl->setVariables($params);

        return $tpl->output();
    }

}