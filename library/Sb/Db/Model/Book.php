<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_books") */
class Book implements \Sb\Db\Model\Model {

    public function __construct() {
        $this->contributors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userbooks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=10) */
    protected $isbn10;

    /** @Column(type="string", length=13) */
    protected $isbn13;

    /** @Column(type="string", length=10) */
    protected $asin;

    /** @Column(type="string", length=150) */
    protected $title;

    /** @Column(type="string", length=4000) */
    protected $description;

    /** @Column(type="string", length=250) */
    protected $image_url;

    /** @Column(type="string", length=250) */
    protected $small_image_url;

    /** @Column(type="string", length=250) */
    protected $large_image_url;

    /** @Column(type="blob", length=250) */
    protected $image_binary;

    /** @Column(type="datetime") */
    protected $publishing_date;
    protected $publishing_date_s; /* membre supplémentaire necessaire pour les opération de serialization/deserialization notamment pour le stockage en cache */

    /** @Column(type="datetime") */
    protected $creation_date;

    /** @Column(type="datetime") */
    protected $last_modification_date;
    protected $last_modification_date_s; /* membre supplémentaire necessaire pour les opération de serialization/deserialization notamment pour le stockage en cache */

    /** @Column(type="string", length=300) */
    protected $amazon_url;

    /**
     * @ManyToMany(targetEntity="Contributor")
     * @JoinTable(name="s1b_bookcontributors",
     *      joinColumns={@JoinColumn(name="book_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="contributor_id", referencedColumnName="id")}
     *      )
     * */
    protected $contributors; // WARNING : Fetch mode is declared EAGER because we want to automatically get the contributors (and have it stored in cache with a Book)

    /**
     * @ManyToOne(targetEntity="Publisher", inversedBy="books")
     * @JoinColumn(name="publisher_id", referencedColumnName="id")
     */
    protected $publisher;

    /** @OneToMany(targetEntity="UserBook", mappedBy="book", fetch="EXTRA_LAZY") */
    protected $userbooks = null;

    /** @Column(type="integer") */
    protected $rating_sum;

    /** @Column(type="integer") */
    protected $nb_rated_userbooks;

    /** @Column(type="integer") */
    protected $nb_blow_of_hearts;

    /** @Column(type="float") */
    protected $average_rating;

    /** @Column(type="integer") */
    protected $nb_of_pages;

    /** @Column(type="string", length=20) */
    protected $language;

    /** @OneToMany(targetEntity="GroupChronicle", mappedBy="book", fetch="EXTRA_LAZY")  */
    protected $groupchronicles;

//~ Getters & setters

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
//        if ($this->id !== null && $this->id != $id) {
//            throw new \Exception('Cannot change identifier to ' . $id . ', already set to ' . $this->id);
//        }
        if (is_numeric($id)) {
            $this->id = (int) $id;
        }
    }

    public function getISBN10() {
        return $this->isbn10;
    }

    public function setISBN10($isbn10) {
        $this->isbn10 = trim($isbn10);
    }

    public function getISBN13() {
        return $this->isbn13;
    }

    public function setISBN13($isbn13) {
        $this->isbn13 = trim($isbn13);
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = trim($title);
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = trim($description);
    }

    public function getImageUrl() {
        return $this->image_url;
    }

    public function setImageUrl($imageUrl) {
        $this->image_url = trim($imageUrl);
    }

    public function getSmallImageUrl() {
        return $this->small_image_url;
    }

    public function setSmallImageUrl($smallImageUrl) {
        $this->small_image_url = trim($smallImageUrl);
    }

    public function getLargeImageUrl() {
        return $this->large_image_url;
    }

    public function setLargeImageUrl($largeImageUrl) {
        $this->large_image_url = $largeImageUrl;
    }

    public function getImageBinary() {
        return $this->image_binary;
    }

    public function setImageBinary($imageBinary) {
        $this->image_binary = $imageBinary;
    }

    public function getContributors() {
        return $this->contributors;
    }

    public function setContributors($contributors) {
        $this->contributors = $contributors;
    }

    public function getPublisher() {
        return $this->publisher;
    }

    public function setPublisher(\Sb\Db\Model\Publisher $publisher) {
        $this->publisher = $publisher;
    }

    public function getPublisherId() {
        return $this->publisherId;
    }

    public function setPublisherId($publisherId) {
        $this->publisherId = $publisherId;
    }

    public function getPublishingDate() {
        if ($this->publishing_date_s)
            $this->publishing_date = \Sb\Helpers\DateHelper::createDateTime($this->publishing_date_s);
        return $this->publishing_date;
    }

    public function setPublishingDate($publishingDate) {
// var_dump($publishingDate);
        $this->publishing_date = $publishingDate;
// stocke une version string de la date pour utilisation lors des serialization/deserialization
        if ($this->publishing_date)
            $this->publishing_date_s = \Sb\Helpers\DateHelper::getDateForDB($this->publishing_date);
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
        if ($this->last_modification_date_s)
            $this->last_modification_date = \Sb\Helpers\DateHelper::createDateTime($this->last_modification_date_s);
        return $this->last_modification_date;
    }

    public function setLastModificationDate($lastModificationDate) {
        $this->last_modification_date = $lastModificationDate;
        if ($this->last_modification_date)
            $this->last_modification_date_s = \Sb\Helpers\DateHelper::getDateForDB($this->last_modification_date);
    }

    public function getPublishingDateS() {
        return $this->publishing_date_s;
    }

    public function setPublishingDateS($publishingDateS) {
        $this->publishing_date_s = $publishingDateS;
    }

    public function getGroupchronicles() {
        return $this->groupchronicles;
    }

    public function setGroupchronicles($groupchronicles) {
        $this->groupchronicles = $groupchronicles;
    }

    public function getTagImg($defImg) {
        $tagImg = "";
        if ($this->getLargeImageUrl()) {
            $tagImg = sprintf("<img width='%s' src='%s' border='0' class='bookPreview'/>", "95%", $this->getLargeImageUrl());
        } elseif ($this->getImageUrl()) {
            $tagImg = sprintf("<img src='%s' border='0' class='bookPreview'/>", $this->getImageUrl());
        } else {
            $tagImg = sprintf("<img src='%s' border='0' class='bookPreview'/>", $defImg);
        }
        return $tagImg;
    }

    public function getTagSmallImg($defImg) {
        $tagImg = "";
        if ($this->getSmallImageUrl()) {
            $tagImg = sprintf("<img src='%s' border='0' class='bookPreview'/>", $this->getSmallImageUrl());
        } else {
            $tagImg = sprintf("<img src='%s' border='0' class='bookPreview'/>", $defImg);
        }
        return $tagImg;
    }

    public function getPublicationInfo() {

        if ($this->getPublisher())
            $pub = $this->getPublisher()->getName();

        $pubDtStr = "";
        if ($this->getPublishingDate()) {
            $pubDtStr = $this->getPublishingDate()->format(__("d/m/Y", "s1b"));
        }
        $publicationInfo = "";
        if ($this->getPublishingDate() && $pub) { // publisher et date de publication renseignées
            //$publicationInfo = "Publié le $pubDtStr par $pub";
            $publicationInfo = sprintf(__("Publié le %s <span class=\"publisher\">par %s</span>", "s1b"), $pubDtStr, $pub);
        } elseif ($this->getPublishingDate()) { // date de publication renseignée
            $publicationInfo = sprintf(__("Publié le %s", "s1b"), $pubDtStr);
        } elseif ($pub) { // publisher renseigné
            $publicationInfo = sprintf(__("Publié <span class=\"publisher\">par %s</span>", "s1b"), $pub);
        }

        return $publicationInfo;
    }

    public function getAmazonUrl() {
        return $this->amazon_url;
    }

    public function setAmazonUrl($amazonUrl) {
        $this->amazon_url = $amazonUrl;
    }

    public function getLastModificationDateS() {
        return $this->last_modification_date_s;
    }

    public function setLastModificationDateS($lastModificationDateS) {
        $this->last_modification_date_s = $lastModificationDateS;
    }

    public function getASIN() {
        return $this->asin;
    }

    public function setASIN($ASIN) {
        $this->asin = $ASIN;
    }

    public function getUserBooks() {
        return $this->userbooks;
    }

    public function getNotDeletedUserBooks() {
        $tmp = array_filter($this->userbooks->toArray(), array(&$this, "isNotDeleted"));
        return array_values($tmp);
    }

    public function setUserBooks($userBooks) {
        $this->userbooks = $userBooks;
    }

    public function addUserBook(\Sb\Db\Model\UserBook $userBook) {
        $this->userbooks[] = $userBook;
    }

    public function updateAggregateFields($ratingDiff, $ratingAdded, $ratingRemoved, $blowOfHeartsAdded, $blowOfHeartsRemoved) {

        //\Sb\Trace\Trace::addItem("ratingDiff : " . $ratingDiff . " - ratingAdded : " . $ratingAdded . " - blowOfHeartsAdded : " . $blowOfHeartsAdded . " - blowOfHeartsRemoved : " . $blowOfHeartsRemoved);

        $this->rating_sum += $ratingDiff;

        if ($ratingAdded)
            $this->nb_rated_userbooks++;

        if ($ratingRemoved)
            if ($this->nb_rated_userbooks > 0)
                $this->nb_rated_userbooks--;

        if ($this->nb_rated_userbooks != 0)
            $this->average_rating = $this->rating_sum / $this->nb_rated_userbooks;
        else
            $this->average_rating = null;

        if ($blowOfHeartsAdded)
            $this->nb_blow_of_hearts++;

        if ($blowOfHeartsRemoved)
            if ($this->nb_blow_of_hearts > 0)
                $this->nb_blow_of_hearts--;
    }

    public function getRatingSum() {
        return $this->rating_sum;
    }

    public function setRatingSum($ratingSum) {
        $this->rating_sum = $ratingSum;
    }

    public function getNbRatedUserBooks() {
        return $this->nb_rated_userbooks;
    }

    public function setNbRatedUserBooks($nbRatedUserBooks) {
        $this->nb_rated_userbooks = $nbRatedUserBooks;
    }

    public function getAverageRating() {
        return $this->average_rating;
    }

    public function setAverageRating($averageRating) {
        $this->average_rating = $averageRating;
    }

    public function getNbOfBlowOfHearts() {
        return $this->nb_blow_of_hearts;
    }

    public function setNbBlowOfHearts($nbBlowOfHearts) {
        $this->nb_blow_of_hearts = $nbBlowOfHearts;
    }

    public function getNb_of_pages() {
        return $this->nb_of_pages;
    }

    public function setNb_of_pages($nb_of_pages) {
        $this->nb_of_pages = $nb_of_pages;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function IsValid() {

        // If none of the 3 ids are set, book is invalid
        if ((!$this->getISBN10()) && (!$this->getISBN13()) && (!$this->getASIN())) {
            return false;
        }

        if (!$this->getTitle()) {
            return false;
        }

        return true;
    }

    public function IsComplete() {
        if (!$this->getDescription())
            return false;
        if (!$this->getImageUrl())
            return false;
        if (!$this->getSmallImageUrl())
            return false;
        if (!$this->getPublishingDate())
            return false;
        return true;
    }

    public function getOrderableContributors() {
        if ($this->getContributors() != null && count($this->getContributors()) > 0)
            $result = implode(array_map(array(&$this, "getOrderableContributor"), $this->getContributors()->toArray()), ", ");
        else
            $result = "";
        return $result;
    }

    public function getOrderableContributor(\Sb\Db\Model\Contributor $contributor) {
        return $contributor->getName();
    }

    public function getLink() {
        $encodedTitle = \Sb\Helpers\HTTPHelper::encodeTextForURL($this->getTitle());
        $encodedAuthors = \Sb\Helpers\HTTPHelper::encodeTextForURL($this->getOrderableContributors());
        return sprintf("livre/%s/%s/%s", $encodedTitle, $encodedAuthors, $this->getId());
    }

    private function isNotDeleted(\Sb\Db\Model\UserBook $userBook) {
        return !$userBook->getIs_deleted();
    }

}