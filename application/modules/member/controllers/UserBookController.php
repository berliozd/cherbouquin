<?php
use Sb\Db\Model\Book,
    Sb\Db\Dao\UserBookDao,
    Sb\Db\Dao\ReadingStateDao,
    Sb\Db\Dao\TagDao,
    Sb\Db\Dao\BookDao,
    Sb\Service\BookPageSvc,

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

            $destination = HTTPHelper::Link(Urls::USER_BOOK_ADD_CHOICE, null, false, false);
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
                    HTTPHelper::redirectToUrl($book->getLink());
                } else {
                    HTTPHelper::redirectToUrl($destination);
                }
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
}
