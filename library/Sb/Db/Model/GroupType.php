<?php
namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_grouptypes") */
class GroupType implements Model {

    /**
     *
     */
    function __construct() {

    }

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=50) */
    protected $label;

    /**
     * @OneToMany(targetEntity="Group", mappedBy="type", fetch="EXTRA_LAZY")
     * @JoinColumn(name="id", referencedColumnName="grouptype_id")
     */
    protected $groups;

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
     * @return String $label
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param String $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * @return Collection of Group $groups
     */
    public function getGroups() {
        return $this->groups;
    }

    /**
     * @param Collection of Group type $groups
     */
    public function setGroups($groups) {
        $this->groups = $groups;
    }

    /**
     * @see \Sb\Db\Model\Model::IsValid()
     */
    public function IsValid() {
        return true;
    }

}
