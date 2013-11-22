<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_usersettings") */
class UserSetting implements \Sb\Db\Model\Model {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @OneToOne(targetEntity="User", inversedBy="setting")
      @JoinColumn(name="user_id", referencedColumnName="id") */
    protected $user;

    /** @Column(type="string", length=45) */
    protected $display_profile;

    /** @Column(type="string", length=45) */
    protected $display_email;
    
    /** @Column(type="string", length=45) */
    protected $display_birthday;

    /** @Column(type="string", length=11) */
    protected $display_wishlist;    

    /** @Column(type="string", length=45) */
    protected $allow_followers;
    
    /** @Column(type="string", length=45) */
    protected $send_messages;

    /** @Column(type="string", length=45) */
    protected $email_me;

    /** @Column(type="string", length=45) */
    protected $data_user;

    /** @Column(type="boolean") */
    protected $accept_newsletter;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDisplayProfile() {
        return $this->display_profile;
    }

    public function setDisplayProfile($displayProfile) {
        $this->display_profile = $displayProfile;
    }
    
    public function getDisplay_wishlist() {
        return $this->display_wishlist;
    }

    public function setDisplay_wishlist($display_wishlist) {
        $this->display_wishlist = $display_wishlist;
    }

	public function getAllowFollowers() {
        return $this->allow_followers;
    }

    public function setAllowFollowers($allowFollowers) {
        $this->allow_followers = $allowFollowers;
    }

    public function getDisplayEmail() {
        return $this->display_email;
    }

    public function setDisplayEmail($displayEmail) {
        $this->display_email = $displayEmail;
    }

    public function getSendMessages() {
        return $this->send_messages;
    }

    public function setSendMessages($sendMessages) {
        $this->send_messages = $sendMessages;
    }

    public function getEmailMe() {
        return $this->email_me;
    }

    public function setEmailMe($emailMe) {
        $this->email_me = $emailMe;
    }

    public function getDataUser() {
        return $this->data_user;
    }

    public function setDataUser($dataUser) {
        $this->data_user = $dataUser;
    }

    public function getDisplayBirthday() {
        return $this->display_birthday;
    }

    public function setDisplayBirthday($displayBirthday) {
        $this->display_birthday = $displayBirthday;
    }

    public function getAccept_newsletter() {
        return $this->accept_newsletter;
    }

    public function setAccept_newsletter($accept_newsletter) {
        $this->accept_newsletter = $accept_newsletter;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function IsValid() {
        return true;
    }

    public function addUser($user) {
        $this->user = $user;
    }

}