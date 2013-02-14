<?php

\Sb\Trace\Trace::addItem(\Sb\Entity\LibraryPages::SEARCH_SHOW);

global $s1b;

$config = $s1b->getConfig();
$context = $s1b->getContext();

// get results from cache
$pageId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_SESSION, Sb\Entity\SessionKeys::SEARCH_A_BOOK_PAGE_ID, 1);
$bookSearch = new \Sb\Lists\BookSearch(false, null, $pageId, $config->getSearchNbResultsPerPage(),
                $config->getSearchNbResultsToShow(), $config->getAmazonApiKey(), $config->getAmazonSecretKey(), $config->getAmazonAssociateTag(),
                $config->getAmazonNumberOfPageRequested());

if (!$bookSearch->getHasResults()) {
    \Sb\Flash\Flash::addItem(__("Vos critères de recherche ne nous ont pas permis de trouver de livre.", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToHome();
} else {
    $list = $bookSearch->getList();
    $view = new \Sb\View\BookSearch($list->getShownResults(), $list->getPagerLinks(), $list->getFirstItemIdx(), $list->getLastItemIdx(), $list->getNbItemsTot());
    echo $view->get();
}