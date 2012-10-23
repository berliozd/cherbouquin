<?php

namespace Sb\View;

/**
 * Description of UserBook
 *
 * @author Didier
 */
class UserBook extends \Sb\View\AbstractView {

    private $userBook;
    private $lendings;
    private $borrowings;
    private $addMode;

    function __construct(\Sb\Db\Model\UserBook $userBook, $addMode) {
        parent::__construct();
        $this->userBook = $userBook;
        $this->lendings = $userBook->getLendings();
        $this->borrowings = $userBook->getBorrowings();
        $this->addMode = $addMode;
    }

    public function get() {

        $baseTpl = "book/bookForm/userBook";

        $readingStateSvc = \Sb\Db\Service\ReadingStateSvc::getInstance();

        $readingStates = $readingStateSvc->getReadingStates();
        $readingStateOptions = "";
        if ($readingStates) {
            foreach ($readingStates as $readingState) {
                $selected = "";
                if (($this->userBook->getReadingState()) && ($readingState->getId() == $this->userBook->getReadingState()->getId())) {
                    $selected = "selected";
                }
                $readingStateOptions .= "<option value='" . $readingState->getId() . "' $selected>" . $readingState->getLabel() . "</option>";
            }
        }

        $readState = $readingStateSvc->getReadSate();

        $tpl = new \Sb\Templates\Template($baseTpl);
        $tpl->set("id", $this->userBook->getId());

        if ($this->userBook->getUser())
            $tpl->set("userid", $this->userBook->getUser()->getId());
        else
            $tpl->set("userid", "");

        if ($this->userBook->getBook())
            $tpl->set("bookid", $this->userBook->getBook()->getId());
        else
            $tpl->set("bookid", "");

        if ($this->addMode)
            $tpl->set("pictos", "");
        else
            $tpl->set("pictos", \Sb\Helpers\UserBookHelper::getStatusPictos($this->userBook));

        $tpl->set("readingStateOptions", $readingStateOptions);
        $tpl->set("review", $this->userBook->getReview());
        $rating = $this->userBook->getRating();
        if (isset($rating)) {
            $ratingCssClass = "rating-" . $rating;
        } else {
            $ratingCssClass = "no-rating";
        }
        $tpl->set("ratingCssClass", $ratingCssClass);
        $tpl->set("rating", $rating);
        if ($this->userBook->getReadingState())
            $tpl->set("noDisplay", ($this->userBook->getReadingState()->getId() != $readState->getId() ? "noDisplay" : ""));
        else
            $tpl->set("noDisplay", "noDisplay");
        $tpl->set("isBlowOfHeartChecked", ($this->userBook->getIsBlowOfHeart() ? "checked" : ""));

        if ($this->addMode) {
            $tpl->set("borrow", sprintf("<a href=\"%s\">%s</a>", "", __("Emprunter ce livre", "s1b")));
        } else {
            $tpl->set("borrow", __("", ""));
        }

        $borrowerName = "";
        $lenderName = "";
        $oneActiveLending = false;
        $oneActiveBorrowing = false;

        $lending = $this->userBook->getActiveLending();
        if ($lending) {
            $oneActiveLending = true;
            $borrowerName = $lending->getBorrower_userbook()->getUser()->getFirstName() . " " . $lending->getBorrower_userbook()->getUser()->getLastName();
        }

        $borrowing = $this->userBook->getActiveBorrowing();
        if ($borrowing) {
            $oneActiveBorrowing = true;
            if ($borrowing->getUserBook())
                $lenderName = $borrowing->getUserBook()->getUser()->getFirstName() . " " . $borrowing->getUserBook()->getUser()->getLastName();
            elseif ($borrowing->getGuest())
                $lenderName = sprintf(__("%s (invité)","s1b"), $borrowing->getGuest()->getName());
        }

        $showLending = true;
        if ($this->addMode || (!$this->userBook->getIsOwned() && !$oneActiveBorrowing))
            $showLending = false;


        $tpl->set("editLendingText", __("Prêtez ce livre", "s1b"));

        if (!$oneActiveBorrowing && !$oneActiveLending) {
            $tpl->set("lendingLabel", "");
        } else if ($oneActiveLending) {
            $tpl->set("lendingLabel", sprintf(__("Vous prêtez actuellement ce livre à %s", "s1b"), $borrowerName));
            $tpl->set("editLendingText", __("Détail", "s1b"));
        } else if ($oneActiveBorrowing) {
            $tpl->set("lendingLabel", sprintf(__("Vous empruntez actuellement ce livre à %s.", "s1b"), $lenderName));
            $tpl->set("editLendingText", __("Détail", "s1b"));
        } else {
            $tpl->set("lendingLabel", "");
        }

        $tpl->set("isOwned", ($this->userBook->getIsOwned() ? "checked" : ""));
        $tpl->set("isWished", ($this->userBook->getIsWished() ? "checked" : ""));
        $tpl->set("editLendingLink", \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::LENDING_EDIT, "ubid" => $this->userBook->getId())));


        $tpl->set("readingDate", ($this->userBook->getReadingDate() ? $this->userBook->getReadingDate()->format(__("d/m/Y", "s1b")) : ""));

        $tpl->set("hyperlink", $this->userBook->getHyperlink());

        $script = "";
        if ($readState) {
            $script = sprintf("<script>var share1bookAddABookJs = {readstate : \"%s\"}</script>", $readState->getId());
        }
        $script .= sprintf("<script src=\"%s\"></script>", $this->baseUrl . "Resources/js/addBook.js");

        // Get all the tags

        $labelCol = $this->getTagLabelCol();
        $tags = \Sb\Db\Service\TagSvc::getInstance()->getAllTags($labelCol);

        if (!$this->addMode) {
            // Get the tags assigned to the userbook
            $this->userBookTags = $this->userBook->getTags();
            $tagsExt = array_map(array($this, "isChecked"), $tags);
        }


        $tpl->setVariables(array("addMode" => $this->addMode,
            "showLending" => $showLending,
            "tags" => $tags,
            "tagsExt" => $tagsExt));

        return $script . $tpl->output();
    }

    private function isChecked($tag) {
        if ($this->userBookTags) {
            foreach ($this->userBookTags as $userBookTag) {
                if ($tag->getId() == $userBookTag->getId())
                    return new \Sb\UserBookTag\Model\UserBookTag($tag, true);
            }
        }
        return new \Sb\UserBookTag\Model\UserBookTag($tag, false);
    }

    private function getRatingCheck($inputRatingId, $currentRating) {
        if (is_numeric($currentRating) && ($currentRating == $inputRatingId)) {
            return "checked";
        }
    }

    private function getTagLabelCol() {
        switch ($_SESSION['WPLANG']) {
            case "fr_FR":
                return "label";
                break;
            case "en_US":
                return "label_en_us";
                break;
            default:
                return "label";
                break;
        }
    }

}

