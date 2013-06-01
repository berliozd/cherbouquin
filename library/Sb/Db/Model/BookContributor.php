<?php

namespace Sb\Db\Model;

/**
 * Description of BookContributor
 * Map une ligne de la table BookContributor
 * @author Didier
 */
class BookContributor implements \Sb\Db\Model\Model {

    public function __construct() {
        $this->lastModificationDate = new \DateTime();
    }

    /** @var int */
    private $id;

    /** @var int */
    private $bookId;

    /** @var int */
    private $contributorId;

    /** @var datetime */
    private $creationDate;

    /** @var DateTime */
    private $lastModificationDate;

    //~ Getters & setters

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
//        if ($this->id !== null && $this->id != $id) {
//            throw new \Exception('Cannot change identifier to ' . $id . ', already set to ' . $this->id);
//        }
        $this->id = (int) $id;
    }

    public function getBookId() {
        return $this->bookId;
    }

    public function setBookId($bookId) {
        $this->bookId = $bookId;
    }

    public function getContributorId() {
        return $this->contributorId;
    }

    public function setContributorId($contributorId) {
        $this->contributorId = $contributorId;
    }

        /**
     * @return DateTime
     */
    public function getCreationDate() {
        if (!$this->creationDate) {
            $this->creationDate = new \DateTime();
        }
        return $this->creationDate;
    }

    /**
     *
     * @param datetime $creationDate
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    /**
     * @return DateTime
     */
    public function getLastModificationDate() {
        return $this->lastModificationDate;
    }

    /**
     *
     * @param datetime $lastModificationDate
     */
    public function setLastModificationDate($lastModificationDate) {
        $this->lastModificationDate = $lastModificationDate;
    }

    /**
     *
     * @return boolean
     */
    public function IsValid() {
        return true;
    }

}

?>
