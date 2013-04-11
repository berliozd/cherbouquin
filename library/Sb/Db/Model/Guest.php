<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_guests") */
class Guest implements \Sb\Db\Model\Model {

    function __construct() {
        $this->invitations = new \Doctrine\Common\Collections\ArrayCollection();    
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=100) */
    protected $name;

    /** @Column(type="string", length=100) */
    protected $email;

    /** @Column(type="datetime") */
    protected $creation_date;
    
    /** @OneToMany(targetEntity="Invitation", mappedBy="guest", fetch="EXTRA_LAZY")
      @JoinColumn(name="id", referencedColumnName="guest_id") */
    protected $invitations;

    /**
     * @OneToMany(targetEntity="Lending", mappedBy="guest", fetch="EXTRA_LAZY")
     * @JoinColumn(name="id", referencedColumnName="guest_id")
     * */
    protected $lendings;

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

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getCreation_date() {
        return $this->creation_date;
    }

    public function setCreation_date($creation_date) {
        $this->creation_date = $creation_date;
    }

    public function getInvitations() {
        return $this->invitations;
    }

    public function setInvitations($invitations) {
        $this->invitations = $invitations;
    }

    public function addInvitations(Invitation $invitation) {
        $this->invitations[] = $invitation;
        $invitation->setGuest($this);
    }

    public function IsValid() {
        return true;
    }

}