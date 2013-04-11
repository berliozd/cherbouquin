<?php

\Sb\Trace\Trace::addItem(\Sb\Entity\LibraryPages::SEARCH_SUBMIT);

use \Sb\Helpers\HTTPHelper;

global $s1b;

$config = $s1b->getConfig();
$context = $s1b->getContext();

// do search
// unset search result page id in session
unset($_SESSION[\Sb\Entity\SessionKeys::SEARCH_A_BOOK_PAGE_ID]);

$searchTerm = null;
if (array_key_exists('searchTerm', $_REQUEST)) {
    $searchTerm = $_REQUEST['searchTerm'];
}
if (strpos($searchTerm, "%") !== false && strlen($searchTerm) == 1) {
    \Sb\Flash\Flash::addItem(__("Le caractère % n'est pas autorisé lors des recherches.", "s1b"));
    HTTPHelper::redirectToReferer();
}

$bookSearch = new \Sb\Lists\BookSearch(true, $searchTerm, 1, $config->getSearchNbResultsPerPage(), 
                $config->getSearchNbResultsToShow(), $config->getAmazonApiKey(), $config->getAmazonSecretKey(), $config->getAmazonAssociateTag(),
                $config->getAmazonNumberOfPageRequested());

if (!$bookSearch->getHasResults()) {
    \Sb\Flash\Flash::addItem(__("Vos critères de recherche ne nous ont pas permis de trouver de livre.", "s1b"));
    HTTPHelper::redirectToHome();
} else {
    // redirect to page in charge of rendering (HTTP GET allows back with back button)
    HTTPHelper::redirect(\Sb\Entity\urls::BOOK_SEARCH, array("page" => \Sb\Entity\LibraryPages::SEARCH_SHOW, "searchTerm" => $searchTerm));
}