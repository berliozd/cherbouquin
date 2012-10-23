<?php

use Doctrine\Common\Collections\ArrayCollection;


namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_readingstates") */
class ReadingState implements \Sb\Db\Model\Model {

    function __construct() {
        $now = new \DateTime();
        $this->creation_date = $now;
        $this->last_modification_date = $now;
        $this->userbooks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=20) */
    protected $label;

    /** @Column(type="string", length=10) */
    protected $code;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;

    /**
     * @OneToMany(targetEntity="UserBook", mappedBy="reading_state")
     * @JoinColumn(name="id", referencedColumnName="reading_state_id")
     */
    protected $userbooks;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setLabel($label) {
        $this->label = trim($label);
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

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function IsValid() {
        return true;
    }

}