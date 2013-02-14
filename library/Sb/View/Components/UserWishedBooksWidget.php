<?php

namespace Sb\View\Components;

use Sb\Db\Dao\UserBookDao;
use Sb\Db\Model\User;
use Sb\Templates\Template;

class UserWishedBooksWidget extends \Sb\View\AbstractView {

    private $user;
    private $isCurrentConnectedUser;
    
    function __construct(User $user, $isCurrentConnectedUser) {
        parent::__construct();
        $this->user = $user;
        $this->isCurrentConnectedUser = $isCurrentConnectedUser;
    }

    public function get() {

        $baseTpl = "components/userWishedBooksWidget";
        $tpl = new Template($baseTpl);

        $params = array();
        $wishedBooks = UserBookDao::getInstance()->getListWishedBooks($this->user->getId(), -1, false);
        $params["wishedBooks"] = $wishedBooks;
        $params["isCurrentConnectedUser"] = $this->isCurrentConnectedUser;
        $params["user"] = $this->user;
        $params["defImage"] = $this->defImg;
        

        $tpl->setVariables($params);

        return $tpl->output();
    }

}