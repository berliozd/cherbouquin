<?php

namespace Sb\View;

use \Sb\Db\Model\UserEvent as UserEventModel;
use \Sb\Entity\EventTypes;
use \Sb\Entity\ReadingStates;
use \Sb\Helpers\HTTPHelper;
use Sb\Entity\Urls;

class UserEvent extends \Sb\View\AbstractView {

    private $userEvent;
    private $addSep;

    function __construct(UserEventModel $userEvent, $addSep) {
        $this->userEvent = $userEvent;
        $this->addSep = $addSep;
        parent::__construct();
    }

    public function get() {

        $tplEvent = new \Sb\Templates\Template("userEvents/userEvent");

        $userFriend = $this->userEvent->getUser();

        $userFriendImg = $userFriend->getGravatar();
        if ($userFriendImg == "")
            $userFriendImg = $this->getContext()->getBaseUrl() . "/Resources/images/avatars/noavatar.png";

        $friendImageTag = "";
        $friendName = $userFriend->getUserName();
        $friendProfileLink = HTTPHelper::Link(Urls::FRIEND_PROFILE, array("fid" => $userFriend->getId()));

        $userBookRelated = false;
        $additionalContent = "";
        switch ($this->userEvent->getType_id()) {
            case EventTypes::USERBOOK_ADD:
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a ajouté un livre.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_RATING_CHANGE:
                $newRating = $this->userEvent->getNew_value();
                $resume = sprintf("<div class=\"ue-rating-label\"><a href=\"%s\" class=\"link\">%s</a> a noté.</div> <div class=\"rating rating-" . $newRating . "\"></div>", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_BLOWOFHEART_CHANGE:
                $isBoh = $this->userEvent->getNew_value();
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a supprimé son coup de coeur.", $friendProfileLink, $friendName);
                if ($isBoh)
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a marqué comme coup de coeur.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_REVIEW_CHANGE:
                $oldReview = $this->userEvent->getOld_value();
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a modifié son commentaire.", $friendProfileLink, $friendName);
                if ($oldReview == "")
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a ajouté un commentaire.", $friendProfileLink, $friendName);
                $additionalContent = $this->userEvent->getNew_value();
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_HYPERLINK_CHANGE:
                $oldHyperLink = $this->userEvent->getOld_value();
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a modifié son lien hypertexte.", $friendProfileLink, $friendName);
                if ($oldHyperLink == "")
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a ajouté un lien hypertexte.", $friendProfileLink, $friendName);
                $hyperLink = "http://" . $this->userEvent->getNew_value();
                $truncatedHyperLink = \Sb\Helpers\StringHelper::tronque($hyperLink, 100);
                $additionalContent = sprintf(__("Lien : <a href=\"%s\" target=\"_blank\" class=\"link\" >%s</a>","s1b"),$hyperLink, $truncatedHyperLink);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_READINGSTATE_CHANGE:
                $newReadingSateId = $this->userEvent->getNew_value();
                switch ($newReadingSateId) {
                    case ReadingStates::NOTREAD:
                        $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a marqué non lu.", $friendProfileLink, $friendName);
                        break;
                    case ReadingStates::READING:
                        $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a marqué en cours de lecture.", $friendProfileLink, $friendName);
                        break;
                    case ReadingStates::READ:
                        $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a marqué lu.", $friendProfileLink, $friendName);
                        break;
                }
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_WISHEDSTATE_CHANGE:
                $newWishedSateValue = $this->userEvent->getNew_value();
                if ($newWishedSateValue)
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a marqué comme souhaité.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            default:
                break;
        }

        $creationDate = $this->userEvent->getCreation_date()->format(__("d/m/Y", "s1b"));

        $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());

        $bookImageUrl = $userBook->getBook()->getSmallImageUrl();
        $bookLink = $userBook->getBook()->getLink();
        $bookTitle = $userBook->getBook()->getTitle();

        // Set variables
        $tplEvent->setVariables(array("userFriendImg" => $userFriendImg,
            "friendName" => $friendName,
            "resume" => $resume,
            "bookImageUrl" => $bookImageUrl,
            "friendImageTag" => $friendImageTag,
            "bookTitle" => $bookTitle,
            "creationDate" => $creationDate,
            "bookLink" => $bookLink,
            "additionalContent" => $additionalContent,
            "addSep" => $this->addSep));

        return $tplEvent->output();
    }

}
