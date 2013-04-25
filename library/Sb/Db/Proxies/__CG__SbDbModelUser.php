<?php

namespace Proxies\__CG__\Sb\Db\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class User extends \Sb\Db\Model\User implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setId($id)
    {
        $this->__load();
        return parent::setId($id);
    }

    public function getFacebookId()
    {
        $this->__load();
        return parent::getFacebookId();
    }

    public function setFacebookId($facebookId)
    {
        $this->__load();
        return parent::setFacebookId($facebookId);
    }

    public function getConnexionType()
    {
        $this->__load();
        return parent::getConnexionType();
    }

    public function setConnexionType($connexionType)
    {
        $this->__load();
        return parent::setConnexionType($connexionType);
    }

    public function getFirstName()
    {
        $this->__load();
        return parent::getFirstName();
    }

    public function setFirstName($firstName)
    {
        $this->__load();
        return parent::setFirstName($firstName);
    }

    public function getLastName()
    {
        $this->__load();
        return parent::getLastName();
    }

    public function setLastName($lastName)
    {
        $this->__load();
        return parent::setLastName($lastName);
    }

    public function getUserName()
    {
        $this->__load();
        return parent::getUserName();
    }

    public function setUserName($userName)
    {
        $this->__load();
        return parent::setUserName($userName);
    }

    public function getEmail()
    {
        $this->__load();
        return parent::getEmail();
    }

    public function setEmail($email)
    {
        $this->__load();
        return parent::setEmail($email);
    }

    public function getPassword()
    {
        $this->__load();
        return parent::getPassword();
    }

    public function setPassword($password)
    {
        $this->__load();
        return parent::setPassword($password);
    }

    public function getGender()
    {
        $this->__load();
        return parent::getGender();
    }

    public function setGender($gender)
    {
        $this->__load();
        return parent::setGender($gender);
    }

    public function getAddress()
    {
        $this->__load();
        return parent::getAddress();
    }

    public function setAddress($address)
    {
        $this->__load();
        return parent::setAddress($address);
    }

    public function getCity()
    {
        $this->__load();
        return parent::getCity();
    }

    public function setCity($city)
    {
        $this->__load();
        return parent::setCity($city);
    }

    public function getZipCode()
    {
        $this->__load();
        return parent::getZipCode();
    }

    public function setZipCode($zipCode)
    {
        $this->__load();
        return parent::setZipCode($zipCode);
    }

    public function getCountry()
    {
        $this->__load();
        return parent::getCountry();
    }

    public function setCountry($country)
    {
        $this->__load();
        return parent::setCountry($country);
    }

    public function getBirthDay()
    {
        $this->__load();
        return parent::getBirthDay();
    }

    public function setBirthDay($birthDay)
    {
        $this->__load();
        return parent::setBirthDay($birthDay);
    }

    public function getFacebookLanguage()
    {
        $this->__load();
        return parent::getFacebookLanguage();
    }

    public function setFacebookLanguage($facebookLanguage)
    {
        $this->__load();
        return parent::setFacebookLanguage($facebookLanguage);
    }

    public function getLanguage()
    {
        $this->__load();
        return parent::getLanguage();
    }

    public function setLanguage($language)
    {
        $this->__load();
        return parent::setLanguage($language);
    }

    public function getToken()
    {
        $this->__load();
        return parent::getToken();
    }

    public function setToken($token)
    {
        $this->__load();
        return parent::setToken($token);
    }

    public function getTokenFacebook()
    {
        $this->__load();
        return parent::getTokenFacebook();
    }

    public function setTokenFacebook($tokenFacebook)
    {
        $this->__load();
        return parent::setTokenFacebook($tokenFacebook);
    }

    public function getActivated()
    {
        $this->__load();
        return parent::getActivated();
    }

    public function setActivated($activated)
    {
        $this->__load();
        return parent::setActivated($activated);
    }

    public function getDeleted()
    {
        $this->__load();
        return parent::getDeleted();
    }

    public function setDeleted($deleted)
    {
        $this->__load();
        return parent::setDeleted($deleted);
    }

    public function getGravatar()
    {
        $this->__load();
        return parent::getGravatar();
    }

    public function setGravatar($gravatar)
    {
        $this->__load();
        return parent::setGravatar($gravatar);
    }

    public function getPicture()
    {
        $this->__load();
        return parent::getPicture();
    }

    public function setPicture($picture)
    {
        $this->__load();
        return parent::setPicture($picture);
    }

    public function getPictureBig()
    {
        $this->__load();
        return parent::getPictureBig();
    }

    public function setPictureBig($pictureBig)
    {
        $this->__load();
        return parent::setPictureBig($pictureBig);
    }

    public function getCreated()
    {
        $this->__load();
        return parent::getCreated();
    }

    public function setCreated($created)
    {
        $this->__load();
        return parent::setCreated($created);
    }

    public function getLastLogin()
    {
        $this->__load();
        return parent::getLastLogin();
    }

    public function setLastLogin($lastLogin)
    {
        $this->__load();
        return parent::setLastLogin($lastLogin);
    }

    public function getNbUserBooks()
    {
        $this->__load();
        return parent::getNbUserBooks();
    }

    public function setNbUserBooks($nbUserBooks)
    {
        $this->__load();
        return parent::setNbUserBooks($nbUserBooks);
    }

    public function getUserBooks()
    {
        $this->__load();
        return parent::getUserBooks();
    }

    public function getNotDeletedUserBooks()
    {
        $this->__load();
        return parent::getNotDeletedUserBooks();
    }

    public function setUserBooks($userBooks)
    {
        $this->__load();
        return parent::setUserBooks($userBooks);
    }

    public function getUserevents()
    {
        $this->__load();
        return parent::getUserevents();
    }

    public function setUserevents($userevents)
    {
        $this->__load();
        return parent::setUserevents($userevents);
    }

    public function getMessages_sent()
    {
        $this->__load();
        return parent::getMessages_sent();
    }

    public function setMessages_sent($messages_sent)
    {
        $this->__load();
        return parent::setMessages_sent($messages_sent);
    }

    public function getMessages_received()
    {
        $this->__load();
        return parent::getMessages_received();
    }

    public function setMessages_received($messages_received)
    {
        $this->__load();
        return parent::setMessages_received($messages_received);
    }

    public function getFriendships_as_source()
    {
        $this->__load();
        return parent::getFriendships_as_source();
    }

    public function setFriendships_as_source($friendships_as_source)
    {
        $this->__load();
        return parent::setFriendships_as_source($friendships_as_source);
    }

    public function getFriendships_as_target()
    {
        $this->__load();
        return parent::getFriendships_as_target();
    }

    public function setFriendships_as_target($friendships_as_target)
    {
        $this->__load();
        return parent::setFriendships_as_target($friendships_as_target);
    }

    public function getInvitations()
    {
        $this->__load();
        return parent::getInvitations();
    }

    public function setInvitations($invitations)
    {
        $this->__load();
        return parent::setInvitations($invitations);
    }

    public function getChronicles()
    {
        $this->__load();
        return parent::getChronicles();
    }

    public function setChronicles($chronicles)
    {
        $this->__load();
        return parent::setChronicles($chronicles);
    }

    public function IsValid()
    {
        $this->__load();
        return parent::IsValid();
    }

    public function IsValidForS1bAuthentification()
    {
        $this->__load();
        return parent::IsValidForS1bAuthentification();
    }

    public function IsValidForFBAuthentification()
    {
        $this->__load();
        return parent::IsValidForFBAuthentification();
    }

    public function addUserBook(\Sb\Db\Model\UserBook $userBook)
    {
        $this->__load();
        return parent::addUserBook($userBook);
    }

    public function getSetting()
    {
        $this->__load();
        return parent::getSetting();
    }

    public function setSetting($setting)
    {
        $this->__load();
        return parent::setSetting($setting);
    }

    public function getFriendsForEmailing()
    {
        $this->__load();
        return parent::getFriendsForEmailing();
    }

    public function getAcceptedFriends()
    {
        $this->__load();
        return parent::getAcceptedFriends();
    }

    public function getPendingFriendShips()
    {
        $this->__load();
        return parent::getPendingFriendShips();
    }

    public function getUnReadReceivedMessages()
    {
        $this->__load();
        return parent::getUnReadReceivedMessages();
    }

    public function addMessagesReceived(\Sb\Db\Model\Message $message)
    {
        $this->__load();
        return parent::addMessagesReceived($message);
    }

    public function addMessagesSent(\Sb\Db\Model\Message $message)
    {
        $this->__load();
        return parent::addMessagesSent($message);
    }

    public function getFriendlyName()
    {
        $this->__load();
        return parent::getFriendlyName();
    }

    public function getPressreviews()
    {
        $this->__load();
        return parent::getPressreviews();
    }

    public function setPressreviews($pressreviews)
    {
        $this->__load();
        return parent::setPressreviews($pressreviews);
    }

    public function getGroupusers()
    {
        $this->__load();
        return parent::getGroupusers();
    }

    public function setGroupusers($groupusers)
    {
        $this->__load();
        return parent::setGroupusers($groupusers);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'facebook_id', 'connexion_type', 'first_name', 'last_name', 'user_name', 'email', 'password', 'gender', 'address', 'city', 'zipcode', 'country', 'birthday', 'facebook_language', 'language', 'token', 'token_facebook', 'activated', 'deleted', 'gravatar', 'picture', 'picture_big', 'created', 'last_login', 'setting', 'messages_sent', 'messages_received', 'userbooks', 'userevents', 'friendships_as_source', 'friendships_as_target', 'invitations', 'chronicles', 'pressreviews', 'groupusers');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}