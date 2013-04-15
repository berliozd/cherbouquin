<?php
namespace Sb\Db\Model;

/** 
 * @author Didier
 * 
 */
/** @Entity @Table(name="s1b_medias") */
class Media implements Model {

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

    /** @Column(type="string", length=40) */
    protected $twitter_name;

    /** @Column(type="string", length=20) */
    protected $twitter_user;

    /** @Column(type="string", length=255) */
    protected $twitter_picture;

    /** @Column(type="string", length=255) */
    protected $website;

    /**
     * @OneToMany(targetEntity="PressReview", mappedBy="media")
     * @JoinColumn(name="id", referencedColumnName="media_id")
     * */
    protected $pressreviews;
    
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
     * @return String $twitter_name
     */
    public function getTwitter_name() {
        return $this->twitter_name;
    }

    /**
     * @param String $twitter_name
     */
    public function setTwitter_name($twitter_name) {
        $this->twitter_name = $twitter_name;
    }

    /**
     * @return String $twitter_user
     */
    public function getTwitter_user() {
        return $this->twitter_user;
    }

    /**
     * @param String $twitter_user
     */
    public function setTwitter_user($twitter_user) {
        $this->twitter_user = $twitter_user;
    }

    /**
     * @return String $twitter_picture
     */
    public function getTwitter_picture() {
        return $this->twitter_picture;
    }

    /**
     * @param String $twitter_picture
     */
    public function setTwitter_picture($twitter_picture) {
        $this->twitter_picture = $twitter_picture;
    }

    /**
     * @return String $website
     */
    public function getWebsite() {
        return $this->website;
    }

    /**
     * @param String $website
     */
    public function setWebsite($website) {
        $this->website = $website;
    }
    
	/**
	 * @return Collection of PressReview $pressreviews
	 */
	public function getPressreviews() {
		return $this->pressreviews;
	}

	/**
	 * @param Collection of PressReview $pressreviews
	 */
	public function setPressreviews($pressreviews) {
		$this->pressreviews = $pressreviews;
	}
	
	/**
	 * @see \Sb\Db\Model\Model::IsValid()
	 */
	public function IsValid() {
		return true;
		
	}
}
