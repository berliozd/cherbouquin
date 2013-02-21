<?php

namespace Proxies\__CG__\Sb\Db\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class UserbookComment extends \Sb\Db\Model\UserbookComment implements \Doctrine\ORM\Proxy\Proxy
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

    public function getUserbook()
    {
        $this->__load();
        return parent::getUserbook();
    }

    public function setUserbook($userbook)
    {
        $this->__load();
        return parent::setUserbook($userbook);
    }

    public function getOwner()
    {
        $this->__load();
        return parent::getOwner();
    }

    public function setOwner($owner)
    {
        $this->__load();
        return parent::setOwner($owner);
    }

    public function getValue()
    {
        $this->__load();
        return parent::getValue();
    }

    public function setValue($value)
    {
        $this->__load();
        return parent::setValue($value);
    }

    public function getCreation_date()
    {
        $this->__load();
        return parent::getCreation_date();
    }

    public function setCreation_date($creation_date)
    {
        $this->__load();
        return parent::setCreation_date($creation_date);
    }

    public function IsValid()
    {
        $this->__load();
        return parent::IsValid();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'value', 'creation_date', 'userbook', 'owner');
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