<?php

namespace Sb\View;

use Sb\Db\Dao\UserDao;
use Sb\Entity\LibraryListKeys;
use Sb\Entity\Urls;
use Sb\Helpers\HTTPHelper;
use Sb\Templates\Template;


class LibraryHeader extends AbstractView
{

    private $friendUserId = null;
    private $key = null;

    function __construct($friendUserId, $key)
    {

        $this->friendUserId = $friendUserId;
        $this->key = $key;

        parent::__construct();
    }

    public function get()
    {

        $globalContext = \Sb\Context\Model\Context::getInstance();

        $tplHeader = new Template("header");

        $baseUrl = Urls::USER_LIBRARY;
        if ($globalContext->getIsShowingFriendLibrary())
            $baseUrl = Urls::FRIEND_LIBRARY;

        $variables = array(
            "allBooksUrl" => HTTPHelper::Link($baseUrl, array("key" => LibraryListKeys::ALL_BOOKS_KEY, "reset" => 1)),
            "borrowedBooksUrl" => HTTPHelper::Link($baseUrl, array("key" => LibraryListKeys::BORROWED_BOOKS_KEY, "reset" => 1)),
            "lendedBooksUrl" => HTTPHelper::Link($baseUrl, array("key" => LibraryListKeys::LENDED_BOOKS_KEY, "reset" => 1)),
            "wishedBooksUrl" => HTTPHelper::Link($baseUrl, array("key" => LibraryListKeys::WISHED_BOOKS_KEY, "reset" => 1)),
            "myBooksUrl" => HTTPHelper::Link($baseUrl, array("key" => LibraryListKeys::MY_BOOKS_KEY, "reset" => 1)),
            "friendLibrary" => false
        );

        if ($globalContext->getIsShowingFriendLibrary()) {

            $friend = UserDao::getInstance()->get($this->friendUserId);

            $variables["friendLibrary"] = true;
            $variables["friendUserName"] = $friend->getFirstName();
        }

        $tplHeader->setVariables($variables);

        $this->setActiveTab($tplHeader, $this->key);

        return $tplHeader->output();
    }

    private function setActiveTab(&$tplHeader, $key)
    {
        $tplHeader->set("cssAll", ($key == "allBooks" ? "active" : ""));
        $tplHeader->set("cssOwned", ($key == "myBooks" ? "active" : ""));
        $tplHeader->set("cssWished", ($key == "wishedBooks" ? "active" : ""));
        $tplHeader->set("cssLended", ($key == "lendedBooks" ? "active" : ""));
        $tplHeader->set("cssBorrowed", ($key == "borrowedBooks" ? "active" : ""));
    }
}


