<?php

namespace Sb\Db\Model;

/**
 * @Entity @Table(name="s1b_pressreviews_subscribers")
 */
class PressReviewsSubscriber implements Model {

    /**
     */
    function __construct() {

    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=100)
     */
    protected $email;

    /**
     * @Column(type="boolean")
     */
    protected $is_deleted = false;

    /**
     *
     * @return Integer $id
     */
    public function getId() {

        return $this->id;
    }

    /**
     *
     * @param Integer $id
     */
    public function setId($id) {

        $this->id = $id;
    }

    /**
     *
     * @return String $email
     */
    public function getEmail() {

        return $this->email;
    }

    /**
     *
     * @param String $email
     */
    public function setEmail($email) {

        $this->email = $email;
    }

    /**
     *
     * @return boolean $is_deleted
     */
    public function getIs_deleted() {

        return $this->is_deleted;
    }

    /**
     *
     * @param boolean $is_deleted
     */
    public function setIs_deleted($is_deleted) {

        $this->is_deleted = $is_deleted;
    }

    /**
     *
     * @see \Sb\Db\Model\Model::IsValid()
     */
    public function IsValid() {

        return true;
    }

}
