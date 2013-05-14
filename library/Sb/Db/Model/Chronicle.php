<?php

namespace Sb\Db\Model;

use Sb\Helpers\HTTPHelper;
use Sb\Helpers\StringHelper;
/** @Entity @Table(name="s1b_groupchronicles") */
class Chronicle implements \Sb\Db\Model\Model {

    function __construct() {

    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Group", inversedBy="chronicles")
     * @JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="chronicles")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ManyToOne(targetEntity="Book", inversedBy="chronicles", fetch="EAGER")
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

    /** @Column(type="string", length=100) */
    protected $source;

    /** @Column(type="string", length=250) */
    protected $keywords;

    /**
     * @ManyToOne(targetEntity="Tag", inversedBy="chronicles")
     * @JoinColumn(name="tag_id", referencedColumnName="id")
     */
    protected $tag;

    /** @Column(type="boolean") */
    protected $is_validated;

    /** @Column(type="integer") */
    protected $nb_views;

    /** @Column(type="string", length=250) */
    protected $image;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return Group
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
     * @return User $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return Book $book
     */
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

    public function getSource() {
        return $this->source;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    /**
     * @return String $keywords
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * @param String $keywords
     */
    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    /**
     * @return Tag $tag
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * @param Tag $tag
     */
    public function setTag($tag) {
        $this->tag = $tag;
    }

    /**
     * @return Boolean $is_validated
     */
    public function getIs_validated() {
        return $this->is_validated;
    }

    /**
     * @param Boolean $is_validated
     */
    public function setIs_validated($is_validated) {
        $this->is_validated = $is_validated;
    }

    /**
     * @return Integer $nb_views
     */
    public function getNb_views() {
        return $this->nb_views;
    }

    /**
     * @param Integer $nb_views
     */
    public function setNb_views($nb_views) {
        $this->nb_views = $nb_views;
    }

    /**
     * @return String $image
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param String $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

    public function IsValid() {
        return true;
    }

    /**
     * Get detail page link for chronicle
     * @return string the detail page link
     */
    public function getDetailLink() {
        if ($this->getTitle())
            return HTTPHelper::Link("chronique/" . StringHelper::sanitize(StringHelper::cleanHTML($this->getTitle())) . "-" . $this->getId());
        else
            return HTTPHelper::Link("chronique/chronique-" . $this->getId());
    }
    
}
