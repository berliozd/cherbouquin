<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_userevents") */
class UserEvent implements \Sb\Db\Model\Model {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

     /**
     * @ManyToOne(targetEntity="User", inversedBy="userevents")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
        
    /** @Column(type="integer") */
    protected $type_id;
    
    /** @Column(type="integer") */
    protected $item_id;
    
    /** @Column(type="string", length=5000) */
    protected $old_value;
    
    /** @Column(type="string", length=5000) */
    protected $new_value;
        
    /** @Column(type="datetime") */
    protected $creation_date;
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getType_id() {
        return $this->type_id;
    }

    public function setType_id($type_id) {
        $this->type_id = $type_id;
    }

    public function getItem_id() {
        return $this->item_id;
    }

    public function setItem_id($item_id) {
        $this->item_id = $item_id;
    }

    public function getOld_value() {
        return $this->old_value;
    }

    public function setOld_value($old_value) {
        $this->old_value = $old_value;
    }

    public function getNew_value() {
        return $this->new_value;
    }

    public function setNew_value($new_value) {
        $this->new_value = $new_value;
    }

    public function getCreation_date() {
        return $this->creation_date;
    }

    public function setCreation_date($creation_date) {
        $this->creation_date = $creation_date;
    }    

    public function IsValid() {
        return true;
    }    

}