<?php

use Doctrine\Common\Collections\ArrayCollection;

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

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;

    /** @Column(type="integer") */
    protected $type_id;

    /**
     * @ManyToMany(targetEntity="Book")
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

    public function getCreationDate() {
        if (!$this->creation_date) {
            $this->creation_date = now();
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

}