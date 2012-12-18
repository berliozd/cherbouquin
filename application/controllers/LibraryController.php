<?php

use \Sb\Helpers\EntityHelper;

class LibraryController extends Zend_Controller_Action {

    const LIST_OPTIONS_SUFFIX = "_options";
    const LIST_AUTHORS_FIRST_LETTER_SUFFIX = "_authorsFirstLetter";
    const LIST_TITLES_FIRST_LETTER_SUFFIX = "_titlesFirstLetter";

    private function getConfig() {
        global $globalConfig;
        return $globalConfig;
    }

    private function getContext() {
        global $globalContext;
        return $globalContext;
    }

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-page', 'html')
                ->addActionContext('sort', 'html')
                ->initContext();
    }

    public function indexAction() {
        // action body
    }

    public function sortAction() {
        $this->setLibraryUserIdForFriend();

        $fullKey = $this->formateListKey($_POST["key"]);
        $sortCriteria = $_POST["param"];
        
        $this->setListOptionsForSorting($fullKey, $sortCriteria);

        $this->view->libraryPage = $this->getPage($_POST["key"]);
    }

    public function getPageAction() {
        $this->setLibraryUserIdForFriend();

        $fullKey = $this->formateListKey($_POST["key"]);
        $pageId = $_POST["param"];
        
        $this->setListOptionsForNavigation($fullKey, $pageId);

        $this->view->libraryPage = $this->getPage($_POST["key"]);
    }

    private function getPage($key) {
        $books = \Sb\Db\Service\UserBookSvc::getInstance()->getUserBooks($key, $this->getContext()->getLibraryUserId(), true);
        if ($books) {
            // Prepare list view
            $view = $this->createBookTableView($key, $books);
            $response = $view->get();
            return $response;
        }
        else
            return "";
    }

    private function formateListKey($key) {
        $tmpFriendId = "";
        if ($this->getContext()->getIsShowingFriendLibrary()) {
            $tmpFriendId = $this->getContext()->getLibraryUserId();
        }
        $fullKey = sprintf("%s_%s_%s_%s", $key, $this->getContext()->getConnectedUser()->getId(), $tmpFriendId, session_id());
        return $fullKey;
    }

    private function setListOptionsForNavigation($listKey, $pageId) {
        $listOptions = $this->getListOptions($listKey);
        if ($pageId) {
            // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement le numéro de page
            if ($listOptions) {
                if ($listOptions->getPaging()) {
                    $listOptions->getPaging()->setCurrentPageId($pageId);
                } else {
                    $paging = new \Sb\Lists\Paging();
                    $paging->setCurrentPageId($pageId);
                    $listOptions->setPaging($paging);
                }
            } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page
                $paging = new \Sb\Lists\Paging();
                $paging->setCurrentPageId($pageId);
                $listOptions = new \Sb\Lists\Options();
                $listOptions->setPaging($paging);
            }
        }
        $this->setListOptions($listKey, $listOptions);
    }

    public function setListOptionsForSorting($listKey, $sortCriteria) {
        \Sb\Trace\Trace::addItem("setListOptionsForSorting");
        $listOptions = $this->getListOptions($listKey);
        if ($sortCriteria) {
            \Sb\Trace\Trace::addItem("paramètre sortCriteria passé  : " . $sortCriteria);
            // Un objet Sb\Lists\Options a été récupéré pour cette liste : on assigne uniquement les infos de sorting
            if ($listOptions) {
                \Sb\Trace\Trace::addItem("Un listOptions trouvé en session pour " . $listKey);
                if ($listOptions->getSorting()) {
                    if ($listOptions->getSorting()->getField() == $sortCriteria) {
                        $sortDirection = ($listOptions->getSorting()->getDirection() == EntityHelper::ASC ? EntityHelper::DESC : EntityHelper::ASC);
                    } else {
                        $sortDirection = EntityHelper::ASC;
                    }
                    $listOptions->getSorting()->setField($sortCriteria);
                    $listOptions->getSorting()->setDirection($sortDirection);
                } else {
                    $sorting = new \Sb\Lists\Sorting();
                    $sorting->setField($sortCriteria);
                    $sorting->setDirection(EntityHelper::ASC);
                    $listOptions->setSorting($sorting);
                }
            } else { // Aucun objet Sb\Lists\Options n'a été récupéré pour cette liste : on en créé un avec le numéro de page
                \Sb\Trace\Trace::addItem("Pas de listOptions trouvé en session pour " . $listKey);
                $sorting = new \Sb\Lists\Sorting();
                $sorting->setField($sortCriteria);
                $sorting->setDirection(EntityHelper::ASC);
                $listOptions = new \Sb\Lists\Options();
                $listOptions->setSorting($sorting);
            }
        }
        $this->setListOptions($listKey, $listOptions);
    }

    private function getListOptions($fullKey) {
        $opts = \Sb\Cache\ZendFileCache::getInstance()->load($fullKey . self::LIST_OPTIONS_SUFFIX);
        if ($opts instanceof Sb\Lists\Options) {
            return $opts;
        } else {
            return null;
        }
    }

    private function setListOptions($fullKey, $value) {
        \Sb\Cache\ZendFileCache::getInstance()->save($value, $fullKey . self::LIST_OPTIONS_SUFFIX);
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
        $list = new \Sb\Lists\BookList($this->getConfig()->getListNbBooksPerPage(), $books, $this->getContext()->getBaseDirectory(), $listOpts);

        $authorsFirstLetters = $this->getListMetaData($fullKey, Sb\Lists\MetaDataType::AUTHORS_FIRST_LETTERS);
        $titlesFirstLetters = $this->getListMetaData($fullKey, Sb\Lists\MetaDataType::TITLES_FIRST_LETTERS);

        return new \Sb\View\BookTable($key, $this->getConfig()->getUserLibraryPageName(), $this->getConfig()->getFriendLibraryPageName(),
                        $list->getShownResults(), $list->getPagerLinks(),
                        $list->getFirstItemIdx(), $list->getLastItemIdx(),
                        $list->getNbItemsTot(), $listOpts, $this->getContext()->getIsShowingFriendLibrary(), $searchValue,
                        $authorsFirstLetters, $titlesFirstLetters, $filteringType, $filter);
    }

    private function getListMetaData($fullKey, $metaDataType) {
        switch ($metaDataType) {
            case Sb\Lists\MetaDataType::AUTHORS_FIRST_LETTERS:
                $fullKey = $fullKey . self::LIST_AUTHORS_FIRST_LETTER_SUFFIX;
                break;
            case Sb\Lists\MetaDataType::TITLES_FIRST_LETTERS:
                $fullKey = $fullKey . self::LIST_TITLES_FIRST_LETTER_SUFFIX;
                break;
            default:
                break;
        }
        return \Sb\Cache\ZendFileCache::getInstance()->load($fullKey);
    }

    private function setLibraryUserIdForFriend() {
        $isShowingFriendLibrary = $_POST["friendlib"];
        if ($isShowingFriendLibrary) {
            global $globalContext;
            $globalContext->setIsShowingFriendLibrary(true);
            $globalContext->setLibraryUserId($_SESSION['fid']);
        }
    }
}

