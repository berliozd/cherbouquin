<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_userbookgifts") */
class UserBookGift implements \Sb\Db\Model\Model {

    function __construct() {
        $this->last_modification_date = new \DateTime();
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="UserBook", inversedBy="giftsRelated")
     * @JoinColumn(name="userbook_id", referencedColumnName="id")
     * */
    protected $userbook;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="giftsDone")
     * @JoinColumn(name="offerer_id", referencedColumnName="id")
     * */
    protected $offerer;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;

    /** @Column(type="boolean") */
    protected $is_active = false;
    
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

    public function getOfferer() {
        return $this->offerer;
    }

    public function setOfferer($offerer) {
        $this->offerer = $offerer;
    }

    public function getCreation_date() {
        return $this->creation_date;
    }

    public function setCreation_date($creation_date) {
        $this->creation_date = $creation_date;
    }

    public function getLast_modification_date() {
        return $this->last_modification_date;
    }

    public function setLast_modification_date($last_modification_date) {
        $this->last_modification_date = $last_modification_date;
    }

    public function getIs_active() {
        return $this->is_active;
    }

    public function setIs_active($is_active) {
        $this->is_active = $is_active;
    }
    
    public function IsValid() {
        return true;
    }
}