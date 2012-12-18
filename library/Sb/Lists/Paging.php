<?php

namespace Sb\Lists;

/**
 * Description of Sb\Lists\Paging
 *
 * @author Didier
 */
class Paging {

    private $currentPageId;

    public function getCurrentPageId() {
        return $this->currentPageId;
    }

    public function setCurrentPageId($currentPageId) {
        $this->currentPageId= $currentPageId;
    }
}

?>
