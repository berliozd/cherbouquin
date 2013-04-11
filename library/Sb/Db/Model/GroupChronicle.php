<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_groupchronicles") */
class GroupChronicle implements \Sb\Db\Model\Model {

    function __construct() {
        
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @group_id @Column(type="integer")
     */
    protected $group_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="groupchronicles")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ManyToOne(targetEntity="Book", inversedBy="groupchronicles", fetch="EAGER")
     * @JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected $book; // fetch="EAGER" because we need to get the book and his members automatically to store it into cache and have it available from restored from cache

    /** @Column(type="string", length=200) */
    protected $title;

    /** @Column(type="string", length=5000) */
    protected $text;

    /** @Column(type="integer") */
    protected $type_id;

    /** @Column(type="integer") */
    protected $link_type;

    /** @Column(type="string", length=255) */
    protected $link;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="boolean") */
    protected $is_published = false;

    /** @Column(type="string", length=100) */
    protected $source;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getGroup_id() {
        return $this->group_id;
    }

    public function setGroup_id($group_id) {
        $this->group_id = $group_id;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getBook() {
        return $this->book;
    }

    public function setBook($book) {
        $this->book = $book;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getType_id() {
        return $this->type_id;
    }

    public function setType_id($type_id) {
        $this->type_id = $type_id;
    }

    public function getLink_type() {
        return $this->link_type;
    }

    public function setLink_type($link_type) {
        $this->link_type = $link_type;
    }

    public function getLink() {
        return $this->link;
    }

    public function setLink($link) {
        $this->link = $link;
    }

    public function getCreation_date() {
        return $this->creation_date;
    }

    public function setCreation_date($creation_date) {
        $this->creation_date = $creation_date;
    }

    public function getIs_published() {
        return $this->is_published;
    }

    public function setIs_published($is_published) {
        $this->is_published = $is_published;
    }

    public function getSource() {
        return $this->source;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function IsValid() {
        return true;
    }

}