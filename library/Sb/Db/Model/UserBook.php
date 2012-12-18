<?php

namespace Sb\Db\Model;

use \Doctrine\Common\Collections\ArrayCollection;

/** @Entity @Table(name="s1b_userbooks") */
class UserBook implements \Sb\Db\Model\Model {

    private $needToUpdateBook = false; // flag to tell us if the associated book need to be updated
    private $ratingDiff; // used when updating the userbook to pass the onfo to the book to update the aggregate fields
    private $ratingAdded; // used when updating the userbook to pass the onfo to the book to update the aggregate fields
    private $blowOfHeartAdded; // used when updating the userbook to pass the onfo to the book to update the aggregate fields
    private $blowOfHeartRemoved; // used when updating the userbook to pass the onfo to the book to update the aggregate fields

    function __construct() {
        $this->tags = new ArrayCollection();
        $this->lendings = new ArrayCollection();
        $this->borrowings = new ArrayCollection();

        // assigning not read state
        $notReadReadingState = \Sb\Db\Dao\ReadingStateDao::getInstance()->get(\Sb\Db\Dao\UserBookDao::NOTREAD_STATE_ID);
        $this->reading_state = $notReadReadingState;
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="userbooks")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ManyToOne(targetEntity="Book", inversedBy="userbooks")
     * @JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected $book;

    /**
     * @ManyToOne(targetEntity="ReadingState", inversedBy="userbooks")
     * @JoinColumn(name="reading_state_id", referencedColumnName="id")
     */
    protected $reading_state;

    /** @Column(type="integer", length=200) */
    protected $rating = null;

    /** @Column(type="boolean") */
    protected $is_blow_of_heart = false;

    /** @Column(type="string", length=5000) */
    protected $review;

    /** @Column(type="boolean") */
    protected $is_wished = false;

    /** @Column(type="boolean") */
    protected $is_owned = false;

    /** @Column(type="boolean") */
    protected $is_deleted = false;

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;

    /** @Column(type="datetime") */
    protected $reading_date;
    protected $reading_date_s;

    /** @Column(type="boolean") */
    protected $borrowed_once = false;

    /** @Column(type="boolean") */
    protected $lent_once = false;

    /** @Column(type="string", length=250) */
    protected $hyperlink;

    /**
     * @ManyToMany(targetEntity="Tag")
     * @JoinTable(name="s1b_userbooktags",
     *      joinColumns={@JoinColumn(name="userbook_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     * */
    protected $tags;

    /**
     * @OneToMany(targetEntity="Lending", mappedBy="userbook")
     * @JoinColumn(name="id", referencedColumnName="userbook_id")
     * */
    protected $lendings;

    /**
     * @OneToMany(targetEntity="Lending", mappedBy="borrower_userbook")
     * @JoinColumn(name="id", referencedColumnName="borrower_userbook_id")
     * */
    protected $borrowings;

    /**
     * @OneToMany(targetEntity="UserBookGift", mappedBy="userbook")
     * @JoinColumn(name="id", referencedColumnName="userbook_id")
     * */
    protected $giftsRelated;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        if ($this->id !== null && $this->id != $id) {
            throw new Exception('Cannot change identifier to ' . $id . ', already set to ' . $this->id);
        }
        $this->id = $id;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(\Sb\Db\Model\User $user) {
        $this->user = $user;
        $user->addUserBook($this);
    }

    /**
     *
     * @return \Sb\Db\Model\Book
     */
    public function getBook() {
        return $this->book;
    }

    public function setBook(\Sb\Db\Model\Book $book) {
        $this->book = $book;
        $book->addUserBook($this);
    }

    public function getRating() {
        return $this->rating;
    }

    public function setRating($rating) {
        if ((!$this->rating) || ($this->rating != $rating)) {

            // no rating before => adding one now
            if ((!$this->rating && $this->rating != 0) && $rating)
                $this->ratingAdded = true;

            if ($this->rating)
                $this->ratingDiff = ($rating - $this->rating);
            else
                $this->ratingDiff = $rating;

            $this->needToUpdateBook = true;
        }
        $this->rating = $rating;
    }

    public function getIsBlowOfHeart() {
        return $this->is_blow_of_heart;
    }

    public function setIsBlowOfHeart($isBlowOfHeart) {
        if ((!$this->is_blow_of_heart) || ($this->is_blow_of_heart != $isBlowOfHeart)) {

            // if removing the blow of heart
            if ($this->is_blow_of_heart && !$isBlowOfHeart)
                $this->blowOfHeartRemoved = true;

            // if adding the blow of heart
            if ((!$this->is_blow_of_heart || !isset($this->is_blow_of_heart)) && $isBlowOfHeart)
                $this->blowOfHeartAdded = true;

            $this->needToUpdateBook = true;
        }
        $this->is_blow_of_heart = $isBlowOfHeart;
    }

    public function getReview() {
        return $this->review;
    }

    public function setReview($review) {
        $this->review = trim($review);
    }

    public function getIsWished() {
        return $this->is_wished;
    }

    public function setIsWished($isWished) {
        $this->is_wished = $isWished;
    }

    public function getIsOwned() {
        return $this->is_owned;
    }

    public function setIsOwned($isOwned) {
        $this->is_owned = $isOwned;
    }

    public function getIs_deleted() {
        return $this->is_deleted;
    }

    public function setIs_deleted($is_deleted) {
        $this->is_deleted = $is_deleted;
    }

    public function getCreationDate() {
        if (!$this->creation_date) {
            $this->creation_date = new \DateTime();
        }
        return $this->creation_date;
    }

    public function setCreationDate($creationDate) {
        $this->creation_date = $creationDate;
    }

    public function getLastModificationDate() {
        return $this->last_modification_date;
    }

    public function setLastModificationDate($lastModificationDate) {
        $this->last_modification_date = $lastModificationDate;
    }

    public function getReadingState() {
        return $this->reading_state;
    }

    public function setReadingState($readingState) {
        $this->reading_state = $readingState;
    }

    public function getReadingDate() {
        if ($this->reading_date_s)
            $this->reading_date = \Sb\Helpers\DateHelper::createDateTime($this->reading_date_s);
        return $this->reading_date;
    }

    public function setReadingDate($readingDate) {
        $this->reading_date = $readingDate;
        // stocke une version string de la date pour utilisation lors des serialization/deserialization
        if ($this->reading_date)
            $this->readingDateS = \Sb\Helpers\DateHelper::getDateForDB($this->reading_date);
    }

    public function getReadingDateS() {
        return $this->reading_date_s;
    }

    public function setReadingDateS($readingDateS) {
        $this->reading_date_s = $readingDateS;
    }

    public function getBorrowedOnce() {
        return $this->borrowed_once;
    }

    public function setBorrowedOnce($borrowedOnce) {
        $this->borrowed_once = $borrowedOnce;
    }

    public function getLentOnce() {
        return $this->lent_once;
    }

    public function setLentOnce($lentOnce) {
        $this->lent_once = $lentOnce;
    }

    public function getHyperlink() {
        return $this->hyperlink;
    }

    public function setHyperlink($hyperlink) {
        $hyperlink = str_replace("http://", "", $hyperlink);
        $hyperlink = str_replace("https://", "", $hyperlink);
        $this->hyperlink = $hyperlink;
    }

    public function getTags() {
        return $this->tags;
    }

    public function setTags($tags) {
        $this->tags = $tags;
    }

    public function getLendings() {
        return $this->lendings;
    }

    public function setLendings($lendings) {
        $this->lendings = $lendings;
    }

    public function getBorrowings() {
        return $this->borrowings;
    }

    public function setBorrowings($borrowings) {
        $this->borrowings = $borrowings;
    }

    public function getGiftsRelated() {
        return $this->giftsRelated;
    }

    public function setGiftsRelated($giftsRelated) {
        $this->giftsRelated = $giftsRelated;
    }

    public function IsValid() {
        if (!$this->getUserId())
            return false;
        if (!$this->getBookId())
            return false;
        return true;
    }

    public function resetId() {
        $this->id = null;
    }

    public function addLending(\Sb\Db\Model\Lending $lending) {
        $this->lendings[] = $lending;
    }

    public function addBorrowing(\Sb\Db\Model\Lending $borrowing) {
        $this->borrowings[] = $borrowing;
    }

    public function getNeedToUpdateBook() {
        return $this->needToUpdateBook;
    }

    public function getRatingDiff() {
        return $this->ratingDiff;
    }

    public function getRatingAdded() {
        return $this->ratingAdded;
    }

    public function getBlowOfHeartAdded() {
        return $this->blowOfHeartAdded;
    }

    public function getBlowOfHeartRemoved() {
        return $this->blowOfHeartRemoved;
    }

    /**
     *
     * @return \Sb\Db\Model\Lending
     */
    public function getActiveLending() {
        if ($this->lendings) {
            foreach ($this->lendings as $lending) {
                if ($lending->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE) {
                    return $lending;
                }
            }
        }
    }

    /**
     *
     * @return \Sb\Db\Model\Lending
     */
    public function getActiveBorrowing() {
        if ($this->borrowings) {
            foreach ($this->borrowings as $borrowing) {
                if ($borrowing->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE) {
                    return $borrowing;
                }
            }
        }
    }

    /**
     *
     * @return \Sb\Db\Model\UserBookGift
     */
    public function getActiveGiftRelated() {
        if ($this->giftsRelated) {
            foreach ($this->giftsRelated as $gitRelated) {
                if ($gitRelated->getIs_active()) {
                    return $gitRelated;
                }
            }
        }
    }

}