<?php
use Sb\Db\Model\Book,
    Sb\Db\Dao\UserBookDao,
    Sb\Db\Dao\ReadingStateDao,
    Sb\Db\Dao\TagDao,
    Sb\Db\Dao\BookDao,

    Sb\Db\Service\UserEventSvc,

    Sb\Helpers\HTTPHelper,
    Sb\Helpers\ArrayHelper,
    Sb\Helpers\BookHelper,

    Sb\Flash\Flash,
    Sb\Trace\Trace,

    Sb\View\UserBook as UserBookView,
    Sb\View\Book as BookView,
    Sb\View\Components\ButtonsBar,

    Sb\Authentification\Service\AuthentificationSvc,

    Sb\Entity\Urls,
    Sb\Entity\Constants,
    Sb\Entity\LibraryPages,

    Sb\Form\UserBook as UserBookForm,

    Sb\Cache\ZendFileCache,

    Sb\Db\Mapping\BookMapper;

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
                Flash::addItem(__('Une erreur s\'est produite lors de la mise à jour de votre fiche de lecture', 's1b'));

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

    public function deleteAction() {

        try {
            global $globalContext;

            if ($globalContext->getIsShowingFriendLibrary())
                Flash::addItem(__("Vous ne pouvez pas supprimer le livre d'un ami.", "s1b"));
            else {

                $userBook = UserBookDao::getInstance()->get($_GET['ubid']);
                if ($userBook) {
                    if ($userBook->getUser()->getId() != $globalContext->getConnectedUser()->getId())
                        Flash::addItem(__("Vous ne pouvez pas supprimer un livre qui ne vous appartient pas.", "s1b"));
                    else {
                        if ($userBook->getActiveLending() || $userBook->getActiveborrowing())
                            Flash::addItem(sprintf(__("Le livre \"%s\" ne peut pas être supprimé de votre bibliothèque car il est associé à un prêt en cours.", "share1book"), $userBook->getBook()->getTitle()));
                        else {
                            UserBookDao::getInstance()->delete($userBook);
                            Flash::addItem(sprintf(__("Le livre \"%s\" a été supprimé de votre bibliothèque.", "s1b"), $userBook->getBook()->getTitle()));
                        }
                    }
                } else
                    Flash::addItem(__("Le livre que vous souhaitez supprimer n'existe pas.", "s1b"));
            }

            $referer = HTTPHelper::getReferer();
            if ($referer)
                HTTPHelper::redirectToUrl($referer);
            else
                HTTPHelper::redirectToLibrary();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Store book to add in cache and redirect to correct page
     */
    public function prepareAddAction() {

        try {
            global $globalContext;

            // checking if book is already in DB
            $isBookInDb = false;
            $bookInUserLib = false;

            if ($globalContext->getIsShowingFriendLibrary())
                Flash::addItem(__("Vous ne pouvez pas ajouter un livre à la bibliothèque d'un ami.", "s1b"));

            $destination = HTTPHelper::Link(Urls::USER_LIBRARY_DETAIL, array("page" => LibraryPages::USERBOOK_ADDCHOICE), false, false);
            if (ArrayHelper::getSafeFromArray($_POST, LibraryPages::LENDING_BORROWFROMFRIENDS, null))
                $destination = HTTPHelper::Link(Urls::USER_LIBRARY_DETAIL, array("page" => LibraryPages::LENDING_BORROWFROMFRIENDS), false, false);

            // Remove book to add in cache
            ZendFileCache::getInstance()->remove(Constants::BOOK_TO_ADD_PREFIX . session_id());

            // Get Book from POST
            $book = new Book();
            BookMapper::map($book, $_POST, "book_");

            if ($book->getId()) {
                $isBookInDb = true;
            } else {
                $bookInDb = BookDao::getInstance()->getOneByCodes($book->getISBN10(), $book->getISBN13(), $book->getASIN());
                if ($bookInDb) {
                    $isBookInDb = true;
                    $book = $bookInDb;
                }
            }

            // Si le livre existe déjà en base
            // Vérification de l'existence du livre pour l'utilisateur
            // et si oui redirection vers la page d'édition
            if ($isBookInDb) {

                $userBook = UserBookDao::getInstance()->getByBookIdAndUserId($globalContext->getConnectedUser()->getId(), $book->getId());
                if ($userBook && !$userBook->getIs_deleted()) {

                    $bookInUserLib = true;

                    // If the user is trying to borrow the book we display a flash message
                    if (ArrayHelper::getSafeFromArray($_POST, LibraryPages::LENDING_BORROWFROMFRIENDS, null))
                        Flash::addItem(__("Vous avez déjà ce livre dans votre bibliothèque.", "s1b"));
                }
            }

            // On complète les infos qui manquent éventuellement
            if (!$book->IsComplete()) {
                Trace::addItem('Requêtage de Google.');
                BookHelper::completeInfos($book);
            }

            if (!$book->IsValid()) {
                Flash::addItem('Il manque certaines données pour ajouter ce livre à notre base de données.');
                HTTPHelper::redirectToReferer();
            } else
                ZendFileCache::getInstance()->save($book, Constants::BOOK_TO_ADD_PREFIX . session_id());

            if ($isBookInDb) {
                if ($bookInUserLib) {
                    HTTPHelper::redirect($book->getLink());
                } else {
                    HTTPHelper::redirect($destination);
                }
            } else
                HTTPHelper::redirect($destination);


        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }
}
