<?php

namespace Sb\View;

use Sb\Helpers\UserHelper;
use Sb\Helpers\HTTPHelper;
use Sb\Helpers\BookHelper;
use Sb\Templates\Template;
use Sb\Db\Model\GroupChronicle;
use Sb\Entity\Urls;
use Sb\Entity\GroupChronicleLinkType;

/**
 *
 * @author Didier
 */
class PushedChronicle extends \Sb\View\AbstractView {

    private $chronicle;

    function __construct(GroupChronicle $chronicle = null) {
        parent::__construct();
        $this->chronicle = $chronicle;
    }

    public function get() {

        $tpl = new Template("pushedChronicle");

        $userName = $this->chronicle->getUser()->getUserName();
        $userProfileLink = HTTPHelper::Link(Urls::USER_PROFILE, array("uid" => $this->chronicle->getUser()->getId()));
        $userImage = UserHelper::getMediumImageTag($this->chronicle->getUser());

        $chronicleHasBook = false;
        $bookImage = "";
        $bookTitle = "";
        $bookAuthors = "";
        $bookLink = "";
        if ($this->chronicle->getBook()) {
            $bookImage = BookHelper::getMediumImageTag($this->chronicle->getBook(), $this->defImg);
            $bookTitle = $this->chronicle->getBook()->getTitle();
            $bookAuthors = $this->chronicle->getBook()->getOrderableContributors();
            $bookLink = $this->chronicle->getBook()->getLink();
            $chronicleHasBook = true;
        }

        $title = $this->chronicle->getTitle();
        $text = $this->chronicle->getText();
        $link = $this->chronicle->getLink();
        $source = $this->chronicle->getSource();
        $linkCss = "pci-link-other";
        $linkText = __("En savoir plus", "s1b");
        switch ($this->chronicle->getLink_type()) {
            case GroupChronicleLinkType::IMAGE:
                $linkCss = "pci-link-image";
                $linkText = __("Voir la photo", "s1b");
                break;            
            case GroupChronicleLinkType::PODCAST:
                $linkCss = "pci-link-podcast";
                $linkText = __("Ecouter le podcast", "s1b");
                break;
            case GroupChronicleLinkType::PRESS:
                $linkCss = "pci-link-press";
                $linkText = __("Lire l'article", "s1b");
                break;
            case GroupChronicleLinkType::URL:
                $linkCss = "pci-link-url";
                $linkText = __("En savoir plus", "s1b");
                break;
            case GroupChronicleLinkType::VIDEO:
                $linkCss = "pci-link-video";
                $linkText = __("Regarder la video", "s1b");
                break;
        }

        // Set variables
        $tpl->setVariables(array("userName" => $userName,
            "userProfileLink" => $userProfileLink,
            "userImage" => $userImage,
            "bookTitle" => $bookTitle,
            "chronicleHasBook" => $chronicleHasBook,
            "bookAuthors" => $bookAuthors,
            "bookLink" => $bookLink,
            "bookImage" => $bookImage,
            "title" => $title,
            "text" => $text,
            "link" => $link,
            "linkCss" => $linkCss,
            "linkText" => $linkText,
            "source" => $source));

        return $tpl->output();
    }

}