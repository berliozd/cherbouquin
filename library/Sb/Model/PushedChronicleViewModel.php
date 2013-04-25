<?php
namespace Sb\Model;

/** 
 * @author Didier
 * 
 */
class PushedChronicleViewModel {

    /**
     * 
     */
    function __construct() {

    }

    private $chronicleId;
    private $title;
    private $link;
    private $description;
    private $image;
    private $detailLink;

    /**
     * @return int $chronicleId
     */
    public function getChronicleId() {
        return $this->chronicleId;
    }

    /**
     * @param int $chronicleId
     */
    public function setChronicleId($chronicleId) {
        $this->chronicleId = $chronicleId;
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
     * @return String $description
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param String $description
     */
    public function setDescription($description) {
        $this->description = $description;
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
    /**
     * @return String $detailLink
     */
    public function getDetailLink() {
        return $this->detailLink;
    }

    /**
     * @param String $detailLink
     */
    public function setDetailLink($detailLink) {
        $this->detailLink = $detailLink;
    }

}
