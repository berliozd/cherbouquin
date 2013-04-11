<?php

namespace Sb\View\Components;

use Sb\Db\Model\User;

class UserReadingWidget extends \Sb\View\AbstractView {

    private $currentlyReadingUserBooks;
    private $isCurrentConnectedUser;
    private $user;

    function __construct(User $user, $allCurrentlyReadingUserBooks, $isCurrentConnectedUser) {
        parent::__construct();
        
        // Getting only first 5 items
        $this->currentlyReadingUserBooks = array_slice($allCurrentlyReadingUserBooks, 0, 5);
        
        $this->isCurrentConnectedUser = $isCurrentConnectedUser;        
        
        $this->user = $user;
    }

    public function get() {

        $baseTpl = "components/userReading";
        $tpl = new \Sb\Templates\Template($baseTpl);

        $params = array();
        $params["defImage"] = $this->getContext()->getDefaultImage();
        $params["currentlyReadingUserBooks"] = $this->currentlyReadingUserBooks;
        $params["isCurrentConnectedUser"] = $this->isCurrentConnectedUser;
        $params["user"] = $this->user;

        $tpl->setVariables($params);

        return $tpl->output();
    }

}