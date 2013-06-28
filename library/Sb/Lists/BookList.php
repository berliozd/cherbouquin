<?php

namespace Sb\Lists;

/**
 * A list of book with options (sorting, filtering, paging) applied and stored in cache
 */
class BookList {

    private $allResults;
    private $nbResultsPerPage;
    private $pagerLinks;
    private $shownResults;
    private $hasResults;
    private $nbItemsTot;
    private $firstItemIdx = 0;
    private $lastItemIdx = 0;
    private $listOptions;

    function __construct($nbResultsPerPage, $allResults, \Sb\Lists\Options $listOptions = null) {
        $this->initVariables($nbResultsPerPage, $allResults, $listOptions);
        $this->prepare();
    }

    public function initVariables($nbResultsPerPage, $allResults, \Sb\Lists\Options $listOptions = null) {
        $this->nbResultsPerPage = $nbResultsPerPage;
        $this->allResults = $allResults;
        $this->listOptions = $listOptions;
    }

    public function prepare() {
        if ($this->allResults) {

            //Application des options de liste (tri, pagination, search, filering)
            $pageId = null;

            if ($this->listOptions) {

                // Sorting
                if ($this->listOptions->getSorting()) {
                    \Sb\Trace\Trace::addItem("Tri de la liste de livre ");
                    \Sb\Helpers\BooksHelper::sort($this->allResults, $this->listOptions->getSorting());
                }

                // Paging
                if ($this->listOptions->getPaging())
                    $pageId = $this->listOptions->getPaging()->getCurrentPageId();

                // Searching
                if ($this->listOptions->getSearch()) {
                    $backedUpBooks = $this->allResults;
                    
                    $tmpRes = \Sb\Helpers\BooksHelper::search($this->allResults, $this->listOptions->getSearch()->getValue());
                    if (!$tmpRes) {
                        \Sb\Flash\Flash::addItem(__("Aucun livre ne correspond à votre recherche.", "s1b"));
                        $this->allResults = $backedUpBooks;
                    }
                }

                // Filtering
                if ($this->listOptions->getFiltering()) {
                    \Sb\Helpers\BooksHelper::filter($this->allResults, $this->listOptions->getFiltering()->getValue(),
                            $this->listOptions->getFiltering()->getType());
                }
            }

            $params = array(
                'itemData' => $this->allResults,
                'perPage' => $this->nbResultsPerPage,
                'delta' => 8, // for 'Jumping'-style a lower number is better
                'append' => true,
                //'separator' => ' | ',
                'clearIfVoid' => false,
                'urlVar' => 'pagenumber',
                'useSessions' => false,
                'closeSession' => false,
                //'mode' => 'Sliding', //try switching modes
                'mode' => 'Jumping',
                'httpMethod' => 'GET'
            );

            $pager = \Sb\Lists\Pager\Pager::factory($params);
            $pageData = $pager->getPageData($pageId);
            $this->pagerLinks = $pager->getLinks($pageId);
            $this->nbItemsTot = $pager->numItems();
            $this->shownResults = $pageData;
            $offSet = $pager->getOffsetByPageId($pageId);
            if (($offSet) && (count($offSet) >= 2)) {
                $this->firstItemIdx = $offSet[0];
                $this->lastItemIdx = $offSet[1];
            }

            if ($this->shownResults) {
                $this->hasResults = true;
            }
        }
    }

    public function getPagerLinks() {
        return $this->pagerLinks;
    }

    public function getShownResults() {
        return $this->shownResults;
    }

    public function getHasResults() {
        return $this->hasResults;
    }

    public function getNbItemsTot() {
        return $this->nbItemsTot;
    }

    public function getFirstItemIdx() {
        return $this->firstItemIdx;
    }

    public function getLastItemIdx() {
        return $this->lastItemIdx;
    }

    public function getListOptions() {
        return $this->listOptions;
    }

}