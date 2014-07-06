<?php
use Sb\Db\Model\Book,
    Sb\Db\Dao\UserBookDao,
    Sb\Db\Dao\ReadingStateDao,
    Sb\Db\Dao\TagDao,
    Sb\Db\Dao\BookDao,
    Sb\Db\Dao\UserDao,
    Sb\Db\Dao\InvitationDao,
    Sb\Db\Dao\GuestDao,
    Sb\Db\Dao\LendingDao,
    Sb\Db\Dao\UserEventDao,

    Sb\Db\Model\UserBook,
    Sb\Db\Model\Guest,
    Sb\Db\Model\Invitation,
    Sb\Db\Model\Lending,
    Sb\Db\Model\UserEvent,

    Sb\Service\BookPageSvc,

    Sb\Db\Service\UserEventSvc,

    Sb\Helpers\HTTPHelper,
    Sb\Helpers\ArrayHelper,
    Sb\Helpers\BookHelper,
    Sb\Helpers\StringHelper,

    Sb\Flash\Flash,
    Sb\Trace\Trace,

    Sb\View\UserBook as UserBookView,
    Sb\View\Book as BookView,
    Sb\View\Components\ButtonsBar,

    Sb\Authentification\Service\AuthentificationSvc,
    Sb\Service\MailSvc,

    Sb\Entity\Urls,
    Sb\Entity\Constants,
    Sb\Entity\EventTypes,

    Sb\Lending\Model\LendingState,

    Sb\Form\UserBook as UserBookForm,
    Sb\Form\Book as BookForm,

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

            $destination = HTTPHelper::Link(Urls::USER_BOOK_ADD_CHOICE, null, false, false);
            if (ArrayHelper::getSafeFromArray($_POST, Constants::BORROW_FROM_FRIENDS, null))
                $destination = HTTPHelper::Link(Urls::USER_BOOK_BORROW_FROM_FRIENDS, null, false, false);

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
                    if (ArrayHelper::getSafeFromArray($_POST, Constants::BORROW_FROM_FRIENDS, null))
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
                if ($bookInUserLib)
                    HTTPHelper::redirectToUrl(HTTPHelper::Link($book->getLink()));
                else
                    HTTPHelper::redirectToUrl($destination);
            } else
                HTTPHelper::redirectToUrl($destination);


        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function displayAddingChoiceAction() {

        try {

            global $globalContext;

            if ($globalContext->getIsShowingFriendLibrary())
                Flash::addItem(__("Vous ne pouvez pas ajouter un livre à la bibliothèque d'un ami.", "s1b"));

            // Récupération du Book depuis le cache
            $book = ZendFileCache::getInstance()->load(Constants::BOOK_TO_ADD_PREFIX . session_id());

            // If id is known, getting the book from db to have all associated members and userbooks to show the potential reviews
            $booksAlsoLiked = null;
            $booksWithSameTags = null;
            $reviewdUserBooks = null;
            if ($book->getId()) {
                $book = BookDao::getInstance()->get($book->getId());
                $bookPage = BookPageSvc::getInstance()->get($book->getId());
                $booksAlsoLiked = $bookPage->getBooksAlsoLiked();
                $booksWithSameTags = $bookPage->getBooksWithSameTags();
                $reviewdUserBooks = $bookPage->getReviewedUserBooks();
            }

            $bookView = new BookView($book, true, true, true, $booksAlsoLiked, $booksWithSameTags, $reviewdUserBooks);
            $this->view->book = $bookView->get();

            $buttonsBar = new ButtonsBar(false);
            $this->view->buttonsBar = $buttonsBar->get();

            $this->view->referer = HTTPHelper::getReferer();
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function importAction() {

        try {

            $tpl = new \Sb\Templates\Template("import");
            $tpl->set("summary", __("Merci de sélectionner un fichier au format CSV, le séparateur de colonne devant être \",\" et le code ISBN des livres sur 10 caractères devant apparaître dans la première colonne.", "share1book"));
            echo $tpl->output();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function submitImportAction() {

        try {

            global $globalContext;
            global $globalConfig;

            $tpl = new \Sb\Templates\Template("import");

            $isbnsInFile = $this->getIsbnsInFile($globalContext);

            if ($isbnsInFile) {

                if (count($isbnsInFile) > $globalConfig->getMaxImportNb()) {
                    Flash::addItem(sprintf(__("Il n'est pas possible d'importer plus de %s livres.", "s1b"), $globalConfig->getMaxImportNb()));
                } else {

                    //\Sb\Flash\Flash::addItem(count($isbnsInFile) . " ont été soumis dans votre fichier d'import.");
                    Flash::addItem(sprintf(__("%s livre(s) ont été soumis dans votre fichier d'import.", "s1b"), count($isbnsInFile)));

                    /// RECHERCHE DES LIVRES (base et amazon)
                    $resultingBooks = $this->searchBooks($isbnsInFile, $globalConfig);

                    /// AJOUT DES LIVRES dans la base
                    $booksAlReadyImported = array();
                    $booksCorrectlyImported = array();
                    $this->loadBooks($resultingBooks, $booksAlReadyImported, $booksCorrectlyImported, $globalContext);

                    // MET A JOUR le résumé de l'import
                    $this->updateSummary($isbnsInFile, $tpl, $booksAlReadyImported, $booksCorrectlyImported, $globalContext);
                }
            } else {
                $tpl->set("summary", "");
                Flash::addItem(__("Aucun ISBN n'a pu être lu dans le fichier soumit.", "s1b"));
            }

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function borrowFromFriendsAction() {

        try {

            global $globalContext;

            $bookIdInIQS = ArrayHelper::getSafeFromArray($_GET, "bid", null);
            if ($bookIdInIQS)
                $book = BookDao::getInstance()->get($bookIdInIQS);
            else
                // Get Book to add from cache
                $book = ZendFileCache::getInstance()->load(Constants::BOOK_TO_ADD_PREFIX . session_id());

            if ($book) {

                $userBookInDb = UserBookDao::getInstance()->getByBookIdAndUserId($globalContext->getConnectedUser()->getId(), $book->getId());
                if ($userBookInDb && !$userBookInDb->getIs_deleted()) {

                    Flash::addItem(__("Vous avez déjà ce livre dans votre bibliothèque.", "s1b"));
                    HTTPHelper::redirectToLibrary();

                } else {

                    // Checking if a friend has this book in his library
                    $userBookDao = UserBookDao::getInstance();

                    $user = $globalContext->getConnectedUser();
                    $friends = $user->getAcceptedFriends();
                    if ($friends) {
                        $userBooks = $userBookDao->getBookInFriendsUserBook($book->getId(), $globalContext->getConnectedUser()->getId());
                        if ($userBooks)
                            $this->view->friendUserBooks = array_filter($userBooks, array(&$this, "IsBorrowable"));
                    }

                    $bookView = new \Sb\View\Book($book, false, false, true, null, null, null, false);
                    $this->view->book = $bookView->get();
                }

            } else
                HTTPHelper::redirectToHome();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function borrowFromGuestAction() {

        try {

            global $globalContext;

            if ($this->validateUserInputForm()) {

                $bookForm = new BookForm($_POST);

                // testing if book can be found in db by id
                if ($bookForm->getId())
                    $bookInDb = BookDao::getInstance()->get($bookForm->getId());

                // testing if book can be found in db by isbn10, isbn13, asin
                if (!$bookInDb)
                    $bookInDb = BookDao::getInstance()->getOneByCodes($bookForm->getISBN10(), $bookForm->getISBN13(), $bookForm->getASIN());

                // getting the book data from post and adding to db
                if (!$bookInDb) {
                    // Récupération du Book depuis le POST
                    $bookInDb = new Book();
                    BookMapper::map($bookInDb, $_POST, "book_");
                    // book not in db : need to add it
                    $bookInDb->setCreationDate(new \DateTime());
                    $bookInDb->setLastModificationDate(new \DateTime());
                    BookDao::getInstance()->add($bookInDb);
                }

                if ($bookInDb) {

                    $guestName = ArrayHelper::getSafeFromArray($_POST, "guest_name", null);
                    $guestEmail = ArrayHelper::getSafeFromArray($_POST, "guest_email", null);

                    $guest = new Guest;
                    $guest->setName($guestName);
                    $guest->setEmail($guestEmail);
                    $guest->setCreation_date(new \DateTime);

                    if ($guestEmail) {

                        $friendToBorrowInDb = UserDao::getInstance()->getByEmail($guestEmail);

                        if ($friendToBorrowInDb) {

                            Flash::addItem(sprintf(__("Un utilisateur existe déjà avec l'email que vous avez saisi. Nous vous proposons de lui envoyer une demande d'ami. Vous pourrez ensuite lui emprunter ce livre directement depuis sa bibliothèque. <a class=\"link\" href=\"%s\">Envoyer une demande d'ami</a>", "s1b"), HTTPHelper::Link(Urls::USER_FRIENDS_REQUEST, array("fid" => $friendToBorrowInDb->getId()))));
                            HTTPHelper::redirectToReferer();

                        } else {
                            $token = sha1(uniqid(rand()));

                            // Send invite email
                            $message = __(sprintf("%s vous invite à rejoindre %s, réseau communautaire autour du livre et de la lecture.", sprintf("%s %s", $globalContext->getConnectedUser()->getFirstName(), $globalContext->getConnectedUser()->getLastName()), $_SERVER["SERVER_NAME"]), "s1b");
                            $message .= "<br/><br/>";
                            $message .= sprintf(__("Il a utilisé %s pour noter qu'il vous a emprunté \"%s\"."), Constants::SITENAME, $bookInDb->getTitle());
                            $message .= "<br/><br/>";
                            $message .= __("Venez échanger sur vos lectures et coups de coeur, chercher l'inspiration grâce aux recommandations, gérer et partager votre bibliothèque avec vos amis et trouver dans leurs listes d'envies des idées de cadeaux.");
                            $message .= "<br/><br/>";
                            $subscriptionLink = HTTPHelper::Link(Urls::SUBSCRIBE);
                            $refuseInvitationLink = HTTPHelper::Link(Urls::REFUSE_INVITATION, array("Token" => $token, "Email" => $guestEmail));
                            $message .= sprintf(__("L'inscription est gratuite ! Rejoignez-nous... <a href=\"%s\">S'inscrire</a> ou <a href=\"%s\">Refuser l'invitation</a>"), $subscriptionLink, $refuseInvitationLink);
                            $message .= "<br/><br/>";
                            $message .= sprintf(__("<strong>L'équipe Cherbouquin</strong>", "s1b"), Constants::SITENAME);
                            MailSvc::getInstance()->send($guestEmail, sprintf(__("Invitation à rejoindre %s", "s1b"), Constants::SITENAME), $message);

                            // Create invitation in DB
                            $invitation = new Invitation;
                            $invitation->setSender($globalContext->getConnectedUser());
                            $invitation->setGuest($guest);
                            $invitation->setCreation_date(new \DateTime);
                            $invitation->setToken($token);

                            InvitationDao::getInstance()->add($invitation);
                            Flash::addItem(__("Un email d'invitation a été envoyé à votre ami.", "s1b"));
                        }
                    } else
                        GuestDao::getInstance()->add($guest);

                    // Testing if the user has the book in his lib but marked as deleted
                    $userBookBorrower = UserBookDao::getInstance()->getByBookIdAndUserId($globalContext->getConnectedUser()->getId(), $bookInDb->getId());
                    if ($userBookBorrower && $userBookBorrower->getIs_deleted()) {
                        $userBookBorrower->setIs_deleted(false);
                        $userBookBorrower->setLastModificationDate(new \DateTime);
                        UserBookDao::getInstance()->update($userBookBorrower);
                        Flash::addItem(sprintf(__("Vous aviez déjà le livre \"%s\" dans votre bibliothèque mais l'aviez supprimé. Il a été rajouté.", "s1b"), $bookInDb->getTitle()));
                    } else {
                        // Create userbook for connected user
                        $userBookBorrower = new UserBook;
                        $userBookBorrower->setUser($globalContext->getConnectedUser());
                        $userBookBorrower->setBook($bookInDb);
                        $userBookBorrower->setCreationDate(new \DateTime());
                        $userBookBorrower->setBorrowedOnce(true);
                        UserBookDao::getInstance()->add($userBookBorrower);
                        Flash::addItem(__("Le livre a été ajouté à votre bibliothèque.", "s1b"));
                    }

                    $lending = new Lending;
                    $lending->setBorrower_userbook($userBookBorrower);
                    $lending->setGuest($guest);
                    $lending->setCreationDate(new \DateTime());
                    $lending->setState(LendingState::ACTIV);
                    $lending->setStartDate(new \DateTime());
                    LendingDao::getInstance()->add($lending);
                }
                HTTPHelper::redirectToLibrary();
            } else
                HTTPHelper::redirectToReferer();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function borrowAction() {

        try {
            global $globalContext;

            $idUserBook = $_GET['ubid'];

            if ($idUserBook) {

                $userBook = UserBookDao::getInstance()->get($idUserBook);

                if ($userBook) {
                    $bookId = $userBook->getBook()->getId();

                    // We check that the userbook we want to bororow is really owned by a friend
                    $userBookCheck = UserBookDao::getInstance()->getBookInFriendsUserBook($bookId, $globalContext->getConnectedUser()->getId());
                    if ($userBookCheck) {

                        // We check if the book is owned by the user we want to borrow the book from
                        if ($userBook->getIsOwned()) {
                            // We check that the book is not currently lent (no lending or an inactive lending)
                            if (!$userBook->getActiveLending()) {

                                $existingUserBook = UserBookDao::getInstance()->getByBookIdAndUserId($globalContext->getConnectedUser()->getId(), $bookId);

                                // We check that the connect user doesn't already have the book
                                if ($existingUserBook) {
                                    // the user already had that book but had deleted it
                                    if ($existingUserBook->getIs_deleted()) {
                                        $newUserBook = $existingUserBook;
                                        $newUserBook->setIs_deleted(false);
                                        $newUserBook->setLastModificationDate(new \DateTime());
                                        $newUserBook->setBorrowedOnce(true);
                                        $newUserBookPersisted = UserBookDao::getInstance()->update($newUserBook);
                                        Flash::addItem(__("Vous aviez déjà ce livre dans votre bibliothèque mais l'aviez supprimé.", "s1b"));
                                    } else {
                                        Flash::addItem(__("Vous avez déjà ce livre dans votre bibliothèque.", "s1b"));
                                        // Redirect to the main library page
                                        HTTPHelper::redirectToLibrary();
                                    }
                                } else {
                                    // We create a userbook for the connected user
                                    $newUserBook = new UserBook;
                                    $newUserBook->setBook($userBook->getBook());
                                    $newUserBook->setCreationDate(new \DateTime());
                                    $newUserBook->setLastModificationDate(new \DateTime());
                                    $newUserBook->setUser($globalContext->getConnectedUser());
                                    $newUserBook->setBorrowedOnce(true);
                                    $newUserBookPersisted = UserBookDao::getInstance()->add($newUserBook);
                                }

                                if ($newUserBookPersisted) {

                                    // update lent userbook with Lent Once = 1
                                    $userBook->setLentOnce(true);
                                    UserBookDao::getInstance()->update($userBook);

                                    // Lending line creation
                                    $lending = new Lending;
                                    $lending->setUserbook($userBook);
                                    $lending->setBorrower_userbook($newUserBook);
                                    $lending->setStartDate(new \DateTime());
                                    $lending->setState(LendingState::ACTIV);
                                    $lendingId = LendingDao::getInstance()->Add($lending);
                                    // if ok : prepare flash message
                                    if ($lendingId) {
                                        try {
                                            $userEvent = new UserEvent;
                                            $userEvent->setNew_value($lending->getId());
                                            $userEvent->setType_id(EventTypes::USER_BORROW_USERBOOK);
                                            $userEvent->setUser($globalContext->getConnectedUser());
                                            UserEventDao::getInstance()->add($userEvent);
                                        } catch (Exception $exc) {
                                            Trace::addItem("erreur lors de l'ajout de l'évènement suite au prêt : " . $exc->getMessages());
                                        }
                                        Flash::addItem(sprintf(__("Le livre %s a été emprunté à %s et ajouté à votre bibliothèque.", "sharebook"), $userBook->getBook()->getTitle(), $userBook->getUser()->getFirstName() . " " . $userBook->getUser()->getLastName()));
                                    }
                                }
                            } else {
                                Flash::addItem(__("Ce livre fait l'objet d'un prêt en cours", "s1b"));
                            }
                        } else {
                            Flash::addItem(__("Ce livre n'est pas possédé par l'utilisateur à qui vous tentez d'emprunter ce livre.", "s1b"));
                        }
                    } else {
                        Flash::addItem(__("Vous n'êtes pas amis avec le propriétaire de ce livre.", "s1b"));
                    }
                } else {
                    Flash::addItem(__("Le livre que vous voulez emprunter n'existe pas dans la base.", "s1b"));
                }
            }

            // Redirect to the main library page
            HTTPHelper::redirectToLibrary();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    // Function for import action

    private function updateSummary($isbnsInFile, &$tpl, $booksAlReadyImported, $booksCorrectlyImported, \Sb\Context\Model\Context $context) {
        $alreadyImported = "";
        if (count($booksAlReadyImported) > 0)
            $alreadyImported = $this->showTableSummary(sprintf(__("%s livre(s) déjà présents dans votre bibliothèque", "s1b"),
                count($booksAlReadyImported)), $booksAlReadyImported, $context);

        $correctlyImported = "";
        if (count($booksCorrectlyImported) > 0)
            $correctlyImported = $this->showTableSummary(sprintf(__("%s livre(s) correctement ajoutés à votre bibliothèque", "s1b"),
                count($booksCorrectlyImported)), $booksCorrectlyImported, $context);
        $notImported = "";

        $isbnCorrectlyImported = array_map(array(&$this, "getIsbnFromBook"), $booksCorrectlyImported);
        $isbnAlreadyImported = array_map(array(&$this, "getIsbnFromBook"), $booksAlReadyImported);
        $isbnOK = array_merge($isbnAlreadyImported, $isbnCorrectlyImported);
        $isbnsNotImported = array_diff($isbnsInFile, $isbnOK);
        if (count($isbnsNotImported) > 0)
            $notImported = $this->showNotImported($isbnsNotImported, $context);


        $tpl->set("summary", $correctlyImported . $alreadyImported . $notImported);
    }

    private function loadBooks($resultingBooks, &$booksAlReadyImported, &$booksCorrectlyImported, \Sb\Context\Model\Context $context) {

        if ($resultingBooks) {

            $bookDao = \Sb\Db\Dao\BookDao::getInstance();

            foreach ($resultingBooks as $book) {

                if (!$book->getId()) { // il faut créer le Book
                    \Sb\Trace\Trace::addItem("Création du livre : " . $book->getISBN10() . "(" . $book->getTitle() . ")");
                    $bookId = $bookDao->Add($book);
                    if ($bookId) { // le Book a été créé correctement
                        \Sb\Trace\Trace::addItem("Création OK");
                        if ($this->addUserBook($bookId, $context)) {
                            $booksCorrectlyImported[] = $book;
                        }
                    } else {
                        \Sb\Trace\Trace::addItem("NOT Création OK");
                    }
                } else {
                    // Le Book existe déjà, il y a des chances qui soit déjà affecté au user et il faut donc vérifier
                    $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();
                    $userBook = new \Sb\Db\Model\UserBook();
                    $userBook = $userBookDao->getByBookIdAndUserId($context->getConnectedUser()->getId(), $book->getId());
                    if ($userBook) { // le userBook existe déjà pour le user connecté
                        \Sb\Trace\Trace::addItem("Le livre " . $book->getISBN10() . " - " . $book->getTitle() . " existe déjà pour cet utilisateur.");
                        $booksAlReadyImported[] = $book;
                    } else { // on peut créer le userBook
                        if ($this->addUserBook($book->getId(), $context)) {
                            $booksCorrectlyImported[] = $book;
                        }
                    }
                }
            }
        }
    }

    private function searchBooks($isbnsInFile, Sb\Config\Model\Config $config) {

        $booksInBase = $this->getBooksInBase($isbnsInFile);
        \Sb\Trace\Trace::addItem(count($booksInBase) . " livres trouvés dans la Base.");

        $resultingBooks = array();
        if ($booksInBase) {
            // récupération des books manquant dans amazon
            if (count($booksInBase) != count($isbnsInFile)) {
                $isbnsFromBase = array_map("getIsbnFromBook", $booksInBase);
                //var_dump($isbnsFromBase);
                $missedIsbns = array_diff($isbnsInFile, $isbnsFromBase);
                \Sb\Trace\Trace::addItem(count($missedIsbns) . " livres à rechercher dans Amazon.");

                $booksFromAmazon = $this->getBooksFromAmazon($missedIsbns, $config);
                if ($booksFromAmazon) {
                    \Sb\Trace\Trace::addItem(count($booksFromAmazon) . " livres trouvés dans Amazon.");
                    $resultingBooks = array_merge($booksInBase, $booksFromAmazon);
                    \Sb\Trace\Trace::addItem(count($resultingBooks) . " livres trouvés dans Amazon + Base.");
                } else {
                    $resultingBooks = $booksInBase;
                }
            } else {
                $resultingBooks = $booksInBase;
            }
        } else { // aucun livre trouvé dans la base, rechercher dans Amazon
            $resultingBooks = getBooksFromAmazon($isbnsInFile, $config);
        }

        return $resultingBooks;
    }

    private function getIsbnsInFile(\Sb\Context\Model\Context $context) {

        $uploaddir = $context->getBaseDirectory() . "/var/uploads/";
        $uploadfile = $uploaddir . session_id() . "_" . basename($_FILES['importFile']['name']);
        if (move_uploaded_file($_FILES['importFile']['tmp_name'], $uploadfile)) {
            \Sb\Trace\Trace::addItem("File is valid, and was successfully uploaded.");
            $fileContent = file_get_contents($uploadfile);
            $lines = explode("\r\n", $fileContent);
            $isbns = array_filter(array_map(array(&$this, "getIsbnFromLine"), $lines), array(&$this, "isIsbn"));
            // dédoublonage des isbns
            $isbns = array_unique($isbns);
            //var_dump($isbns);
            return $isbns;
        } else {
            \Sb\Trace\Trace::addItem("Possible file upload attack!");
        }
    }

    private function getIsbnFromLine($line) {
        $cols = explode(",", $line);
        return $cols[0];
    }

    private function getIsbnFromBook(\Sb\Db\Model\Book $book) {
        return $book->getISBN10();
    }

    private function isIsbn($isbn) {
        if (strlen($isbn) == 10) {
            return true;
        }
        return false;
    }

    private function getBooksInBase($isbns) {
        $bookDao = \Sb\Db\Dao\BookDao::getInstance();
        return $bookDao->getOneByCodes($isbns, null, null);
    }

    private function getBooksFromAmazon($isbns, Config $config) {

        \Sb\Trace\Trace::addItem(count($isbns) . " Isbns vont être requetés à Amazon : " . implode(",", $isbns));

        $booksFromAmazon = array();

        for ($index = 0; $index < count($isbns); $index = $index + 10) {

            $isbnsSlice = array_slice($isbns, $index, 10);
            \Sb\Trace\Trace::addItem(count($isbnsSlice) . " Isbns requetés par requête Amazon : " . implode(",", $isbnsSlice));

            $amazonService = new Zend_Service_Amazon($config->getAmazonApiKey(), 'FR', $config->getAmazonSecretKey());
            $responsesGroups = 'Small,ItemAttributes,Images,EditorialReview,Reviews';

            // Recherche d'une liste de livre
            $amazonResults = $amazonService->itemLookup(implode(",", $isbnsSlice),
                array('SearchIndex' => 'Books',
                    'AssociateTag' => $config->getAmazonAssociateTag(),
                    'ResponseGroup' => $responsesGroups,
                    'IdType' => 'ISBN'));

            if ($amazonResults) {
                $i = 0;
                foreach ($amazonResults as $amazonResult) {
                    $result = new \Sb\Db\Model\Book();
                    \Sb\Db\Mapping\BookMapper::mapFromAmazonResult($result, $amazonResult);
                    if ($result->IsValid()) {
                        $i++;
                        $booksFromAmazon[] = $result;
                    }
                    //var_dump($booksFromAmazon);
                }
                \Sb\Trace\Trace::addItem($i . " trouvés lors de cette requête amazon.");
            }
        }

        return $booksFromAmazon;
    }

    private function addUserBook($bookId, \Sb\Context\Model\Context $context) {

        $userBook = new \Sb\Db\Model\UserBook();
        $userBook->setUserId($context->getConnectedUser()->getId());
        $userBook->setBookId($bookId);
        $userBook->setIsOwned(true);
        $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();
        $returnId = $userBookDao->Add($userBook);
        if ($returnId) {
            \Sb\Trace\Trace::addItem("Le livre a été ajouté à la biblio correctement.");
        } else {
            \Sb\Trace\Trace::addItem("KO : Le livre n'a pas été ajouté à la biblio.");
        }
        return $returnId;
    }

    private function showTableSummary($title, $books, \Sb\Context\Model\Context $context) {

        $tplRows = array();
        $idx = 0;
        foreach ($books as $book) {
            $idx++;
            $tplRow = new \Sb\Templates\Template("import/tableRow");
            $tplRow->set("isbn", $book->getISBN10());
            $tplRow->set("title", $book->getTitle());
            $tplRow->set("index", $idx);
            $tplRows[] = $tplRow;
        }
        $rows = \Sb\Templates\Template::merge($tplRows);

        $tpl = new \Sb\Templates\Template("import/table");
        $tpl->set("rows", $rows);
        $tpl->set("tableTitle", $title);
        return $tpl->output();
    }

    private function showNotImported($isbnsNotImported, \Sb\Context\Model\Context $context) {
        $tpl = new \Sb\Templates\Template("import/notImported");
        $tpl->set("isbns", implode(", ", $isbnsNotImported));
        $tpl->set("nbBooks", count($isbnsNotImported));
        return $tpl->output();
    }

    // End Function for import action

    private function IsBorrowable(UserBook $userBook) {
        // test if user owns the book
        if (!$userBook->getIsOwned())
            return false;
        // test if books is not lent
        $activeLending = $userBook->getActiveLending();
        if ($activeLending) {
            return false;
        }
        return true;
    }

    private function validateUserInputForm() {
        $ret = true;
        if ($_POST) {

            if (strlen(ArrayHelper::getSafeFromArray($_POST, "guest_name", NULL)) < 3) {
                Flash::addItem(__("Le nom doit comprendre au moins 3 caractères.", "s1b"));
                $ret = false;
            }

            if (ArrayHelper::getSafeFromArray($_POST, "send_invitation", NULL) == 1) {
                $guestEmail = ArrayHelper::getSafeFromArray($_POST, "guest_email", NULL);
                if (!$guestEmail) {
                    Flash::addItem(__("Vous devez renseigné un email si vous souhaitez envoyer une invitation.", "s1b"));
                    $ret = false;
                } else {
                    if (!StringHelper::isValidEmail($guestEmail)) {
                        Flash::addItem(__("L'email que vous avez renseigné n'est pas valide. Merci de réessayer.", "s1b"));
                        $ret = false;
                    }
                }
            }
        } else {
            $ret = false;
        }
        return $ret;
    }

}
