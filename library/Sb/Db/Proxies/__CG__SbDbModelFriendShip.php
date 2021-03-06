<?php

namespace Proxies\__CG__\Sb\Db\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class FriendShip extends \Sb\Db\Model\FriendShip implements \Doctrine\ORM\Proxy\Proxy
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

    public function getUserId()
    {
        $this->__load();
        return parent::getUserId();
    }

    public function setUserId($userId)
    {
        $this->__load();
        return parent::setUserId($userId);
    }

    public function getFriendId()
    {
        $this->__load();
        return parent::getFriendId();
    }

    public function setFriendId($friendId)
    {
        $this->__load();
        return parent::setFriendId($friendId);
    }

    public function getCreationDate()
    {
        $this->__load();
        return parent::getCreationDate();
    }

    public function setCreationDate($creationDate)
    {
        $this->__load();
        return parent::setCreationDate($creationDate);
    }

    public function getAccepted()
    {
        $this->__load();
        return parent::getAccepted();
    }

    public function setAccepted($accepted)
    {
        $this->__load();
        return parent::setAccepted($accepted);
    }

    public function getValidated()
    {
        $this->__load();
        return parent::getValidated();
    }

    public function setValidated($validated)
    {
        $this->__load();
        return parent::setValidated($validated);
    }

    public function getUser_source()
    {
        $this->__load();
        return parent::getUser_source();
    }

    public function setUser_source($user_source)
    {
        $this->__load();
        return parent::setUser_source($user_source);
    }

    public function getUser_target()
    {
        $this->__load();
        return parent::getUser_target();
    }

    public function setUser_target($user_target)
    {
        $this->__load();
        return parent::setUser_target($user_target);
    }

    public function IsValid()
    {
        $this->__load();
        return parent::IsValid();
    }

    public function getRefused()
    {
        $this->__load();
        return parent::getRefused();
    }

    public function setRefused($refused)
    {
        $this->__load();
        return parent::setRefused($refused);
    }

    public function getFriendLastname()
    {
        $this->__load();
        return parent::getFriendLastname();
    }

    public function setFriendLastname($friendLastname)
    {
        $this->__load();
        return parent::setFriendLastname($friendLastname);
    }

    public function getFriendFirstname()
    {
        $this->__load();
        return parent::getFriendFirstname();
    }

    public function setFriendFirstname($friendFirstname)
    {
        $this->__load();
        return parent::setFriendFirstname($friendFirstname);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'creation_date', 'accepted', 'validated', 'user_source', 'user_target');
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