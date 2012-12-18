<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_friendships") */
class FriendShip implements \Sb\Db\Model\Model {

    function __construct() {

    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="friendships_as_source", fetch="EXTRA_LAZY")
     * @JoinColumn(name="source_user_id", referencedColumnName="id")
     */
    protected $user_source;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="friendships_as_target", fetch="EXTRA_LAZY")
     * @JoinColumn(name="target_user_id", referencedColumnName="id")
     */
    protected $user_target;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="boolean") */
    protected $accepted = 0;

    /** @Column(type="boolean") */
    protected $validated = 0;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($userId) {
        $this->user_id = $userId;
    }

    public function getFriendId() {
        return $this->friend_id;
    }

    public function setFriendId($friendId) {
        $this->friend_id = $friendId;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function setCreationDate($creationDate) {
        $this->creation_date = $creationDate;
    }

    public function getAccepted() {
        return $this->accepted;
    }

    public function setAccepted($accepted) {
        $this->accepted = $accepted;
    }

    public function getValidated() {
        return $this->validated;
    }

    public function setValidated($validated) {
        $this->validated = $validated;
    }

    public function getUser_source() {
        return $this->user_source;
    }

    public function setUser_source($user_source) {
        $this->user_source = $user_source;
    }

    public function getUser_target() {
        return $this->user_target;
    }

    public function setUser_target($user_target) {
        $this->user_target = $user_target;
    }

    public function IsValid() {
        return true;
    }

    // TODO : delete
    protected $user_id;
    protected $friend_id;
    protected $refused;
    protected $friend_last_name;
    protected $friend_first_name;

    public function getRefused() {
        return $this->refused;
    }

    public function setRefused($refused) {
        $this->refused = $refused;
    }

    public function getFriendLastname() {
        return $this->friend_last_name;
    }

    public function setFriendLastname($friendLastname) {
        $this->friend_last_name = $friendLastname;
    }

    public function getFriendFirstname() {
        return $this->friend_first_name;
    }

    public function setFriendFirstname($friendFirstname) {
        $this->friend_first_name = $friendFirstname;
    }


}