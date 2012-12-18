<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_userbooktags") */
class UserBookTag implements \Sb\Db\Model\Model {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    protected $userbook_id;
    protected $tag_id;
    protected $creation_date;
    protected $tag;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserBookId() {
        return $this->userbook_id;
    }

    public function setUserBookId($userBookId) {
        $this->userbook_id = $userBookId;
    }

    public function getTagId() {
        return $this->tag_id;
    }

    public function setTagId($tagId) {
        $this->tag_id = $tagId;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function setCreationDate($creationDate) {
        $this->creation_date = $creationDate;
    }

    public function getTag() {
        return $this->tag;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }

    public function IsValid() {
        return true;
    }

}