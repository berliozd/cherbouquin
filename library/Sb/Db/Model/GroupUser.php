<?php
namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_group_users") */
class GroupUser implements Model {

    /**
     * 
     */
    function __construct() {

    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** 
     * @ManyToOne(targetEntity="Group", inversedBy="groupusers")
     * @JoinColumn(name="group_id", referencedColumnName="id") 
     */
    protected $group;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="groupusers")
     * @JoinColumn(name="user_id", referencedColumnName="id") 
     */
    protected $user;

    /** @Column(type="boolean") */
    protected $is_superadmin;

    /** @Column(type="boolean") */
    protected $is_importation_activated;

    /** @Column(type="boolean") */
    protected $is_anonymous;

    /**
     * @return Integer $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param Integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return Group $group
     */
    public function getGroup() {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup($group) {
        $this->group = $group;
    }

    /**
     * @return User $user
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return Boolean $is_superadmin
     */
    public function getIs_superadmin() {
        return $this->is_superadmin;
    }

    /**
     * @param Boolean $is_superadmin
     */
    public function setIs_superadmin($is_superadmin) {
        $this->is_superadmin = $is_superadmin;
    }

    /**
     * @return Boolean $is_importation_activated
     */
    public function getIs_importation_activated() {
        return $this->is_importation_activated;
    }

    /**
     * @param Boolean $is_importation_activated
     */
    public function setIs_importation_activated($is_importation_activated) {
        $this->is_importation_activated = $is_importation_activated;
    }

    /**
     * @return Boolean $is_anonymous
     */
    public function getIs_anonymous() {
        return $this->is_anonymous;
    }

    /**
     * @param Boolean $is_anonymous
     */
    public function setIs_anonymous($is_anonymous) {
        $this->is_anonymous = $is_anonymous;
    }

    /**
     * @see \Sb\Db\Model\Model::IsValid()
     */
    public function IsValid() {
        return true;
    }

}
