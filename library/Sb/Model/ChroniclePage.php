<?php

namespace Sb\Model;

use Sb\Db\Model\Chronicle;
use Sb\Model\ChronicleViewModel;

/**
 *
 * @author Didier
 */
class ChroniclePage {
    
    /* @var $chronicle Chronicle */
    private $chronicle;
    
    /* @var $chronicleViewModel ChronicleViewModel */
    private $chronicleViewModel;
    
    /* @var $similarChronicles array of ChronicleViewModelLight */
    private $similarChronicles;
    
    /* @var $sameAuthorChronicles array of ChronicleViewModelLight */
    private $sameAuthorChronicles;
    
    /* @var $pressReviews array of PressReview */
    private $pressReviews;
    
    /* @var $pressReviews array of UserBook */
    private $userBooksReviews;
    
    /* @var $videoPressReview PressReview */
    private $videoPressReview;

    /**
     *
     * @return Chronicle $chronicle
     */
    public function getChronicle() {

        return $this->chronicle;
    }

    /**
     *
     * @param Chronicle $chronicle
     */
    public function setChronicle($chronicle) {

        $this->chronicle = $chronicle;
    }

    /**
     *
     * @return ChronicleViewModel $chronicleViewModel
     */
    public function getChronicleViewModel() {

        return $this->chronicleViewModel;
    }

    /**
     *
     * @param ChronicleViewModel $chronicleViewModel
     */
    public function setChronicleViewModel($chronicleViewModel) {

        $this->chronicleViewModel = $chronicleViewModel;
    }

    /**
     *
     * @return array of ChronicleViewModelLight $similarChronicles
     */
    public function getSimilarChronicles() {

        return $this->similarChronicles;
    }

    /**
     *
     * @param array of ChronicleViewModelLight $similarChronicles
     */
    public function setSimilarChronicles($similarChronicles) {

        $this->similarChronicles = $similarChronicles;
    }

    /**
     *
     * @return array of ChronicleViewModelLight $sameAuthorChronicles
     */
    public function getSameAuthorChronicles() {

        return $this->sameAuthorChronicles;
    }

    /**
     *
     * @param array of ChronicleViewModelLight $sameAuthorChronicles
     */
    public function setSameAuthorChronicles($sameAuthorChronicles) {

        $this->sameAuthorChronicles = $sameAuthorChronicles;
    }

    /**
     *
     * @return array of PressReview $pressReviews
     */
    public function getPressReviews() {

        return $this->pressReviews;
    }

    /**
     *
     * @param array of PressReview $pressReviews
     */
    public function setPressReviews($pressReviews) {

        $this->pressReviews = $pressReviews;
    }

    /**
     *
     * @return array of UserBook $userBooksReviews
     */
    public function getUserBooksReviews() {

        return $this->userBooksReviews;
    }

    /**
     *
     * @param array of UserBook $userBooksReviews
     */
    public function setUserBooksReviews($userBooksReviews) {

        $this->userBooksReviews = $userBooksReviews;
    }
	/**
     * @return PressReview $videoPressReview
     */
    public function getVideoPressReview() {

        return $this->videoPressReview;
    }


	/**
     * @param PressReview $videoPressReview
     */
    public function setVideoPressReview($videoPressReview) {

        $this->videoPressReview = $videoPressReview;
    }


    
    

}
