<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_userbookcomments") */
class UserbookComment implements \Sb\Db\Model\Model {

    function __construct() {
        
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Userbook", inversedBy="comments")
     * @JoinColumn(name="userbook_id", referencedColumnName="id")
     */
    protected $userbook;
    
    /**
     * @ManyToOne(targetEntity="User", inversedBy="comments")
     * @JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /** @Column(type="string", length=5000) */
    protected $value;

    /** @Column(type="datetime") */
    protected $creation_date;
//    protected $creation_date_s;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserbook() {
        return $this->userbook;
    }

    public function setUserbook($userbook) {
        $this->userbook = $userbook;
    }
        
    public function getOwner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getCreation_date() {
//        if ($this->creation_date_s)
//            $this->creation_date = \Sb\Helpers\DateHelper::createDateTime($this->creation_date_s);
        return $this->creation_date;
    }

    public function setCreation_date($creation_date) {
        $this->creation_date = $creation_date;
//        if ($this->creation_date)
//            $this->creation_date_s = \Sb\Helpers\DateHelper::getDateForDB($this->creation_date);
    }

    public function IsValid() {
        return true;
    }
}