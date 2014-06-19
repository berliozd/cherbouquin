<?php
use Sb\Db\Dao\UserBookDao,
    Sb\Db\Dao\ReadingStateDao,
    Sb\Db\Dao\TagDao,

    Sb\Db\Service\UserEventSvc,

    Sb\Helpers\HTTPHelper,
    Sb\Helpers\ArrayHelper,

    Sb\Flash\Flash,
    Sb\Trace\Trace,

    Sb\View\UserBook as UserBookView,
    Sb\View\Book as BookView,
    Sb\View\Components\ButtonsBar,

    Sb\Authentification\Service\AuthentificationSvc,

    Sb\Entity\Urls,

    Sb\Form\UserBook as UserBookForm;

/**
 *
 * @author Didier
 */
class Member_UserBookController extends Zend_Controller_Action {

    public function init() {

        // Check if user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
    }

    public function editAction() {

        try {

            global $globalContext;

            $idUserBook = $_GET['ubid'];
            /* @var $userBook \Sb\Db\Model\UserBook */
            $userBook = UserBookDao::getInstance()->get($idUserBook);

            if ($userBook) {

                // On vérifit la correspondance du user
                if ($globalContext->getConnectedUser()->getId() != $userBook->getUser()->getId()) {
                    Flash::addItem(__("Le livre que vous souhaitez éditer ne correspond pas à l'utilisateur connecté.", "share1book"));
                    HTTPHelper::redirectToLibrary();
                }

                $book = $userBook->getBook();

                $this->view->action = "/" . Urls::USER_BOOK_SUBMIT;

                $bookView = new BookView($book, false, false, false);
                $this->view->book = $bookView->get();

                $userBookView = new UserBookView($userBook, false);
                $this->view->bookForm = $userBookView->get();

                $buttonsBar = new ButtonsBar(true, __("Mettre à jour", "s1b"));
                $this->view->buttonsBar = $buttonsBar->get();

                $referer = HTTPHelper::getReferer();
                $this->view->referer = $referer;

            } else {
                Flash::addItem(__("Le livre que vous souhaitez éditer n'existe pas.", "s1b"));
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

            // getting form data
            $userBookForm = new UserBookForm($_POST);

            // getting userbook in DB
            $userBook = UserBookDao::getInstance()->get($userBookForm->getId());

            // Getting the events related to the userbook changes
            $userEvents = UserEventSvc::getInstance()->prepareUserBookEvents($userBook, $userBookForm);

            // On vérifit la correspondance du user
            if ($globalContext->getConnectedUser()->getId() != $userBook->getUser()->getId()) {
                Flash::addItem(__("Le livre que vous souhaitez éditer ne correspond pas à l'utilisateur connecté.", "share1book"));
                HTTPHelper::redirectToLibrary();
            }

            // updating userbook members
            $userBook->setReview($userBookForm->getReview());
            $userBook->setIsBlowOfHeart($userBookForm->getIsBlowOfHeart());
            $userBook->setIsOwned($userBookForm->getIsOwned());
            $userBook->setIsWished($userBookForm->getIsWished());
            $userBook->setRating($userBookForm->getRating());
            $userBook->setNb_of_pages($userBookForm->getNb_of_pages());
            $userBook->setNb_of_pages_read($userBookForm->getNb_of_pages_read());

            $readingState = ReadingStateDao::getInstance()->get($userBookForm->getReadingStateId());
            if ($userBookForm->getReadingDate())
                $userBook->setReadingDate($userBookForm->getReadingDate());
            $userBook->setReadingState($readingState);
            $userBook->setHyperlink($userBookForm->getHyperLink());

            if ($userBookForm->getTags()) {
                $tags = new \Doctrine\Common\Collections\ArrayCollection();
                foreach ($userBookForm->getTags() as $tagId) {
                    $tag = TagDao::getInstance()->get($tagId);
                    $tags->add($tag);
                }
                $userBook->setTags($tags);
            }

            // Mise à jour du UserBook
            if (UserBookDao::getInstance()->update($userBook)) {

                // persisting the userevent related to the userbook changes
                UserEventSvc::getInstance()->persistAll($userEvents);

                Flash::addItem(sprintf(__('Le livre "%s" a été mis à jour.', "s1b"), urldecode($userBook->getBook()->getTitle())));
            } else
                Flash::addItem(__('Une erreur s\'est produite lors de la mise à jour de votre fiche de lecture','s1b'));

            $referer = ArrayHelper::getSafeFromArray($_POST, "referer", null);
            if ($referer)
                HTTPHelper::redirectToUrl($referer);
            else
                HTTPHelper::redirectToLibrary();


        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

}
