<?php

namespace Sb\Lists;

/**
 * Description of Sb\Lists\Options
 *
 * @author Didier
 */
class Options {

    private $sorting;
    private $paging;
    private $search;
    private $filtering;

    function __construct() {
        $this->sorting = null;
        $this->paging = null;
    }

    public function getSorting() {
        if ($this->sorting instanceof \Sb\Lists\Sorting) {
            return $this->sorting;
        } else {
            return null;
        }
    }

    public function setSorting($sorting) {
        $this->sorting = $sorting;
    }

    public function getPaging() {
        if ($this->paging instanceof \Sb\Lists\Paging) {
            return $this->paging;
        } else {
            return null;
        }
    }

    public function setPaging($paging) {
        $this->paging = $paging;
    }

    public function getSearch() {
        return $this->search;
    }

    public function setSearch($search) {
        $this->search= $search;
    }

    public function getFiltering() {
        return $this->filtering;
    }

    public function setFiltering($filtering) {
        $this->filtering = $filtering;
    }
}