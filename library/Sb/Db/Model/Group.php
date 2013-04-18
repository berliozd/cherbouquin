<?php
namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_groups") */
class Group implements Model {

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

    /** @Column(type="string", length=150) */
    protected $name;

    /**
     * @ManyToOne(targetEntity="GroupType", inversedBy="groups")
     * @JoinColumn(name="grouptype_id", referencedColumnName="id")
     */
    protected $type;

    /** @Column(type="boolean") */
    protected $is_validated;

    /**
     * @OneToMany(targetEntity="GroupUser", mappedBy="group", fetch="EXTRA_LAZY")
     * @JoinColumn(name="id", referencedColumnName="group_id")
     */
    protected $groupusers;

    /**
     * @OneToMany(targetEntity="GroupChronicle", mappedBy="group", fetch="EXTRA_LAZY")
     * @JoinColumn(name="id", referencedColumnName="group_id")
     */
    protected $chronicles;

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
     * @return String $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return GroupType $type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param GroupType $type
     */
    public function setType($type) {
        $this->type = $type;
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
     * @return GroupUser[] $groupusers
     */
    public function getGroupusers() {
        return $this->groupusers;
    }

    /**
     * @param Collection of GoupUser $groupusers
     */
    public function setGroupusers($groupusers) {
        $this->groupusers = $groupusers;
    }

    /**
     * @return Collection of GroupChronicle $chronicles
     */
    public function getChronicles() {
        return $this->chronicles;
    }

    /**
     * @param Collection of GroupChronicle $chronicles
     */
    public function setChronicles($chronicles) {
        $this->chronicles = $chronicles;
    }

    /**
     * @see \Sb\Db\Model\Model::IsValid()
     */
    public function IsValid() {
        return true;
    }

}
