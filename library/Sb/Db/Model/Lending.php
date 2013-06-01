<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_lendings") */
class Lending implements \Sb\Db\Model\Model {

    function __construct() {
        $this->last_modification_date = new \DateTime();
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="UserBook", inversedBy="lendings", fetch="EAGER")
     * @JoinColumn(name="userbook_id", referencedColumnName="id")
     * */
    protected $userbook;

    /**
     * @ManyToOne(targetEntity="UserBook", inversedBy="borrowings", fetch="EAGER")
     * @JoinColumn(name="borrower_userbook_id", referencedColumnName="id")
     * */
    protected $borrower_userbook;
    
    /**
     * @ManyToOne(targetEntity="Guest", inversedBy="lendings")
     * @JoinColumn(name="guest_id", referencedColumnName="id")
     * */
    protected $guest;

    /** @Column(type="datetime") */
    protected $start_date;
    protected $start_date_s = "";

    /** @Column(type="datetime") */
    protected $end_date;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;

    /** @Column(type="integer") */
    protected $state;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getStartDate() {
        // Initialisation de la date de publication à partir du membre suffixé S (de type string) pour palier au problème
        // de perte des données sur les dates lors de la sérialisation pour le stockage dans le cache
        if ($this->start_date_s)
            $this->start_date = \Sb\Helpers\DateHelper::createDateTime($this->start_date_s);
        return $this->start_date;
    }

    public function setStartDate($startDate) {
        $this->start_date = $startDate;
        // stocke une version string de la date pour utilisation lors des serialization/deserialization
        if ($startDate) {
            $this->start_date_s = \Sb\Helpers\DateHelper::getDateForDB($startDate);
        }
    }

    public function getStartDateS() {
        return $this->start_date_s;
    }

    public function getEndDate() {
        return $this->end_date;
    }

    public function setEndDate($endDate) {
        $this->end_date = $endDate;
    }

    public function getCreationDate() {
        if (!$this->creation_date) {
            $this->creation_date = new \DateTime();
        }
        return $this->creation_date;
    }

    public function setCreationDate($creationDate) {
        $this->creation_date = $creationDate;
    }

    public function getLastModificationDate() {
        return $this->last_modification_date;
    }

    public function setLastModificationDate($lastModificationDate) {
        $this->last_modification_date = $lastModificationDate;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getUserbook() {
        return $this->userbook;
    }

    public function setUserbook(\Sb\Db\Model\UserBook $userbook) {
        $this->userbook = $userbook;
        $userbook->addLending($this);
    }

    public function getBorrower_userbook() {
        return $this->borrower_userbook;
    }

    public function setBorrower_userbook(\Sb\Db\Model\UserBook $borrower_userbook) {
        $this->borrower_userbook = $borrower_userbook;
        $borrower_userbook->addBorrowing($this);
    }

    public function IsValid() {
        if (!$this->getUserBookId())
            return false;
        if (!$this->getBorrowerUserBookId())
            return false;
        return true;
    }

    public function isActive() {
        if ($this->state != \Sb\Lending\Model\LendingState::IN_ACTIVE)
            return true;
    }

    // TODO : delete
    protected $userBookId;
    protected $borrowerUserBookId;
    protected $bookId;
    protected $lenderId;
    protected $borrowerId;
    protected $lenderFirstName = "";
    protected $lenderLastName = "";
    protected $borrowerFirstName = "";
    protected $borrowerLastName = "";

    public function getBookId() {
        return $this->bookId;
    }

    public function setBookId($bookId) {
        $this->bookId = $bookId;
    }

    public function getLenderId() {
        return $this->lenderId;
    }

    public function setLenderId($lenderId) {
        $this->lenderId = $lenderId;
    }

    public function getBorrowerId() {
        return $this->borrowerId;
    }

    public function setBorrowerId($borrowerId) {
        $this->borrowerId = $borrowerId;
    }

    public function getLenderFirstName() {
        return $this->lenderFirstName;
    }

    public function setLenderFirstName($lenderFirstName) {
        $this->lenderFirstName = $lenderFirstName;
    }

    public function getLenderLastName() {
        return $this->lenderLastName;
    }

    public function setLenderLastName($lenderLastName) {
        $this->lenderLastName = $lenderLastName;
    }

    public function getBorrowerFirstName() {
        return $this->borrowerFirstName;
    }

    public function setBorrowerFirstName($borrowerFirstName) {
        $this->borrowerFirstName = $borrowerFirstName;
    }

    public function getBorrowerLastName() {
        return $this->borrowerLastName;
    }

    public function setBorrowerLastName($borrowerLastName) {
        $this->borrowerLastName = $borrowerLastName;
    }

    public function getUserBookId() {
        return $this->userBookId;
    }

    public function setUserBookId($userBookId) {
        $this->userBookId = $userBookId;
    }

    public function getBorrowerUserBookId() {
        return $this->borrowerUserBookId;
    }

    public function setBorrowerUserBookId($borrowerUserBookId) {
        $this->borrowerUserBookId = $borrowerUserBookId;
    }
    
    public function getGuest() {
        return $this->guest;
    }

    public function setGuest($guest) {
        $this->guest = $guest;
    }

}