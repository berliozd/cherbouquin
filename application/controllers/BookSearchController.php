<?php

class BookSearchController extends Zend_Controller_Action {

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
                ->initContext();
    }

    public function indexAction() {
        // action body
    }

    public function getPageAction() {
        
        $pageId = $_POST['param'];
        
        $_SESSION[\Sb\Entity\SessionKeys::SEARCH_A_BOOK_PAGE_ID] = $pageId;

        $bookSearch = new \Sb\Lists\BookSearch(false, null, $pageId, $this->getConfig()->getSearchNbResultsPerPage(), $this->getContext()->getBaseDirectory(),
                        $this->getConfig()->getSearchNbResultsToShow(), $this->getConfig()->getAmazonApiKey(), $this->getConfig()->getAmazonSecretKey(),
                        $this->getConfig()->getAmazonAssociateTag(), $this->getConfig()->getAmazonNumberOfPageRequested());

        $list = $bookSearch->getList();

        if ($list) {
            $view = new \Sb\View\BookSearch($list->getShownResults(), $list->getPagerLinks(),
                            $list->getFirstItemIdx(), $list->getLastItemIdx(), $list->getNbItemsTot());
            $response = $view->get();
        } else {
            Throw new \Exception(__("Une erreur s'est produite lors de la récupération de la recherche dans la cache."));
        }

        $this->view->page = $response;
    }

}

