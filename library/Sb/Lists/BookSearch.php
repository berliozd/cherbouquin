<?php

namespace Sb\Lists;

class BookSearch {

    const SEARCH_BOOK_KEY = 'searchBook';
    
    private $allResults = null;
    private $hasResults = false;
    private $list;

    function __construct($doSearch, $searchTerm, $pageId, $nbResultsPerPage, $nbResultsToShow, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested) {

        $fullCacheKey = self::SEARCH_BOOK_KEY . "_" . session_id();
        $this->loadResults($fullCacheKey, $doSearch, $searchTerm, $nbResultsToShow, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested);

        if ($this->allResults) {
            $this->hasResults = true;
            if ($pageId) {
                \Sb\Trace\Trace::addItem("Affectation du listOptions");
                $listOptions = new \Sb\Lists\Options();
                $paging = new \Sb\Lists\Paging();
                $paging->setCurrentPageId($pageId);
                $listOptions->setPaging($paging);
            }
            $this->list = new \Sb\Lists\BookList($nbResultsPerPage, $this->allResults, $listOptions, false);
        }
    }

    private function loadResults($fullCacheKey, $doSearch, $searchTerm, $nbResultsToShow, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested) {

        $cache = \Sb\Cache\ZendFileCache::getInstance();

        if ($doSearch) {

            $cache->remove($fullCacheKey);

            // Exécution de la recherche pour le premier affichage de la page
            $this->searchData($searchTerm, $nbResultsToShow, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested);

            if ($this->allResults)
                $cache->save($this->allResults, $fullCacheKey);
        } else {
            // Pagination: Tentative de récupération des données depuis le cache
            $this->allResults = $cache->load($fullCacheKey);
            \Sb\Trace\Trace::addItem(count($this->allResults) . " retrouvé(s) dans le cache.");
        }
    }

    private function searchData($searchTerm, $nbResultsToShow, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested) {

        if ($searchTerm) {

            $searchTerm = trim($searchTerm);

            // faire la recherche dans la base de donnée
            \Sb\Trace\Trace::addItem('Début de recherche dans la base.');
            $this->allResults = \Sb\Db\Dao\BookDao::getInstance()->getListByKeyword($searchTerm);

            \Sb\Trace\Trace::addItem(count($this->allResults) . " résulat(s) trouvé(s) dans la base.");

            try {
                $amazonResults = null;
                // si pas de résultat ou nb de resultats < à ce que l'on souhaite afficher, faire la recherche Amazon
                if ((!$this->allResults) || (count($this->allResults) < $nbResultsToShow)) {

                    \Sb\Trace\Trace::addItem('Pas de résultats trouvés dans la base ou pas suffisamment -> début de recherche sur Amazon.');
                    // Requesting amazon FR first
                    $amazonResults = $this->getAmazonResults($searchTerm, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested, 'FR');
                    // Requesting amazon US
                    if (!$amazonResults)
                        $amazonResults = $this->getAmazonResults($searchTerm, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested, 'US');
                } else {
                    \Sb\Trace\Trace::addItem('Résulats trouvés dans la base.');
                }

                // si des résultats ont été trouvés avec Amazon
                // si des résultats avaient été trouvés dans la base
                // ==> ils doivent être mergés ensemble
                if ($this->allResults && $amazonResults) {

                    $allResultsKeys = array_map(array(&$this, "extractKey"), $this->allResults);
                    $this->allResults = array_combine($allResultsKeys, $this->allResults);

                    \Sb\Trace\Trace::addItem("Livres trouvés dans la base:" . implode(",", array_keys($this->allResults)));
                    \Sb\Trace\Trace::addItem("Livres trouvés dans la Amazon:" . implode(",", array_keys($amazonResults)));
                    \Sb\Trace\Trace::addItem("Merge des résulats issues de la base et de ceux issues de amazon.");

                    // Adding the books not present in sql found on amazon after the books that are already in sql
                    $this->allResults = $this->allResults + array_diff_key($amazonResults, $this->allResults);

                    \Sb\Trace\Trace::addItem(count($this->allResults) . " résulat(s) après merge.");
                    \Sb\Trace\Trace::addItem("Livres mergés:" . implode(",", array_keys($this->allResults)));
                } elseif ($amazonResults) {
                    \Sb\Trace\Trace::addItem("Les résulats sont issus d'amazon uniquement.");
                    $this->allResults = $amazonResults;
                }
            } catch (Exception $exc) {
                \Sb\Trace\Trace::addItem(sprintf("Une erreur s'est produite lors de l'appel à l'api amazon : %s", $exc->getMessage()));
            }
        }
    }

    /**
     *
     * @param type $results
     */
    public function extractKey(\Sb\Db\Model\Book $book) {
        return $book->getISBN10();
    }

    /**
     *
     * @return array of Books (of type Book) from Amazon
     */
    private function getAmazonResults($searchTerm, $amazonApiKey, $amazonSecretKey, $amazonAssociateTag, $amazonNumberOfPageRequested, $amazonSite) {

        $amazonService = new \Zend_Service_Amazon($amazonApiKey, $amazonSite, $amazonSecretKey);
        $amazonResults = null; // tableau d'amazon item recu directement
        $booksFromAmazon = null; // tableau d'objet Book reçu depuis amazon
        $responsesGroups = 'Small,ItemAttributes,Images,EditorialReview,Reviews,BrowseNodes,OfferSummary';
        //$version = '2005-10-05';
        $nbPageRequested = $amazonNumberOfPageRequested;

        $nbMaxPageRequested = 5;
        if ($nbPageRequested > $nbMaxPageRequested)
            $nbPageRequested = $nbMaxPageRequested;

        for ($itemPageNum = 1; $itemPageNum <= $nbPageRequested; $itemPageNum++) {

            $tmpAmazonResults = null;

            \Sb\Trace\Trace::addItem("Page " . $itemPageNum . " demandée.");
            // recherche faite sur le code isbn10
            if (strlen($searchTerm) == 10) {
                \Sb\Trace\Trace::addItem('Recherche Amazon sur ISBN : ' . $searchTerm);
                $tmpAmazonResults = $amazonService->itemSearch(
                        array('SearchIndex' => 'Books',
                            'AssociateTag' => $amazonAssociateTag,
                            'ResponseGroup' => $responsesGroups,
                            'Power' => 'isbn:' . $searchTerm,
                            //'Version' => $version,
                            'ItemPage' => $itemPageNum));
                if (!$tmpAmazonResults || $tmpAmazonResults->totalResults() == 0) {
                    \Sb\Trace\Trace::addItem('Recherche Amazon sur ASIN : ' . $searchTerm);
                    $tmpAmazonResults = $amazonService->itemSearch(
                            array('SearchIndex' => 'Books',
                                'AssociateTag' => $amazonAssociateTag,
                                'ResponseGroup' => $responsesGroups,
                                'Power' => 'asin:' . $searchTerm,
                                //'Version' => $version,
                                'ItemPage' => $itemPageNum));
                }
            }


            // recherche faite sur le mot clé
            if (!$tmpAmazonResults || $tmpAmazonResults->totalResults() == 0) {
                \Sb\Trace\Trace::addItem('Recherche Amazon sur mot clé : ' . $searchTerm);
                $tmpAmazonResults = $amazonService->itemSearch(
                        array('SearchIndex' => 'Books',
                            'AssociateTag' => $amazonAssociateTag,
                            'ResponseGroup' => $responsesGroups,
                            'Keywords' => $searchTerm,
                            //'Version' => $version,
                            'ItemPage' => $itemPageNum));
            }

            $amazonResults = $tmpAmazonResults;

            // mapping des résultats amazon en objet Book
            if ($amazonResults) {
                foreach ($amazonResults as $amazonResult) {
                    $result = new \Sb\Db\Model\Book();
                    \Sb\Db\Mapping\BookMapper::mapFromAmazonResult($result, $amazonResult);
                    // Book is added to the collection only if 
                    // author is set
                    // and ISBN10, ISBN13 or ASIN is set                    
                    if (count($result->getContributors()) > 0) {
                        if ($result->getISBN10() || $result->getISBN13() || $result->getASIN())
                        $booksFromAmazon[$result->getISBN10()] = $result;
                    }                    
                }
            }
        }
        \Sb\Trace\Trace::addItem(count($booksFromAmazon) . " résultat(s) trouvé(s) sur amazon $amazonSite (après mapping vers Book).");
        return $booksFromAmazon;
    }

    public function getHasResults() {
        return $this->hasResults;
    }

    public function getList() {
        return $this->list;
    }

}

?>