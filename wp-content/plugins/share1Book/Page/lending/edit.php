<?php

use \Sb\Trace\Trace;
use \Sb\Db\Model\UserEvent;
use \Sb\Db\Dao\UserEventDao;
use \Sb\Entity\EventTypes;

Trace::addItem(\Sb\Entity\LibraryPages::LENDING_EDIT);

global $s1b;
$context = $s1b->getContext();

if ($context->getIsShowingFriendLibrary()) {
    Throw new \Sb\Exception\UserException(__("You cannot edit a lending from a friend's library"));
}

if (!$s1b->getIsSubmit()) {

    $userBookId = $_GET['ubid'];
    $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($userBookId);

    if ($userBook) {
        // Vérification de la correspondance du user
        $s1b->compareWithConnectedUserId($userBook->getUser()->getId());
        showLendingDetail($userBook, $context);
    } else {
        \Sb\Flash\Flash::addItem("Le livre que vous souhaitez éditer n'existe pas.");
        //\Sb\Helpers\HTTPHelper::redirect("");
        \Sb\Helpers\HTTPHelper::redirectToLibrary();
    }
} else {
    if ($_REQUEST['LendingType'] == "NEW") {

        $userBookId = $_POST['ubid'];

        // getting userbook lent
        $userBook = Sb\Db\Dao\UserBookDao::getInstance()->get($userBookId);
        $userBook->setLentOnce(true);

        // getting borrower userbook (new one)
        // checking if borrower alreday have the book
        $borrowerId = $_POST['BorrowerId'];
        $userBookBorrower = Sb\Db\Dao\UserBookDao::getInstance()->getByBookIdAndUserId($borrowerId, $userBook->getBook()->getId());
        // if not creating a new one
        if (!$userBookBorrower) {
            $userBookBorrower = new \Sb\Db\Model\UserBook;
            $userBookBorrower->setCreationDate(new \DateTime);
            $userBookBorrower->setLastModificationDate(new \DateTime);
            $userBookBorrower->setBook($userBook->getBook());
            $borrower = \Sb\Db\Dao\UserDao::getInstance()->get($borrowerId);
            $userBookBorrower->setUser($borrower);
        }
        $userBookBorrower->setIs_deleted(false); // set is_deleted to false in case the borrower already had the book but deleted it in the past
        $userBookBorrower->setBorrowedOnce(true);

        // creating lending
        $lending = new \Sb\Db\Model\Lending();
        $lending->setUserbook($userBook);
        $lending->setBorrower_userbook($userBookBorrower);
        $lending->setStartDate(new \DateTime);
        $lending->setCreationDate(new \DateTime);
        $lending->setLastModificationDate(new \DateTime);
        $lending->setState(\Sb\Lending\Model\LendingState::ACTIV);

        if (\Sb\Db\Dao\LendingDao::getInstance()->add($lending)) {
            Trace::addItem("Lending créé avec succès.");
            \Sb\Flash\Flash::addItem(__("Les informations de prêt ont bien été mises à jour.", "s1b"));
            try {
                $userEvent = new UserEvent;
                $userEvent->setNew_value($lending->getId());
                $userEvent->setType_id(EventTypes::USER_LEND_USERBOOK);
                $userEvent->setUser($context->getConnectedUser());
                UserEventDao::getInstance()->add($userEvent);
            } catch (Exception $exc) {
                Trace::addItem("erreur lors de l'ajout de l'évènement suite au prêt : " . $exc->getMessages());
            }
        }
    } else {
        // editing a lending -> ending it
        $lendingId = $_POST["LendingId"];
        $lending = \Sb\Db\Dao\LendingDao::getInstance()->get($lendingId);

        if ($lending) {

            // Testing if the user editing the lending is either the lender or the borrower

            $canEditLending = false;
            if ($lending->getUserbook() && ($lending->getUserbook()->getUser()->getId() == $context->getConnectedUser()->getId()))
                $canEditLending = true;
            if ($lending->getBorrower_userbook() && ($lending->getBorrower_userbook()->getUser()->getId() == $context->getConnectedUser()->getId()))
                $canEditLending = true;

            if ($canEditLending) {

                $lending->setEndDate(new \DateTime()); // End date set to today

                $userIsLender = ($lending->getUserbook() && ($lending->getUserbook()->getUser()->getId() == $context->getConnectedUser()->getId()));
                $userIsBorrower = ($lending->getBorrower_userbook() && ($lending->getBorrower_userbook()->getUser()->getId() == $context->getConnectedUser()->getId()));
                $isBorrowedToGuest = ($lending->getGuest());

                if ($userIsLender) {
                    $lending->setState(\Sb\Lending\Model\LendingState::IN_ACTIVE); // user is the lender, State set to IN_ACTIVE
                } elseif ($userIsBorrower) {
                    if (!$isBorrowedToGuest)
                        $lending->setState(\Sb\Lending\Model\LendingState::WAITING_INACTIVATION); // user is the borrower, State set to WAITING_RETURN_APPROVATION
                    else
                        $lending->setState(\Sb\Lending\Model\LendingState::IN_ACTIVE); // user is the borrower but is borrowed to a guest, State set to IN_ACTIVE
                }
                $lending->setLastModificationDate(new \DateTime);
                if (\Sb\Db\Dao\LendingDao::getInstance()->update($lending)) {
                    // Send email to owner to remind him that he needs to validate the lending end
                    if ($userIsBorrower && !$isBorrowedToGuest) {
                        $mailSvc = \Sb\Mail\Service\MailSvcImpl::getInstance();
                        $mailSvc->send($lending->getUserbook()->getUser()->getEmail(), __("Prêt en attente de retour de validation", "s1b"), emailReturnValidationRequiredBody($lending->getUserbook()->getBook()->getTitle(), $lending->getBorrower_userbook()->getUser()->getUserName()));
                    }

                    Trace::addItem("Mise à jour (FIN) du lending correctement.");
                    if ($userIsBorrower && !$isBorrowedToGuest)
                        \Sb\Flash\Flash::addItem(__("Les informations de prêt ont bien été mises à jour mais le retour doit être validé par le prêteur.", "share1book"));
                    else
                        \Sb\Flash\Flash::addItem(__("Les informations de prêt ont bien été mises à jour.", "s1b"));
                }
            }
        }
    }

    \Sb\Helpers\HTTPHelper::redirectToLibrary();
}

////////////////////////////////////////////////////////////////
function showLendingDetail(\Sb\Db\Model\UserBook $userBook, \Sb\Context\Model\Context $context) {

    $tpl = new \Sb\Templates\Template("lending");

    // Showing book detail
    // ------------------------------------------
    $book = $userBook->getBook();
    $bookView = new \Sb\View\Book($book, false, false, false);
    $tpl->set("book", $bookView->get());

    $lendingView = new \Sb\View\Lending($userBook, $context->getConnectedUser()->getId());
    $tpl->set("bookForm", $lendingView->get());

    $buttonsBar = new \Sb\View\Components\ButtonsBar(false);
    $tpl->set("buttonsBar", $buttonsBar->get());

    echo $tpl->output();
}

function emailReturnValidationRequiredBody($bookTitle, $borrowerName) {
    $mail = __("Le livre %s vient d'être rendu par votre ami %s. Vous devez valider ce retour. Vous pouvez le faire en allant directement consulter son état dans votre bibliothèque.", "share1book");
    return sprintf($mail, $bookTitle, $borrowerName);
}
