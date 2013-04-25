<?php

namespace Sb\Db\Model;

use \Doctrine\Common\Collections\ArrayCollection;

/** @Entity @Table(name="s1b_users") */
class User implements \Sb\Db\Model\Model {

    function __construct() {
        $this->userbooks = new ArrayCollection();
        $this->messages_received = new ArrayCollection();
        $this->messages_sent = new ArrayCollection();
        $this->friendships_as_user = new ArrayCollection();
        $this->friendships_as_friend = new ArrayCollection();
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @OneToOne(targetEntity="UserSetting", mappedBy="user") */
    protected $setting;

    /** @Column(type="bigint") */
    protected $facebook_id;

    /** @Column(type="string", length=3) */
    protected $connexion_type;

    /** @Column(type="string", length=30) */
    protected $first_name;

    /** @Column(type="string", length=30) */
    protected $last_name;

    /** @Column(type="string", length=30) */
    protected $user_name;

    /** @Column(type="string", length=100) */
    protected $email;

    /** @Column(type="string", length=255) */
    protected $password;

    /** @Column(type="string", length=10) */
    protected $gender;

    /** @Column(type="string", length=100) */
    protected $address;

    /** @Column(type="string", length=100) */
    protected $city;

    /** @Column(type="bigint") */
    protected $zipcode;

    /** @Column(type="string", length=100) */
    protected $country;

    /** @Column(type="date") */
    protected $birthday;

    /** @Column(type="string", length=5) */
    protected $facebook_language;

    /** @Column(type="string", length=45) */
    protected $language;

    /** @Column(type="string", length=45) */
    protected $token;

    /** @Column(type="string", length=48) */
    protected $token_facebook;

    /** @Column(type="boolean") */
    protected $activated = 0;

    /** @Column(type="boolean") */
    protected $deleted = 0;

    /** @Column(type="string", length=255) */
    protected $gravatar = "/public/Resources/images/avatars/avatar02.jpg";

    /** @Column(type="string", length=255) */
    protected $picture;

    /** @Column(type="string", length=255) */
    protected $picture_big;

    /** @Column(type="datetime") */
    protected $created;

    /** @Column(type="datetime") */
    protected $last_login;

    /** @OneToMany(targetEntity="Message", mappedBy="sender", fetch="EXTRA_LAZY") */
    protected $messages_sent;

    /** @OneToMany(targetEntity="Message", mappedBy="recipient", fetch="EXTRA_LAZY") */
    protected $messages_received;

    /** @OneToMany(targetEntity="UserBook", mappedBy="user", fetch="EXTRA_LAZY")  */
    protected $userbooks;

    /** @OneToMany(targetEntity="UserEvent", mappedBy="user", fetch="EXTRA_LAZY")  */
    protected $userevents;

    /** @OneToMany(targetEntity="FriendShip", mappedBy="user_source", fetch="EAGER") */
    protected $friendships_as_source;

    /** @OneToMany(targetEntity="FriendShip", mappedBy="user_target", fetch="EXTRA_LAZY") */
    protected $friendships_as_target;

    /** @OneToMany(targetEntity="Invitation", mappedBy="sender", fetch="EXTRA_LAZY") */
    protected $invitations;

    /** 
     * @OneToMany(targetEntity="Chronicle", mappedBy="user", fetch="EXTRA_LAZY")
     * @JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $chronicles;

    /**
     * @OneToMany(targetEntity="PressReview", mappedBy="user", fetch="EXTRA_LAZY")
     * @JoinColumn(name="id", referencedColumnName="user_id") 
     */
    protected $pressreviews;

    /**
     * @OneToMany(targetEntity="GroupUser", mappedBy="user", fetch="EXTRA_LAZY")
     * @JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $groupusers;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getFacebookId() {
        return $this->facebook_id;
    }

    public function setFacebookId($facebookId) {
        $this->facebook_id = $facebookId;
    }

    public function getConnexionType() {
        return $this->connexion_type;
    }

    public function setConnexionType($connexionType) {
        $this->connexion_type = $connexionType;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($firstName) {
        $this->first_name = $firstName;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($lastName) {
        $this->last_name = $lastName;
    }

    public function getUserName() {
        return $this->user_name;
    }

    public function setUserName($userName) {
        $this->user_name = $userName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getGender() {
        return $this->gender;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getZipCode() {
        return $this->zipcode;
    }

    public function setZipCode($zipCode) {
        $this->zipcode = $zipCode;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function getBirthDay() {
        return $this->birthday;
    }

    public function setBirthDay($birthDay) {
        $this->birthday = $birthDay;
    }

    public function getFacebookLanguage() {
        return $this->facebook_language;
    }

    public function setFacebookLanguage($facebookLanguage) {
        $this->facebook_language = $facebookLanguage;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function getTokenFacebook() {
        return $this->token_facebook;
    }

    public function setTokenFacebook($tokenFacebook) {
        $this->token_facebook = $tokenFacebook;
    }

    public function getActivated() {
        return $this->activated;
    }

    public function setActivated($activated) {
        $this->activated = $activated;
    }

    public function getDeleted() {
        return $this->deleted;
    }

    public function setDeleted($deleted) {
        $this->deleted = $deleted;
    }

    public function getGravatar() {
        return $this->gravatar;
    }

    public function setGravatar($gravatar) {
        $this->gravatar = $gravatar;
    }

    public function getPicture() {
        return $this->picture;
    }

    public function setPicture($picture) {
        $this->picture = $picture;
    }

    public function getPictureBig() {
        return $this->picture_big;
    }

    public function setPictureBig($pictureBig) {
        $this->picture_big = $pictureBig;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreated($created) {
        $this->created = $created;
    }

    public function getLastLogin() {
        return $this->last_login;
    }

    public function setLastLogin($lastLogin) {
        $this->last_login = $lastLogin;
    }

    public function getNbUserBooks() {
        return $this->nb_of_userbooks;
    }

    public function setNbUserBooks($nbUserBooks) {
        $this->nb_of_userbooks = $nbUserBooks;
    }

    public function getUserBooks() {
        return $this->userbooks;
    }

    public function getNotDeletedUserBooks() {
        $tmp = array_filter($this->userbooks->toArray(), array(&$this, "isNotDeleted"));
        return array_values($tmp);
    }

    public function setUserBooks($userBooks) {
        $this->userbooks = $userBooks;
    }

    public function getUserevents() {
        return $this->userevents;
    }

    public function setUserevents($userevents) {
        $this->userevents = $userevents;
    }

    public function getMessages_sent() {
        return $this->messages_sent;
    }

    public function setMessages_sent($messages_sent) {
        $this->messages_sent = $messages_sent;
    }

    public function getMessages_received() {
        return $this->messages_received;
    }

    public function setMessages_received($messages_received) {
        $this->messages_received = $messages_received;
    }

    public function getFriendships_as_source() {
        return $this->friendships_as_source;
    }

    public function setFriendships_as_source($friendships_as_source) {
        $this->friendships_as_source = $friendships_as_source;
    }

    public function getFriendships_as_target() {
        return $this->friendships_as_target;
    }

    public function setFriendships_as_target($friendships_as_target) {
        $this->friendships_as_target = $friendships_as_target;
    }

    public function getInvitations() {
        return $this->invitations;
    }

    public function setInvitations($invitations) {
        $this->invitations = $invitations;
    }

    public function getChronicles() {
        return $this->chronicles;
    }

    public function setChronicles($chronicles) {
        $this->chronicles = $chronicles;
    }

    public function IsValid() {
        return true;
    }

    public function IsValidForS1bAuthentification() {
        if ($this->getEmail() && $this->getPassword())
            return true;
        return false;
    }

    public function IsValidForFBAuthentification() {
        if ($this->getEmail() && $this->getFacebookId())
            return true;
        return false;
    }

    public function addUserBook(\Sb\Db\Model\UserBook $userBook) {
        $this->userbooks[] = $userBook;
    }

    public function getSetting() {
        return $this->setting;
    }

    public function setSetting($setting) {
        $this->setting = $setting;
        $setting->addUser($this);
    }

    public function getFriendsForEmailing() {
        // Get the friendship relation where the current user appears as source
        $friendShips = $this->getFriendships_as_source();
        $friendShips = $friendShips->toArray();
        // filter accepted friendship
        $friendShips = array_filter($friendShips, array(&$this, "isAcceptedFriendShip"));
        $friendShips = array_filter($friendShips, array(&$this, "isTargetUserInFriendShipNotDeleted"));
        // user in each friendships
        $friends = null;
        if ($friendShips && count($friendShips) > 0)
            $friends = array_map(array(&$this, "getFriendInFriendShip"), $friendShips);
        return $friends;
    }

    public function getAcceptedFriends() {
        // Get the friendship relation where the current user appears as source
        $friendShips = $this->getFriendships_as_source();
        $friendShips = $friendShips->toArray();
        // filter accepted friendship
        $friendShips = array_filter($friendShips, array(&$this, "isAcceptedFriendShip"));
        $friendShips = array_filter($friendShips, array(&$this, "isTargetUserInFriendShipNotDeleted"));
        // user in each friendships
        $friends = null;
        if ($friendShips && count($friendShips) > 0)
            $friends = array_map(array(&$this, "getFriendInFriendShip"), $friendShips);
        return $friends;
    }

    public function getPendingFriendShips() {
        $friendShips = $this->getFriendships_as_target();
        $friendShips = $friendShips->toArray();
        // filter accepted friendship
        $friendShips = array_filter($friendShips, array(&$this, "isPendingFriendShip"));
        return $friendShips;
    }

    public function getUnReadReceivedMessages() {
        $messages = $this->getMessages_received();
        $messages = $messages->toArray();
        // filter accepted friendship
        $unReadMessages = array_filter($messages, array(&$this, "isUnReadMessage"));
        return $unReadMessages;
    }

    public function addMessagesReceived(\Sb\Db\Model\Message $message) {
        $this->messages_received[] = $message;
    }

    public function addMessagesSent(\Sb\Db\Model\Message $message) {
        $this->messages_sent[] = $message;
    }

    private function getFriendInFriendShip(\Sb\Db\Model\FriendShip $friendShip) {
        return $friendShip->getUser_target();
    }

    private function isAcceptedFriendShip(\Sb\Db\Model\FriendShip $friendShip) {
        if ($friendShip->getAccepted()) {
            return true;
        }
        return false;
    }

    private function isUnReadMessage(\Sb\Db\Model\Message $message) {
        if (!$message->getIs_read()) {
            return true;
        }
        return false;
    }

    private function userAcceptEmail(\Sb\Db\Model\User $user) {
        $setting = $user->getSetting();
        if ($setting) {
            if ($setting->getEmailMe() == "Yes") {
                return true;
            }
        }
    }

    private function isPendingFriendShip(\Sb\Db\Model\FriendShip $friendShip) {
        if (!$friendShip->getValidated()) {
            return true;
        }
        return false;
    }

    private function isNotDeleted(\Sb\Db\Model\UserBook $userBook) {
        return !$userBook->getIs_deleted();
    }

    private function isTargetUserInFriendShipNotDeleted(\Sb\Db\Model\FriendShip $friendShip) {
        return !$friendShip->getUser_target()->getDeleted();
    }

    public function getFriendlyName() {
        return ucfirst(\Sb\Helpers\StringHelper::tronque(strtolower($this->getFirstName()), 20)) . " " . strtoupper(mb_substr($this->getLastName(), 0, 1)) . ".";
    }
    /**
     * @return Collection of PressReview $pressreviews
     */
    public function getPressreviews() {
        return $this->pressreviews;
    }

    /**
     * @param Collection of PressReview $pressreviews
     */
    public function setPressreviews($pressreviews) {
        $this->pressreviews = $pressreviews;
    }
    
	/**
	 * @return Collection of GroupUser $groupusers
	 */
	public function getGroupusers() {
		return $this->groupusers;
	}

	/**
	 * @param Collection of GroupUser $groupusers
	 */
	public function setGroupusers($groupusers) {
		$this->groupusers = $groupusers;
	}


    
}
