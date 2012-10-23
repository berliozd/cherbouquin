<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_tags") */
class Tag implements \Sb\Db\Model\Model {


    // TODO : ramener le nombre de userbook à ce niveau => colonne nb_userbook ou bien vérifier si on peut le faire avec un count($userbooks)
    // test à faire : je veux les tags classé par ordre croissant de nombre de userbook
    // select * from tag order by nb_userbook desc ??? est-ce possible sans colonne?


    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=50) */
    protected $label;

    /** @Column(type="string", length=50) */
    protected $label_en_us;

    /** @Column(type="datetime") */
    protected $creation_date;

     /**
     * @ManyToMany(targetEntity="UserBook")
     * @JoinTable(name="s1b_userbooktags",
     *      joinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="userbook_id", referencedColumnName="id")}
     *      )
     * */
    protected $userbooks;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setLabel($label) {
        $this->label = $label;
    }

    public function getLabel_en_us() {
        return $this->label_en_us;
    }

    public function setLabel_en_us($label_en_us) {
        $this->label_en_us = $label_en_us;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function setCreationDate($creationDate) {
        $this->creation_date = $creationDate;
    }

    public function getUserBooks() {
        return $this->userbooks;
    }

    public function setUserBooks($userBooks) {
        $this->userbooks = $userBooks;
    }

    public function IsValid() {
        return true;
    }

}