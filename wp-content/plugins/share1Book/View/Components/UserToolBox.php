<?php

namespace Sb\View\Components;

class UserToolBox extends \Sb\View\AbstractView {

    // Flag to tell if currently reading books must be shown or not
    private $currentlyReadingBooks = false;

    function __construct($currentlyReadingBooks = false) {
        $this->currentlyReadingBooks = $currentlyReadingBooks;
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/userToolBox";
        $tpl = new \Sb\Templates\Template($baseTpl);

        $user = $this->getContext()->getConnectedUser();
        $nbFriends = count($user->getAcceptedFriends());
        $nbMessagesToRead = count($user->getUnReadReceivedMessages());
        $nbPendingRequests = count($user->getPendingFriendShips());

        $params = array("nbPendingRequests" => $nbPendingRequests,
            "nbMessagesToRead" => $nbMessagesToRead,
            "nbFriends" => $nbFriends,
            "defImage" => $this->getContext()->getDefaultImage());

        // Add the currently reading books in requested
        if ($this->currentlyReadingBooks) {
            $currentlyReadingUserBooks = \Sb\Db\Dao\UserBookDao::getInstance()->getCurrentlyReadingsNow($user->getId());
            // Getting only first 5 items
            $currentlyReadingUserBooks = array_slice($currentlyReadingUserBooks, 0, 5);
            $params["currentlyReadingUserBooks"] = $currentlyReadingUserBooks;
        }

        $tpl->setVariables($params);

        return $tpl->output();
    }

}