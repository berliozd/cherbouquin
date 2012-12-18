<?php

namespace Sb\Lists;

class PaginatedList {

    private $navigationBar;
    private $totalPages = 0;
    private $firstPage = 0;
    private $lastPage = 0;
    private $items;
    private $itemPerPage;

    function __construct($data, $itemPerPage) {
        $this->itemPerPage = $itemPerPage;
        $this->load($data);
    }

    private function load($data) {
        $pageId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'pagenumber', 1);
        $params = array(
            'itemData' => $data,
            'perPage' => $this->itemPerPage,
            'delta' => 8,
            'append' => true,
            'clearIfVoid' => false,
            'urlVar' => 'pagenumber',
            'useSessions' => false,
            'closeSession' => false,
            'mode' => 'Jumping',
            'httpMethod' => 'GET'
        );
        $pager = \Sb\Lists\Pager\Pager::factory($params);

        $pageData = $pager->getPageData($pageId);
        $this->items = $pageData;
        $links = $pager->getLinks($pageId);
        $this->navigationBar = $links['all'];

        $offSet = $pager->getOffsetByPageId($pageId);
        if (($offSet) && (count($offSet) >= 2)) {
            $this->firstPage = $offSet[0];
            $this->lastPage = $offSet[1];
        }
        $this->totalPages = $pager->numItems();
    }

    public function getItems() {
        return $this->items;
    }

    public function getNavigationBar() {
        return $this->navigationBar;
    }

    public function getTotalPages() {
        return $this->totalPages;
    }

    public function getFirstPage() {
        return $this->firstPage;
    }

    public function getLastPage() {
        return $this->lastPage;
    }

}

?>
