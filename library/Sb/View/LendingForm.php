<?php

namespace Sb\View;

/**
 * Description of UserBook
 *
 * @author Didier
 */
class LendingForm extends \Sb\View\AbstractView {

    private $activeLending;
    private $activeBorrowing;
    private $book;
    private $userBook;
    private $connectedUserId;

    function __construct($lendings, $borrowings, $userBook, $connectedUserId) {
        parent::__construct();

        $this->connectedUserId = $connectedUserId;
        $this->userBook = $userBook;
        $this->book = $userBook->getBook();

        foreach ($lendings as $lending) {
            if (($lending->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE))
                $this->activeLending = $lending;
        }
        foreach ($borrowings as $borrowing) {
            if (($borrowing->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE))
                $this->activeBorrowing = $borrowing;
        }
    }

    public function get() {

        $baseTpl = "book/bookForm/lending/lendingForm";
        $tpl = new \Sb\Templates\Template($baseTpl, $this->baseDir);
        $tpl->set("bookId", $this->book->getId());
        $tpl->set("ubid", $this->userBook->getId());

        // Préparation de la zone de formulaire
        // ------------------------------------

        if ($this->activeLending) {

            $this->setTemplateFormFields($tpl, $this->activeLending->getId(), "CURRENT", "");

            $startDate = $this->activeLending->getStartDate()->format(__("d/m/Y", "s1b"));
            $borrowerName = $this->activeLending->getBorrower_userbook()->getUser()->getFirstName() . " " . $this->activeLending->getBorrower_userbook()->getUser()->getLastName();
            $lendingText = sprintf(__("Vous prêtez actuellement ce livre à %s depuis le %s", "s1b"), $borrowerName, $startDate);
            $button1Text = __("Terminer le prêt", "s1b");
            switch ($this->activeLending->getState()) {
                case \Sb\Lending\Model\LendingState::WAITING_INACTIVATION:
                    $warningText = __("En attente de validation de retour de votre part.", "s1b");
                    break;
                default:
                    $warningText = "";
                    break;
            }
        } elseif ($this->activeBorrowing) {

            $this->setTemplateFormFields($tpl, $this->activeBorrowing->getId(), "CURRENT", "");

            $startDate = $this->activeBorrowing->getStartDate()->format(__("d/m/Y", "s1b"));

            if ($this->activeBorrowing->getUserBook())
                $lenderName = $this->activeBorrowing->getUserBook()->getUser()->getFirstName() . " " . $this->activeBorrowing->getUserBook()->getUser()->getLastName();
            elseif ($this->activeBorrowing->getGuest())
                $lenderName = sprintf("%s (invité)", $this->activeBorrowing->getGuest()->getName());

            $lendingText = sprintf(__("Vous empruntez actuellement ce livre à %s depuis le %s", "s1b"), $lenderName, $startDate);
            switch ($this->activeBorrowing->getState()) {
                case \Sb\Lending\Model\LendingState::WAITING_INACTIVATION:
                    $warningText = __("En attente de validation de retour de la part du prêteur.", "s1b");
                    break;
                default:
                    $button1Text = __("Terminer le prêt", "s1b");
                    $warningText = "";
                    break;
            }
        } elseif ($this->userBook->getIsOwned()) {

            $this->setTemplateFormFields($tpl, "", "NEW");

            $user = \Sb\Db\Dao\UserDao::getInstance()->get($this->connectedUserId);
            $userFriends = $user->getAcceptedFriends();

            $options = "";
            // si l'user a des amis, construire le liste des options
            $oneFriendAtLeast = false;
            if ($userFriends) {
                foreach ($userFriends as $userFriend) {
                    $oneFriendAtLeast = true;
                    $options .= "<option value=" . $userFriend->getId() . ">" .
                            $userFriend->getFirstName() . " " . $userFriend->getLastName() .
                            "</option>";
                }
            }
            if (!$oneFriendAtLeast) {
                $lendingText = __("Vous n'avez pas encore d'amis.", "s1b");
            } else {
                $friendSelection = sprintf(__("Vous souhaitez prêter ce livre à <select name=\"BorrowerId\">%s</select><input type=\"hidden\" name=\"State\" value=\"1\" />", "s1b"), $options);
                $button1Text = __("Démarrer le prêt", "s1b");
            }
        } else {

            $this->setTemplateFormFields($tpl, "", "");
            $lendingText = __("Vous ne pouvez pas prêter ce livre car vous ne le possédez pas.", "s1b");
        }

        $tpl->setVariables(array("lendingText" => $lendingText,
            "warningText" => $warningText,
            "button1Text" => $button1Text,
            "friendSelection" => $friendSelection));

        return $tpl->output();
    }

    private function setTemplateFormFields(&$tpl, $lendingId, $lendingType) {
        $tpl->set("lendingId", $lendingId);
        $tpl->set("lendingType", $lendingType);
    }

}