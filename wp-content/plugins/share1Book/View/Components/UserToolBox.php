<?php

namespace Sb\View\Components;

use \Sb\Db\Dao\UserBookDao;

class UserToolBox extends \Sb\View\AbstractView {

    // Flag to tell if currently reading books must be shown or not
    private $currentlyReadingBooks = false;
    // Flag to tell if wished books must be shown or not
    private $wishedBooks = false;

    function __construct($currentlyReadingBooks = false, $wishedBooks = false) {
        $this->currentlyReadingBooks = $currentlyReadingBooks;
        $this->wishedBooks = $wishedBooks;
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

        // Temporary desactivate the currently readings books and wished books in usertoolbox
        if (!$this->getConfig()->getIsProduction()) {

            // Add the currently reading books if requested
            if ($this->currentlyReadingBooks) {
                $allCurrentlyReadingUserBooks = UserBookDao::getInstance()->getCurrentlyReadingsNow($user->getId());
                // Getting only first 5 items
                $currentlyReadingUserBooks = array_slice($allCurrentlyReadingUserBooks, 0, 5);
                $params["currentlyReadingUserBooks"] = $currentlyReadingUserBooks;
            }

            // Add the wished books if requested
            if ($this->wishedBooks) {
                $wishedBooks = UserBookDao::getInstance()->getListWishedBooks($this->getContext()->getConnectedUser()->getId(), -1, true);
                $params["wishedBooks"] = $wishedBooks;
            }
        }

        $tpl->setVariables($params);

        return $tpl->output();
    }

}