<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_countries") */
class Country implements \Sb\Db\Model\Model {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=2) */
    protected $iso3166;

    /** @Column(type="string", length=255) */
    protected $label_english;

    /** @Column(type="string", length=255) */
    protected $label_french;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getIso3166() {
        return $this->iso3166;
    }

    public function setIso3166($iso3166) {
        $this->iso3166 = $iso3166;
    }

    public function getLabel_english() {
        return $this->label_english;
    }

    public function setLabel_english($label_english) {
        $this->label_english = $label_english;
    }

    public function getLabel_french() {
        return $this->label_french;
    }

    public function setLabel_french($label_french) {
        $this->label_french = $label_french;
    }

    public function IsValid() {
        return true;
    }

}