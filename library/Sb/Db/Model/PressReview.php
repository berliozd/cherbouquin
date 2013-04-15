<?php
namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_pressreviews") */
class PressReview implements Model {

    /**
     * 
     */
    function __construct() {

    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="pressreviews")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ManyToOne(targetEntity="Book", inversedBy="pressreviews")
     * @JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected $book;

    /** @Column(type="string", length=150) */
    protected $title;

    /** @Column(type="string", length=2000) */
    protected $text;

    /** @Column(type="string", length=255) */
    protected $link;

    /**
     * @ManyToOne(targetEntity="Media", inversedBy="pressreviews")
     * @JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $media;

    /** @Column(type="string", length=50) */
    protected $author;

    /** @Column(type="datetime") */
    protected $date;

    /** @Column(type="string", length=250) */
    protected $keywords;

    /**
     * @ManyToOne(targetEntity="Tag", inversedBy="pressreviews")
     * @JoinColumn(name="tag_id", referencedColumnName="id")
     */
    protected $tag;

    /** @Column(type="boolean") */
    protected $is_validated;

    /** @Column(type="integer") */
    protected $type;

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
     * @return Book $book
     */
    public function getBook() {
        return $this->book;
    }

    /**
     * @param Book $book
     */
    public function setBook($book) {
        $this->book = $book;
    }

    /**
     * @return String $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param String $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return String $text
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param String $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @return String $link
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param String $link
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * @return Media $media
     */
    public function getMedia() {
        return $this->media;
    }

    /**
     * @param Media $media
     */
    public function setMedia($media) {
        $this->media = $media;
    }

    /**
     * @return String $author
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param String $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @return the $date
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param Datetime $date
     */
    public function setDate($date) {
        $this->date = $date;
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
     * @return Integer $type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param Integer $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @see \Sb\Db\Model\Model::IsValid()
     */
    public function IsValid() {
        return true;
    }

}
