<?php

namespace Proxies\__CG__\Sb\Db\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Tag extends \Sb\Db\Model\Tag implements \Doctrine\ORM\Proxy\Proxy
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

    public function getLabel()
    {
        $this->__load();
        return parent::getLabel();
    }

    public function setLabel($label)
    {
        $this->__load();
        return parent::setLabel($label);
    }

    public function getLabel_en_us()
    {
        $this->__load();
        return parent::getLabel_en_us();
    }

    public function setLabel_en_us($label_en_us)
    {
        $this->__load();
        return parent::setLabel_en_us($label_en_us);
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

    public function getUserBooks()
    {
        $this->__load();
        return parent::getUserBooks();
    }

    public function setUserBooks($userBooks)
    {
        $this->__load();
        return parent::setUserBooks($userBooks);
    }

    public function IsValid()
    {
        $this->__load();
        return parent::IsValid();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'label', 'label_en_us', 'creation_date', 'userbooks');
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