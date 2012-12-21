<?php

namespace Sb\View;

use Sb\Helpers\UserHelper;
use Sb\Helpers\HTTPHelper;
use Sb\Helpers\BookHelper;
use Sb\Templates\Template;
use Sb\Db\Model\GroupChronicle;
use Sb\Entity\Urls;

/**
 *
 * @author Didier
 */
class PushedChronicle extends \Sb\View\AbstractView {

    private $chronicle;
    private $boh;

    function __construct(GroupChronicle $chronicle = null) {
        parent::__construct();
        $this->chronicle = $chronicle;
    }

    public function get() {

        $tpl = new Template("pushedChronicle");

        $userName = $this->chronicle->getUser()->getFriendlyName();
        $userProfileLink = HTTPHelper::Link(Urls::FRIEND_PROFILE, array("fid" => $this->chronicle->getUser()->getId()));
        $userImage = UserHelper::getMediumImageTag($this->chronicle->getUser());

        $bookImage = BookHelper::getMediumImageTag($this->chronicle->getBook(), $this->defImg);
        $bookTitle = $this->chronicle->getBook()->getTitle();
        $bookAuthors = $this->chronicle->getBook()->getOrderableContributors();
        $bookLink = $this->chronicle->getBook()->getLink();
        $title = $this->chronicle->getTitle();
        $text = $this->chronicle->getText();
        $link = $this->chronicle->getLink();

        // Set variables
        $tpl->setVariables(array("userName" => $userName,
            "userProfileLink" => $userProfileLink,
            "userImage" => $userImage,
            "bookTitle" => $bookTitle,
            "bookAuthors" => $bookAuthors,
            "bookLink" => $bookLink,
            "bookImage" => $bookImage,
            "title" => $title,
            "text" => $text,
            "link" => $link));

        return $tpl->output();
    }

}