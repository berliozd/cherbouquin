<?php

namespace Sb\View;

use Sb\Db\Model\UserEvent as UserEventModel;
use Sb\Entity\EventTypes;
use Sb\Entity\ReadingStates;
use Sb\Helpers\HTTPHelper;
use Sb\Entity\Urls;
use Sb\Db\Dao\UserDao;
use Sb\Db\Dao\LendingDao;
use Sb\Helpers\StringHelper;
use Sb\Helpers\UserHelper;
use Sb\Helpers\BookHelper;

class UserEvent extends \Sb\View\AbstractView {

    private $userEvent;
    private $showOwner;

    function __construct(UserEventModel $userEvent, $showOwner) {
        $this->userEvent = $userEvent;
        $this->showOwner = $showOwner;
        parent::__construct();
    }

    public function get() {

        $globalContext = new \Sb\Context\Model\Context();

        $tplEvent = new \Sb\Templates\Template("userEvents/userEvent");

        $friend = $this->userEvent->getUser();

        $friendImg = UserHelper::getSmallImageTag($friend);
        if ($friendImg == "")
            $friendImg = UserHelper::getSmallImageTag($friend);

        $friendName = $friend->getUserName();
        $friendProfileLink = HTTPHelper::Link(Urls::USER_PROFILE, array("uid" => $friend->getId()));

        $userBookRelated = false;
        $friendRelated = false; // used for cases of new friend event
        $additionalContent = "";
        $friendId = null;
        $friendFriendImg = null;
        $friendFriendProfileLink = null;

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
                $additionalContent = StringHelper::tronque(strip_tags($this->userEvent->getNew_value()), 120);
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
                $additionalContent = sprintf(__("<a href=\"%s\" target=\"_blank\" class=\"hyperlink link\" >%s</a>", "s1b"), $hyperLink, $truncatedHyperLink);
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
                        $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> lit actuellement.", $friendProfileLink, $friendName);
                        break;
                    case ReadingStates::READ:
                        $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a lu.", $friendProfileLink, $friendName);
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
                $friendNewFriendProfileLink = null;
                $newFriendId = $this->userEvent->getNew_value();
                if ($this->getContext()->getConnectedUser() && $newFriendId == $this->getContext()->getConnectedUser()->getId()) {
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> est ami avec moi.", $friendProfileLink, $friendName);
                    $friendFriendImg = UserHelper::getXSmallImageTag($this->getContext()->getConnectedUser());
                } else {
                    $friendNewFriend = UserDao::getInstance()->get($newFriendId);
                    $friendNewFriendProfileLink = HTTPHelper::Link(Urls::USER_PROFILE, array("uid" => $friendNewFriend->getId()));
                    $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> est ami avec <a class=\"link\" href=\"%s\">%s</a>.", $friendProfileLink, $friendName, $friendNewFriendProfileLink, $friendNewFriend->getUserName());
                    $friendFriendImg = UserHelper::getXSmallImageTag($friendNewFriend);
                }
                $friendId = $newFriendId;
                $friendFriendProfileLink = $friendNewFriendProfileLink;
                $friendRelated = true;
                break;
            case EventTypes::USER_BORROW_USERBOOK:
                $lendingId = $this->userEvent->getNew_value();
                $lending = LendingDao::getInstance()->get($lendingId);
                $userBookBorrowed = $lending->getUserBook();
                $userBook = $userBookBorrowed;
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a emprunté le livre à %s.", $friendProfileLink, $friendName, $userBookBorrowed->getUser()->getUserName());
                if ($this->getContext()->getConnectedUser()) {
                    if ($userBookBorrowed->getUser()->getId() == $this->getContext()->getConnectedUser()->getId())
                        $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> m'a emprunté le livre.", $friendProfileLink, $friendName);
                }
                $userBookRelated = true;
                break;
            case EventTypes::USER_LEND_USERBOOK:
                $lendingId = $this->userEvent->getNew_value();
                $lending = LendingDao::getInstance()->get($lendingId);
                $userBookLended = $lending->getBorrower_UserBook();
                $userBook = $userBookLended;
                $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> a prêté le livre à %s.", $friendProfileLink, $friendName, $userBookLended->getUser()->getUserName());
                if ($this->getContext()->getConnectedUser()) {
                    if ($userBookLended->getUser()->getId() == $this->getContext()->getConnectedUser()->getId())
                        $resume = sprintf("<a href=\"%s\" class=\"link\">%s</a> m'a prêté le livre.", $friendProfileLink, $friendName);
                }
                $userBookRelated = true;
                break;
            default:
                break;
        }

        $creationDate = $this->userEvent->getCreation_date()->format(__("d/m/Y à H:m", "s1b"));

        $bookImageUrl = null;
        $bookLink = null;
        $bookTitle = null;
        $bookAuthor = null;
        $bookId = null;
        $bookImgTag = null;
        if ($userBookRelated) {
            $bookImageUrl = $userBook->getBook()->getSmallImageUrl();
            $bookImgTag = BookHelper::getSmallImageTag($userBook->getBook(), $this->getContext()->getDefaultImage());
            $bookLink = HTTPHelper::Link($userBook->getBook()->getLink());
            $bookTitle = $userBook->getBook()->getTitle();
            $bookAuthor = $userBook->getBook()->getOrderableContributors();
            $bookId = $userBook->getBook()->getId();
        }


        $showAddButton = false;
        if ($globalContext->getConnectedUser())
            $showAddButton = true;

        // Set variables
        $tplEvent->setVariables(array("friendImg" => $friendImg,
            "friendName" => $friendName,
            "resume" => $resume,
            "bookImageUrl" => $bookImageUrl,
            "bookImgTag" => $bookImgTag,
            "friendProfileLink" => $friendProfileLink,
            "friendId" => $friendId,
            "bookTitle" => $bookTitle,
            "bookId" => $bookId,
            "bookAuthor" => $bookAuthor,
            "creationDate" => $creationDate,
            "bookLink" => $bookLink,
            "additionalContent" => $additionalContent,
            "userBookRelated" => $userBookRelated,
            "userFriendRelated" => $friendRelated,
            "friendFriendImg" => $friendFriendImg,
            "friendFriendProfileLink" => $friendFriendProfileLink,
            "showOwner" => $this->showOwner,
            "showAddButton" => $showAddButton
        ));

        return $tplEvent->output();
    }

}
