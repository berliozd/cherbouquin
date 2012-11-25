<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_invitations") */
class Invitation implements \Sb\Db\Model\Model  {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="invitations")
     * @JoinColumn(name="sender_id", referencedColumnName="id")
     */
    protected $sender;

    /** @ManyToOne(targetEntity="Guest", inversedBy="invitations") 
     * * @JoinColumn(name="guest_id", referencedColumnName="id")
     */
    protected $guest;
    
    /** @Column(type="datetime") */
    protected $creation_date;
    
    /** @Column(type="datetime") */
    protected $last_modification_date;

    /** @Column(type="boolean") */
    protected $is_accepted = false;

    /** @Column(type="boolean") */
    protected $is_validated = false;
    
    /** @Column(type="string", length=45) */
    protected $token;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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
        
    public function getSender() {
        return $this->sender;
    }

    public function setSender($sender) {
        $this->sender = $sender;
    }
    
    public function getGuest() {
        return $this->guest;
    }

    public function setGuest($guest) {
        $this->guest = $guest;
    }
    
    public function getIs_accepted() {
        return $this->is_accepted;
    }

    public function setIs_accepted($is_accepted) {
        $this->is_accepted = $is_accepted;
    }

    public function getIs_validated() {
        return $this->is_validated;
    }

    public function setIs_validated($is_validated) {
        $this->is_validated = $is_validated;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }
        
    public function IsValid() {
        return true;
    }
}