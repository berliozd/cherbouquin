<?php
use Sb\Authentification\Service\AuthentificationSvc,
    Sb\Service\MailSvc,

    Sb\Helpers\HTTPHelper,

    Sb\Flash\Flash,
    Sb\Trace\Trace,

    Sb\Db\Dao\UserBookDao,
    Sb\Db\Dao\LendingDao,
    Sb\Db\Dao\UserEventDao,

    Sb\Db\Model\UserBook,
    Sb\Db\Model\Lending,
    Sb\Db\Model\UserEvent,

    Sb\Lending\Model\LendingState,

    Sb\View\Book as BookView,
    Sb\View\Lending as LendingView,
    Sb\View\Components\ButtonsBar,

    Sb\Entity\EventTypes;

/**
 *
 * @author Didier
 */
class Member_LendingController extends Zend_Controller_Action {

    public function init() {

        // Check if user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
    }

    public function editAction() {

        try {
            global $globalContext;

            if ($globalContext->getIsShowingFriendLibrary()) {
                Flash::addItem(__("You cannot edit a lending from a friend's library"));
                HTTPHelper::redirectToHome();
            }

            $userBookId = $_GET['ubid'];
            $userBook = UserBookDao::getInstance()->get($userBookId);

            if ($userBook) {

                // Check user is user book owner
                if ($globalContext->getConnectedUser()->getId() != $userBook->getUser()->getId()) {
                    Flash::addItem(__("Le livre que vous souhaitez éditer ne correspond pas à l'utilisateur connecté.", "share1book"));
                    HTTPHelper::redirectToLibrary();
                }

                $book = $userBook->getBook();
                $bookView = new BookView($book, false, false, false);
                $this->view->book = $bookView->get();

                $lendingView = new LendingView($userBook, $globalContext->getConnectedUser()->getId());
                $this->view->bookForm = $lendingView->get();

                $buttonsBar = new ButtonsBar(false);
                $this->view->buttonsBar = $buttonsBar->get();

            } else {
                Flash::addItem("Le livre que vous souhaitez éditer n'existe pas.");
                HTTPHelper::redirectToLibrary();
            }

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function submitAction() {

        try {
            global $globalContext;


            if ($_REQUEST['LendingType'] == "NEW") {

                $userBookId = $_POST['ubid'];

                // getting userbook lent
                $userBook = UserBookDao::getInstance()->get($userBookId);
                $userBook->setLentOnce(true);

                // getting borrower userbook (new one)
                // checking if borrower alreday have the book
                $borrowerId = $_POST['BorrowerId'];
                $userBookBorrower = UserBookDao::getInstance()->getByBookIdAndUserId($borrowerId, $userBook->getBook()->getId());
                // if not creating a new one
                if (!$userBookBorrower) {
                    $userBookBorrower = new UserBook;
                    $userBookBorrower->setCreationDate(new \DateTime);
                    $userBookBorrower->setLastModificationDate(new \DateTime);
                    $userBookBorrower->setBook($userBook->getBook());
                    $borrower = UserDao::getInstance()->get($borrowerId);
                    $userBookBorrower->setUser($borrower);
                }
                $userBookBorrower->setIs_deleted(false); // set is_deleted to false in case the borrower already had the book but deleted it in the past
                $userBookBorrower->setBorrowedOnce(true);

                // creating lending
                $lending = new Lending();
                $lending->setUserbook($userBook);
                $lending->setBorrower_userbook($userBookBorrower);
                $lending->setStartDate(new \DateTime);
                $lending->setCreationDate(new \DateTime);
                $lending->setLastModificationDate(new \DateTime);
                $lending->setState(LendingState::ACTIV);

                if (LendingDao::getInstance()->add($lending)) {
                    Trace::addItem("Lending créé avec succès.");
                    Flash::addItem(__("Les informations de prêt ont bien été mises à jour.", "s1b"));
                    try {
                        $userEvent = new UserEvent;
                        $userEvent->setNew_value($lending->getId());
                        $userEvent->setType_id(EventTypes::USER_LEND_USERBOOK);
                        $userEvent->setUser($globalContext->getConnectedUser());
                        UserEventDao::getInstance()->add($userEvent);
                    } catch (Exception $exc) {
                        Trace::addItem("erreur lors de l'ajout de l'évènement suite au prêt : " . $exc->getMessages());
                    }
                }
            } else {
                // editing a lending -> ending it
                $lendingId = $_POST["LendingId"];
                $lending = LendingDao::getInstance()->get($lendingId);

                if ($lending) {

                    // Testing if the user editing the lending is either the lender or the borrower

                    $canEditLending = false;
                    if ($lending->getUserbook() && ($lending->getUserbook()->getUser()->getId() == $globalContext->getConnectedUser()->getId()))
                        $canEditLending = true;
                    if ($lending->getBorrower_userbook() && ($lending->getBorrower_userbook()->getUser()->getId() == $globalContext->getConnectedUser()->getId()))
                        $canEditLending = true;

                    if ($canEditLending) {

                        $lending->setEndDate(new \DateTime()); // End date set to today

                        $userIsLender = ($lending->getUserbook() && ($lending->getUserbook()->getUser()->getId() == $globalContext->getConnectedUser()->getId()));
                        $userIsBorrower = ($lending->getBorrower_userbook() && ($lending->getBorrower_userbook()->getUser()->getId() == $globalContext->getConnectedUser()->getId()));
                        $isBorrowedToGuest = ($lending->getGuest());

                        if ($userIsLender) {
                            $lending->setState(LendingState::IN_ACTIVE); // user is the lender, State set to IN_ACTIVE
                        } elseif ($userIsBorrower) {
                            if (!$isBorrowedToGuest)
                                $lending->setState(LendingState::WAITING_INACTIVATION); // user is the borrower, State set to WAITING_RETURN_APPROVATION
                            else
                                $lending->setState(LendingState::IN_ACTIVE); // user is the borrower but is borrowed to a guest, State set to IN_ACTIVE
                        }

                        $lending->setLastModificationDate(new \DateTime);

                        if (LendingDao::getInstance()->update($lending)) {
                            // Send email to owner to remind him that he needs to validate the lending end
                            if ($userIsBorrower && !$isBorrowedToGuest) {
                                MailSvc::getInstance()->send($lending->getUserbook()->getUser()->getEmail(),
                                    __("Prêt en attente de retour de validation", "s1b"),
                                    $this->emailReturnValidationRequiredBody($lending->getUserbook()->getBook()->getTitle(), $lending->getBorrower_userbook()->getUser()->getUserName()));
                            }

                            Trace::addItem("Mise à jour (FIN) du lending correctement.");
                            if ($userIsBorrower && !$isBorrowedToGuest)
                                Flash::addItem(__("Les informations de prêt ont bien été mises à jour mais le retour doit être validé par le prêteur.", "share1book"));
                            else
                                Flash::addItem(__("Les informations de prêt ont bien été mises à jour.", "s1b"));
                        }
                    }
                }
            }

            HTTPHelper::redirectToLibrary();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function validateAction() {

        try {

            if (array_key_exists("lid", $_GET)) {
                $lendingId = $_GET["lid"];

                $lendingDao = LendingDao::getInstance();
                $lending = $lendingDao->GetById($lendingId);

                if ($lending) {
                    $lending->setState(LendingState::ACTIV);
                    $lending->setStartDate(new \DateTime());
                    $lending->setLastModificationDate(new \DateTime());
                    if ($lendingDao->Update($lending, $lendingId))
                        Flash::addItem(__("Le prêt à été validé.", "s1b"));
                } else
                    Flash::addItem(__("L'identifiant reçu ne correspond à aucun prêt.", "s1b"));
            } else
                Flash::addItem(__("Identifiant manquant", "s1b"));

            HTTPHelper::redirectToLibrary();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    private function emailReturnValidationRequiredBody($bookTitle, $borrowerName) {
        $mail = __("Le livre %s vient d'être rendu par votre ami %s. Vous devez valider ce retour. Vous pouvez le faire en allant directement consulter son état dans votre bibliothèque.", "share1book");
        return sprintf($mail, $bookTitle, $borrowerName);
    }

}
