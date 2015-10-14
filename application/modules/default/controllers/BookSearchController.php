<?php

use Sb\Lists\BookSearch,

    Sb\View\BookSearch as BookSearchView,

    Sb\Entity\Urls,
    Sb\Entity\SessionKeys,

    Sb\Helpers\HTTPHelper,
    Sb\Helpers\ArrayHelper,

    Sb\Flash\Flash;

class Default_BookSearchController extends Zend_Controller_Action {

    private function getConfig() {
        return new Sb\Config\Model\Config();
    }

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-page', 'html')
            ->initContext();
    }

    public function searchAction() {

        $searchTerm = ArrayHelper::getSafeFromArray($_REQUEST, "searchTerm", null);
        if (strlen($searchTerm) <= 2) {
            Flash::addItem(__("Votre terme de recherche doit être constitué de plus de 2 caractères.", "s1b"));
            HTTPHelper::redirectToReferer();
        }

        $_SESSION[SessionKeys::SEARCH_A_BOOK_PAGE_ID] = 1;

        $bookSearch = $this->getBooks(true, $searchTerm, 1);
        if (!$bookSearch->getHasResults())
            // Redirect to home
            $this->redirectToHome();
        else
            // Redirect to show page
            HTTPHelper::redirect(Urls::BOOK_SEARCH_SHOW, array("searchTerm" => $searchTerm));
    }

    public function showAction() {

        $pageId = ArrayHelper::getSafeFromArray($_SESSION, SessionKeys::SEARCH_A_BOOK_PAGE_ID, 1);

        $bookSearch = $this->getBooks(false, null, $pageId);
        if (!$bookSearch->getHasResults())
            // Redirect to home
            $this->redirectToHome();
        else
            // Set view
            $this->view->view = $this->getView($bookSearch->getList());
    }

    public function getPageAction() {

        $pageId = $_POST['param'];

        $_SESSION[SessionKeys::SEARCH_A_BOOK_PAGE_ID] = $pageId;

        $bookSearch = $this->getBooks(false, null, $pageId);

        $list = $bookSearch->getList();
        if (!$list)
            Throw new \Exception(__("Une erreur s'est produite lors de la récupération de la recherche dans la cache."));

        $response = $this->getView($list)->get();
        $this->view->page = $response;
    }

    /**
     * @param $doSearch
     * @param $searchTerm
     * @param $pageId
     * @return BookSearch
     */
    private function getBooks($doSearch, $searchTerm, $pageId) {

        return new BookSearch($doSearch, $searchTerm, $pageId, $this->getConfig()->getSearchNbResultsPerPage(),
            $this->getConfig()->getSearchNbResultsToShow(), $this->getConfig()->getAmazonApiKey(),
            $this->getConfig()->getAmazonSecretKey(), $this->getConfig()->getAmazonAssociateTag(),
            $this->getConfig()->getAmazonNumberOfPageRequested());
    }

    /**
     * @param $list
     * @return BookSearchView
     */
    private function getView($list) {

        return new BookSearchView($list->getShownResults(), $list->getPagerLinks(),
            $list->getFirstItemIdx(), $list->getLastItemIdx(), $list->getNbItemsTot());
    }

    private function redirectToHome() {
        Flash::addItem(__("Vos critères de recherche ne nous ont pas permis de trouver de livre.", "s1b"));
        HTTPHelper::redirectToHome();
    }
}

