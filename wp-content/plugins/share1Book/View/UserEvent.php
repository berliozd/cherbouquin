<?php

namespace Sb\View;

use \Sb\Db\Model\UserEvent as UserEventModel;
use \Sb\Entity\EventTypes;
use \Sb\Entity\ReadingStates;
use \Sb\Helpers\HTTPHelper;
use \Sb\Entity\Urls;
use \Sb\Db\Dao\UserDao;
use Sb\Db\Dao\LendingDao;

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
                $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a ajouté un livre.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_RATING_CHANGE:
                $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());
                $newRating = $this->userEvent->getNew_value();
                $resume = sprintf("<div class=\"ue-rating-label\"><a href=\"%s\" class=\"link\">%s</a> a noté.</div> <div class=\"rating rating-" . $newRating . "\"></div>", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_BLOWOFHEART_CHANGE:
                $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());
                $isBoh = $this->userEvent->getNew_value();
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a supprimé son coup de coeur.", $friendProfileLink, $friendName);
                if ($isBoh)
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a marqué comme coup de coeur.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_REVIEW_CHANGE:
                $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());
                $oldReview = $this->userEvent->getOld_value();
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a modifié son commentaire.", $friendProfileLink, $friendName);
                if ($oldReview == "")
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a ajouté un commentaire.", $friendProfileLink, $friendName);
                $additionalContent = $this->userEvent->getNew_value();
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_HYPERLINK_CHANGE:
                $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());
                $oldHyperLink = $this->userEvent->getOld_value();
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a modifié son lien hypertexte.", $friendProfileLink, $friendName);
                if ($oldHyperLink == "")
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a ajouté un lien hypertexte.", $friendProfileLink, $friendName);
                $hyperLink = "http://" . $this->userEvent->getNew_value();
                $truncatedHyperLink = \Sb\Helpers\StringHelper::tronque($hyperLink, 100);
                $additionalContent = sprintf(__("Lien : <a href=\"%s\" target=\"_blank\" class=\"link\" >%s</a>", "s1b"), $hyperLink, $truncatedHyperLink);
                $userBookRelated = true;
                break;
            case EventTypes::USERBOOK_READINGSTATE_CHANGE:
                $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());
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
                $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($this->userEvent->getItem_id());
                $newWishedSateValue = $this->userEvent->getNew_value();
                $oldWishedSateValue = $this->userEvent->getOld_value();
                if ($newWishedSateValue)
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a marqué comme souhaité.", $friendProfileLink, $friendName);
                elseif ($oldWishedSateValue)
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> ne souhaite plus.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USER_ADD_FRIEND:
                $newFriendId = $this->userEvent->getNew_value();
                if ($newFriendId == $this->getContext()->getConnectedUser()->getId())
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> est ami avec moi.", $friendProfileLink, $friendName);
                else {
                    $friendNewFriend = UserDao::getInstance()->get($newFriendId);
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> est ami avec %s.", $friendProfileLink, $friendName, $friendNewFriend->getUserName());
                }
                break;
            case EventTypes::USER_BORROW_USERBOOK:
                $lendingId = $this->userEvent->getNew_value();
                $lending = LendingDao::getInstance()->get($lendingId);
                $userBookBorrowed = $lending->getUserBook();
                $userBook = $userBookBorrowed;
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a emprunté le livre à %s.", $friendProfileLink, $friendName, $userBookBorrowed->getUser()->getUserName());
                if ($userBookBorrowed->getUser()->getId() == $this->getContext()->getConnectedUser()->getId())
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> m'a emprunté le livre.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            case EventTypes::USER_LEND_USERBOOK:
                $lendingId = $this->userEvent->getNew_value();
                $lending = LendingDao::getInstance()->get($lendingId);
                $userBookLended = $lending->getBorrower_UserBook();
                $userBook = $userBookLended;
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a prêté le livre à %s.", $friendProfileLink, $friendName, $userBookLended->getUser()->getUserName());
                if ($userBookLended->getUser()->getId() == $this->getContext()->getConnectedUser()->getId())
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> m'a prêté le livre.", $friendProfileLink, $friendName);
                $userBookRelated = true;
                break;
            default:
                break;
        }

        $creationDate = $this->userEvent->getCreation_date()->format(__("d/m/Y", "s1b"));

        if ($userBookRelated) {
            $bookImageUrl = $userBook->getBook()->getSmallImageUrl();
            $bookLink = HTTPHelper::Link($userBook->getBook()->getLink());
            $bookTitle = $userBook->getBook()->getTitle();
        }

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
            "addSep" => $this->addSep,
            "userBookRelated" => $userBookRelated));

        return $tplEvent->output();
    }

}
