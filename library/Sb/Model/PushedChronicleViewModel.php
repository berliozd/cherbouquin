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

    private $groupChronicleId;
    private $title;
    private $link;
    private $description;
    private $image;

    /**
     * @return int $groupChronicleId
     */
    public function getGroupChronicleId() {
        return $this->groupChronicleId;
    }

    /**
     * @param int $groupChronicleId
     */
    public function setGroupChronicleId($groupChronicleId) {
        $this->groupChronicleId = $groupChronicleId;
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

}
