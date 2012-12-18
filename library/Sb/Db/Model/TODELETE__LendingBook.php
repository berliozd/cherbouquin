<?php

namespace Sb\Db\Model;

/**
 * Map une ligne de la table Lending jointe avec la table Book afin d'avoir certaines infos du livre (le titre principalement)
 */
class LendingBook extends \Sb\Db\Model\Lending implements \Sb\Db\Model\Model {

    protected $title;

    /**
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = trim($title);
    }

}

?>
