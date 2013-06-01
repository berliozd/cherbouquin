<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_contributors") */
class Contributor implements \Sb\Db\Model\Model {

    public function __construct() {
        
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=200) */
    protected $full_name;

    /** @Column(type="string", length=100) */
    protected $first_name;

    /** @Column(type="string", length=100) */
    protected $last_name;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;

    /** @Column(type="integer") */
    protected $type_id;

    /**
     * @ManyToMany(targetEntity="Book", fetch="EXTRA_LAZY")
     * @JoinTable(name="s1b_bookcontributors",
     *      joinColumns={@JoinColumn(name="contributor_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="book_id", referencedColumnName="id")}
     *      )
     */
    protected $books;

    //~ Getters & setters

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = (int) $id;
    }

    public function getFullName() {
        return $this->full_name;
    }

    public function setFullName($fullName) {
        $this->full_name = trim($fullName);
    }

    public function getFirst_name() {
        return $this->first_name;
    }

    public function setFirst_name($first_name) {
        $this->first_name = $first_name;
    }

    public function getLast_name() {
        return $this->last_name;
    }

    public function setLast_name($last_name) {
        $this->last_name = $last_name;
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

    public function getTypeId() {
        return $this->type_id;
    }

    public function setTypeId($typeId) {
        $this->type_id = $typeId;
    }

    public function getBooks() {
        return $this->books;
    }

    public function setBooks($books) {
        $this->books = $books;
    }

    public function IsValid() {
        return true;
    }

    public function getName() {
        if (($this->getFirst_name() != "") && ($this->getLast_name() != ""))
            return $this->getFirst_name() . " " . $this->getLast_name();
        return $this->getFullName();
    }
}