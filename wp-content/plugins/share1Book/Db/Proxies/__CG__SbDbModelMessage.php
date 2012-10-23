<?php

namespace Proxies\__CG__\Sb\Db\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Message extends \Sb\Db\Model\Message implements \Doctrine\ORM\Proxy\Proxy
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

    public function getSender()
    {
        $this->__load();
        return parent::getSender();
    }

    public function setSender(\Sb\Db\Model\User $sender)
    {
        $this->__load();
        return parent::setSender($sender);
    }

    public function getRecipient()
    {
        $this->__load();
        return parent::getRecipient();
    }

    public function setRecipient(\Sb\Db\Model\User $recipient)
    {
        $this->__load();
        return parent::setRecipient($recipient);
    }

    public function getDate()
    {
        $this->__load();
        return parent::getDate();
    }

    public function setDate($date)
    {
        $this->__load();
        return parent::setDate($date);
    }

    public function getTitle()
    {
        $this->__load();
        return parent::getTitle();
    }

    public function setTitle($title)
    {
        $this->__load();
        return parent::setTitle($title);
    }

    public function getMessage()
    {
        $this->__load();
        return parent::getMessage();
    }

    public function setMessage($message)
    {
        $this->__load();
        return parent::setMessage($message);
    }

    public function getIs_read()
    {
        $this->__load();
        return parent::getIs_read();
    }

    public function setIs_read($is_read)
    {
        $this->__load();
        return parent::setIs_read($is_read);
    }

    public function getIs_facebook_post()
    {
        $this->__load();
        return parent::getIs_facebook_post();
    }

    public function setIs_facebook_post($is_facebook_post)
    {
        $this->__load();
        return parent::setIs_facebook_post($is_facebook_post);
    }

    public function IsValid()
    {
        $this->__load();
        return parent::IsValid();
    }

    public function getSender_id()
    {
        $this->__load();
        return parent::getSender_id();
    }

    public function setSender_id($sender_id)
    {
        $this->__load();
        return parent::setSender_id($sender_id);
    }

    public function getRecipient_id()
    {
        $this->__load();
        return parent::getRecipient_id();
    }

    public function setRecipient_id($recipient_id)
    {
        $this->__load();
        return parent::setRecipient_id($recipient_id);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'date', 'title', 'message', 'is_read', 'is_facebook_post', 'sender', 'recipient');
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