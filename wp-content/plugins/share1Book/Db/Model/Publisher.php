<?php

use Doctrine\Common\Collections\ArrayCollection;


namespace Sb\Db\Model;


/** @Entity @Table(name="s1b_publishers") */
class Publisher implements \Sb\Db\Model\Model {

    public function __construct() {
        $this->books = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=200) */
    protected $name;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;

    /**
     * @OneToMany(targetEntity="Book", mappedBy="publisher")
     * @JoinColumn(name="id", referencedColumnName="publisher_id")
     */
    protected $books;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getCreationDate() {
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

    public function IsValid(){
        if (!$this->getName()){
            return false;
        }
        return true;
    }

    public function getBooks() {
        return $this->books;
    }

    public function setBooks($books) {
        $this->books = $books;
    }

}