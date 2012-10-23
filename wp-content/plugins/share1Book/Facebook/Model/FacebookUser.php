<?php

namespace Sb\Facebook\Model;

/**
 * Description of \Sb\Facebook\Model\FacebookUser
 *
 * @author Didier
 */
class FacebookUser {

    private $id;
    private $uid;
    private $email;
    private $first_name;
    private $last_name;
    private $name;
    private $sex;
    private $hometown_location;
    private $birthday;
    private $locale;
    private $pic_small;
    private $pic;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUid() {
        return $this->uid;
    }

    public function setUid($uid) {
        $this->uid = $uid;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getFirst_name() {
        return $this->first_name;
    }

    public function setFirst_name($first_name) {
        $this->first_name = $first_name;
    }

    public function getLast_name() {
        return $this->last_name;
    }

    public function setLast_name($last_name) {
        $this->last_name = $last_name;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSex() {
        return $this->sex;
    }

    public function setSex($sex) {
        $this->sex = $sex;
    }

    public function getHometown_location() {
        return $this->hometown_location;
    }

    public function setHometown_location($hometown_location) {
        $this->hometown_location = $hometown_location;
    }

    public function getBirthday() {
        return $this->birthday;
    }

    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    public function getPic_small() {
        return $this->pic_small;
    }

    public function setPic_small($pic_small) {
        $this->pic_small = $pic_small;
    }

    public function getPic() {
        return $this->pic;
    }

    public function setPic($pic) {
        $this->pic = $pic;
    }

    public function IsValid(){
        return true;
    }

}

?>
