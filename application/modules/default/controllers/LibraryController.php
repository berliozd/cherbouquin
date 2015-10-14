<?php

use Sb\Helpers\EntityHelper,
    Sb\Helpers\SecurityHelper,
    Sb\Helpers\ArrayHelper,
    Sb\Helpers\HTTPHelper,

    Sb\Db\Model\UserBook,

    Sb\Db\Dao\UserDao,

    Sb\Db\Service\UserBookSvc,
    Sb\Authentification\Service\AuthentificationSvc,

    Sb\Lists\Filtering,
    Sb\Lists\Paging,
    Sb\Lists\Sorting,
    Sb\Lists\Options,
    Sb\Lists\MetaDataType,
    Sb\Lists\BookList,
    Sb\Lists\Search,
    Sb\View\BookList as BookListView,
    Sb\View\LibraryHeader,

    Sb\Cache\ZendFileCache,
    Sb\Flash\Flash,

    Sb\View\BookTable;

class Default_LibraryController extends Zend_Controller_Action {

    const LIST_OPTIONS_SUFFIX = "_options";
    const LIST_AUTHORS_FIRST_LETTER_SUFFIX = "_authorsFirstLetter";
    const LIST_TITLES_FIRST_LETTER_SUFFIX = "_titlesFirstLetter";

    const ALL_BOOKS_KEY = 'allBooks';
    const WISHED_BOOKS_KEY = 'wishedBooks';
    const BORROWED_BOOKS_KEY = 'borrowedBooks';
    const LENDED_BOOKS_KEY = 'lendedBooks';
    const MY_BOOKS_KEY = 'myBooks';

    public function init() {

        // Checks is user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();

        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-page', 'html')
            ->addActionContext('sort', 'html')
            ->initContext();
    }

    public function indexAction() {

        try {

            $key = $this->getListKey();

            // Get the list key (allBooks, wishedBooks, etc...)
            $fullKey = $this->formateListKey($key);

            // Reset the list options (sorting, searching, paging, filtering) if requested
            if (ArrayHelper::getSafeFromArray($_GET, "reset", false))
                $this->resetListOption($fullKey);

            $filteringOrSearching = (array_key_exists("searchvalue", $_GET) || (array_key_exists("filter", $_GET) && array_key_exists("filtertype", $_GET)));

            // Get the books
            $books = UserBookSvc::getInstance()->getUserBooks($key, $this->getContext()->getConnectedUser()->getId(), $filteringOrSearching);

            // Set list meta data if getting list first time
            if (!$filteringOrSearching)
                $this->setListMetaData($books, $fullKey);

            // Set filtering and searching options
            if ($filteringOrSearching)
                $this->setFilteringAndSearching($fullKey);

            $booksTableView = $this->createBookTableView($key, $books, false);

            $this->view->list = new BookListView($key, $booksTableView, $key);
            $this->view->header = new LibraryHeader(null, $key);
            $this->view->friendLibrary = false;

        } catch (\Exception $e) {
            Flash::addItem($e->getMessage());
            HTTPHelper::redirectToReferer();
        }
    }

    public function friendLibraryAction() {

        try {

            // Set friend library data
            $this->setFriendLibaryData();

            $key = $this->getListKey();

            // Get the list key (allBooks, wishedBooks, etc...)
            $fullKey = $this->formateListKey($key);

            // Reset the list options (sorting, searching, paging, filtering) if requested
            if (ArrayHelper::getSafeFromArray($_GET, "reset", false))
                $this->resetListOption($fullKey);

            $filteringOrSearching = (array_key_exists("searchvalue", $_GET) || (array_key_exists("filter", $_GET) && array_key_exists("filtertype", $_GET)));

            // Get the books
            $books = UserBookSvc::getInstance()->getUserBooks($key, $this->getContext()->getLibraryUserId(), $filteringOrSearching);

            // Set list meta data if getting list first time
            if (!$filteringOrSearching)
                $this->setListMetaData($books, $fullKey);

            // Set filtering and searching options
            if ($filteringOrSearching)
                $this->setFilteringAndSearching($fullKey);

            $booksTableView = $this->createBookTableView($key, $books, false);

            $this->view->list = new BookListView($key, $booksTableView, $key);
            $this->view->header = new LibraryHeader($this->getContext()->getLibraryUserId(), $key);
            $this->view->friendLibrary = $this->getContext()->getIsShowingFriendLibrary();

        } catch (\Exception $e) {
            Flash::addItem($e->getMessage());
            HTTPHelper::redirectToReferer();
        }
    }

    public function sortAction() {

        if ($_POST["friendlib"])
            $this->setLibraryUserIdForFriend();

        $fullKey = $this->formateListKey($_POST["key"]);
        $sortCriteria = $_POST["param"];

        $this->setListOptionsForSorting($fullKey, $sortCriteria);

        $this->view->libraryPage = $this->getPage($_POST["key"]);
    }

    public function getPageAction() {

        if ($_POST["friendlib"])
            $this->setLibraryUserIdForFriend();

        $fullKey = $this->formateListKey($_POST["key"]);
        $pageId = $_POST["param"];

        $this->setListOptionsForNavigation($fullKey, $pageId);

        $this->view->libraryPage = $this->getPage($_POST["key"]);
    }

    private function getPage($key) {

        $userId = ($this->getContext()->getIsShowingFriendLibrary() ? $this->getContext()->getLibraryUserId() : $this->getContext()->getConnectedUser()->getId());

        $books = UserBookSvc::getInstance()->getUserBooks($key, $userId, true);
        if ($books) {
            // Prepare list view
            $view = $this->createBookTableView($key, $books);
            $response = $view->get();
            return $response;
        } else
            return "";
    }

    private function setListOptionsForNavigation($listKey, $pageId) {

        $listOptions = $this->getListOptions($listKey);
        if ($pageId) {

            // Un objet Options a été récupéré pour cette liste : on assigne uniquement le numéro de page
            if ($listOptions) {

                if ($listOptions->getPaging())
                    $listOptions->getPaging()->setCurrentPageId($pageId);
                else {

                    $paging = new Paging();
                    $paging->setCurrentPageId($pageId);
                    $listOptions->setPaging($paging);
                }

            } else { // Aucun objet Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page

                $paging = new Paging();
                $paging->setCurrentPageId($pageId);
                $listOptions = new Options();
                $listOptions->setPaging($paging);

            }
        }
        $this->setListOptions($listKey, $listOptions);
    }

    private function setListOptionsForSorting($key, $sortCriteria) {

        $listOptions = $this->getListOptions($key);

        if ($sortCriteria) {

            // Un objet Options a été récupéré pour cette liste : on assigne uniquement les infos de sorting
            if ($listOptions) {

                if ($listOptions->getSorting()) {

                    if ($listOptions->getSorting()->getField() == $sortCriteria)
                        $sortDirection = ($listOptions->getSorting()->getDirection() == EntityHelper::ASC ? EntityHelper::DESC : EntityHelper::ASC);
                    else
                        $sortDirection = EntityHelper::ASC;

                    $listOptions->getSorting()->setField($sortCriteria);
                    $listOptions->getSorting()->setDirection($sortDirection);

                } else {

                    $sorting = new Sorting();
                    $sorting->setField($sortCriteria);
                    $sorting->setDirection(EntityHelper::ASC);
                    $listOptions->setSorting($sorting);
                }

            } else { // Aucun objet Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page

                $sorting = new Sorting();
                $sorting->setField($sortCriteria);
                $sorting->setDirection(EntityHelper::ASC);
                $listOptions = new Options();
                $listOptions->setSorting($sorting);
            }
        }

        $this->setListOptions($key, $listOptions);
    }

    public function setListOptionsForSearching($key, $searchValue) {

        $listOptions = $this->getListOptions($key);

        if ($searchValue) {
            // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement le search value
            if ($listOptions) {

                if ($listOptions->getSearch())
                    $listOptions->getSearch()->setValue($searchValue);
                else {

                    $search = new Search();
                    $search->setValue($searchValue);
                    $listOptions->setSearch($search);
                }

            } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page

                $search = new Search();
                $search->setValue($searchValue);
                $listOptions = new Options();
                $listOptions->setSearch($search);
            }
        }

        $this->setListOptions($key, $listOptions);
    }

    public function setListOptionsForFiltering($key, $filteringValue, $filteringType) {

        $listOptions = $this->getListOptions($key);

        if ($filteringValue) {

            // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement le filtering
            if ($listOptions) {

                if ($listOptions->getFiltering()) {

                    $listOptions->getFiltering()->setType($filteringType);
                    $listOptions->getFiltering()->setValue($filteringValue);
                } else {

                    $filtering = new Filtering();
                    $filtering->setType($filteringType);
                    $filtering->setValue($filteringValue);
                    $listOptions->setFiltering($filtering);
                }

            } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le filtering

                $filtering = new Filtering();
                $filtering->setType($filteringType);
                $filtering->setValue($filteringValue);
                $listOptions = new Options();
                $listOptions->setFiltering($filtering);
            }
        }
        $this->setListOptions($key, $listOptions);
    }

    private function getListOptions($key) {

        $opts = ZendFileCache::getInstance()->load($key . self::LIST_OPTIONS_SUFFIX);
        if ($opts instanceof Options)
            return $opts;
        else
            return null;
    }

    private function setListOptions($key, $value) {

        ZendFileCache::getInstance()->save($value, $key . self::LIST_OPTIONS_SUFFIX);
    }

    public function resetListOption($key) {

        $key = $key . self::LIST_OPTIONS_SUFFIX;
        ZendFileCache::getInstance()->remove($key);
    }

    private function setFilteringAndSearching($key) {

        if (array_key_exists("searchvalue", $_GET)) {

            // assignation du paramètre de recherche
            $searchValue = ArrayHelper::getSafeFromArray($_GET, "searchvalue", null);
            $this->setListOptionsForSearching($key, $searchValue);

        } else if (array_key_exists("filter", $_GET) && array_key_exists("filtertype", $_GET)) {

            // assignation du paramètre de filtrage
            $filteringValue = ArrayHelper::getSafeFromArray($_GET, "filter", null);
            $filteringType = ArrayHelper::getSafeFromArray($_GET, "filtertype", null);
            $this->setListOptionsForFiltering($key, $filteringValue, $filteringType);

        }

        // Rebase on first page
        $this->setListOptionsForNavigation($key, 1);
    }

    private function createBookTableView($key, $books) {

        // Get full key for list option cache
        $fullKey = $this->formateListKey($key);

        // Get list options
        $listOpts = $this->getListOptions($fullKey);
        // Get potential search value and filtering
        $searchValue = "";
        $filteringType = "";
        $filter = "";
        if ($listOpts) {
            if ($listOpts->getSearch()) {
                $searchValue = $listOpts->getSearch()->getValue();
            }
            if ($listOpts->getFiltering()) {
                $filteringType = $listOpts->getFiltering()->getType();
                $filter = $listOpts->getFiltering()->getValue();
            }
        }

        // Prepare list
        $list = new BookList($this->getConfig()->getListNbBooksPerPage(), $books, $listOpts);

        $authorsFirstLetters = $this->getListMetaData($fullKey, MetaDataType::AUTHORS_FIRST_LETTERS);
        $titlesFirstLetters = $this->getListMetaData($fullKey, MetaDataType::TITLES_FIRST_LETTERS);

        return new BookTable($key, $list->getShownResults(), $list->getPagerLinks(),
            $list->getFirstItemIdx(), $list->getLastItemIdx(),
            $list->getNbItemsTot(), $listOpts, $this->getContext()->getIsShowingFriendLibrary(), $searchValue,
            $authorsFirstLetters, $titlesFirstLetters, $filteringType, $filter);
    }

    private function getConfig() {
        return new Sb\Config\Model\Config();
    }

    private function getContext() {
        return new \Sb\Context\Model\Context();
    }

    private function isValidBooksKey($key) {

        $booksKeys = array(self::ALL_BOOKS_KEY, self::MY_BOOKS_KEY, self::WISHED_BOOKS_KEY, self::BORROWED_BOOKS_KEY, self::LENDED_BOOKS_KEY);

        if (in_array($key, $booksKeys))
            return true;
        return false;
    }

    /**
     * Set user id of user's library we are acccessing in session
     * @throws Sb\Exception\UserException
     */
    private function setFriendLibaryData() {

        $temporayFriendUSerId = null;
        // Get fid from QS
        if (array_key_exists("fid", $_GET)) {
            $temporayFriendUSerId = $_GET['fid'];
        } else { // Get fid from SESSION
            if (array_key_exists("fid", $_SESSION)) {
                $temporayFriendUSerId = $_SESSION['fid'];
            }
        }

        // if fid is set, test if fid is a friend of the connected user or if the user has accepted everyone to see his library
        if ($temporayFriendUSerId) {

            $canAccessLibrary = $this->canAccessLibrary($temporayFriendUSerId);
            if ($canAccessLibrary) {
                // if ok, fid is stored in SESSION and in private variables
                $_SESSION['fid'] = $temporayFriendUSerId;

                // Set infos in context
                $this->setLibraryUserIdForFriend();

            } else {
                throw new \Sb\Exception\UserException(__("Cette bibliothèque n'est pas accessible.", "s1b"));
                unset($_SESSION['fid']);
            }
        } else {
            throw new \Sb\Exception\UserException(__("No friend id received."));
            unset($_SESSION['fid']);
        }
    }

    private function setLibraryUserIdForFriend() {

        $this->getContext()->setIsShowingFriendLibrary(true);
        $this->getContext()->setLibraryUserId($_SESSION['fid']);
    }

    /**
     * Define if connected user can access another user library
     * @param $userId
     * @return bool
     */
    private function canAccessLibrary($userId) {

        $requestedUser = UserDao::getInstance()->get($userId);
        $requestingUser = UserDao::getInstance()->get($this->getContext()->getConnectedUser()->getId());

        return SecurityHelper::IsUserAccessible($requestedUser, $requestingUser);
    }

    private function getListMetaData($fullKey, $metaDataType) {
        switch ($metaDataType) {
            case MetaDataType::AUTHORS_FIRST_LETTERS:
                $fullKey = $fullKey . self::LIST_AUTHORS_FIRST_LETTER_SUFFIX;
                break;
            case MetaDataType::TITLES_FIRST_LETTERS:
                $fullKey = $fullKey . self::LIST_TITLES_FIRST_LETTER_SUFFIX;
                break;
            default:
                break;
        }
        return ZendFileCache::getInstance()->load($fullKey);
    }

    /**
     * Save authors first letters and title first letters
     * @param $books
     * @param $fullKey
     */
    private function setListMetaData($books, $fullKey) {

        if ($books) {

            // Save authors first letters in cache
            $authorsFirstLetter = array_unique(array_map(array(&$this, "firstLetterFromAuthor"), $books));
            usort($authorsFirstLetter, array(&$this, "compareLetters"));
            ZendFileCache::getInstance()->save($authorsFirstLetter, $fullKey . self::LIST_AUTHORS_FIRST_LETTER_SUFFIX);

            // Save titles first letters in cache
            $titlesFirstLetter = array_unique(array_map(array(&$this, "firstLetterFromTitle"), $books));
            usort($titlesFirstLetter, array(&$this, "compareLetters"));
            ZendFileCache::getInstance()->save($titlesFirstLetter, $fullKey . self::LIST_TITLES_FIRST_LETTER_SUFFIX);
        }
    }

    private function getListKey() {

        $key = self::ALL_BOOKS_KEY;
        if ($_GET && array_key_exists("key", $_GET) && $this->isValidBooksKey($_GET["key"])) {
            $key = $_GET["key"];
        }

        return $key;
    }

    private function formateListKey($key) {

        $tmpFriendId = "";
        if ($this->getContext()->getIsShowingFriendLibrary()) {
            $tmpFriendId = $this->getContext()->getLibraryUserId();
        }
        $fullKey = sprintf("%s_%s_%s_%s", $key, $this->getContext()->getConnectedUser()->getId(), $tmpFriendId, session_id());

        return $fullKey;
    }

    function firstLetterFromTitle(UserBook $userBook) {
        return strtoupper(substr($userBook->getBook()->getTitle(), 0, 1));
    }

    function firstLetterFromAuthor(UserBook $userBook) {
        return strtoupper(substr($userBook->getBook()->getOrderableContributors(), 0, 1));
    }

    function compareLetters($letterA, $letterB) {
        return ($letterA < $letterB) ? -1 : 1;
    }
}