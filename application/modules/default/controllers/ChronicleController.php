<?php

use Sb\Db\Dao\ChronicleDao;
use Sb\Db\Model\Chronicle;
use Sb\View\ChronicleDetail;
use Sb\Db\Service\ChronicleSvc;
use Sb\View\OtherChroniclesSameType;
use Sb\Trace\Trace;
use Sb\View\OtherChroniclesSameAuthor;
use Sb\View\Components\Ad;
use Sb\Helpers\HTTPHelper;

/**
 * ChronicleController
 * 
 * @author
 * @version 
 */

class Default_ChronicleController extends Zend_Controller_Action {

    public function init() {

        // Add chronicle css to head
        $this->view->headLink()->appendStylesheet(BASE_URL . "resources/css/chronicle.css?v=" . VERSION);

    }

    /**
     * The default action - show a chronicle detail page
     */
    public function indexAction() {

        try {

            // Get chronicle id from request
            $chronicleId = $this->getParam("cid");

            // Get main chronicle
            /* @var $chronicle Chronicle */
            $chronicle = ChronicleDao::getInstance()->get($chronicleId);

            // Increment chronicle nb views
            $this->incrementChronicleNbViews($chronicle);

            // Add main chronicle to model view
            $chronicleView = new ChronicleDetail($chronicle);
            $this->view->chronicle = $chronicleView->get();

            // Get similar chronicles (with same tag or with similar keywords) and add it to model view
            $similarChronicles = $this->getSimilarChronicles($chronicle);
            if ($similarChronicles) {
                $otherChoniclesSameTypeView = new OtherChroniclesSameType($similarChronicles);
                $this->view->otherChoniclesSameType = $otherChoniclesSameTypeView->get();
            }

            // Get same author chronicles and add it to model view
            $authorChronicles = ChronicleSvc::getInstance()->getAuthorChronicles($chronicle->getUser()->getId());
            if ($authorChronicles) {
                $authorChronicles = $this->getDifferentChronicles($chronicle->getId(), $authorChronicles, 5);
                if ($authorChronicles) {
                    $authorChroniclesView = new OtherChroniclesSameAuthor($authorChronicles);
                    // Add author chronicles to model
                    $this->view->authorChroniclesView = $authorChroniclesView->get();
                }
            }

            // Get ad
            $ad = new Ad("", "");
            $this->view->ad = $ad->get();


        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }

    }

    /**
     * Get only different chronicles than the one corresponding to the id received in $currentChronicleId parameters  
     * @param int $currentChronicleId the id of the current chronicle
     * @param Collection of Chronicle $chronicles the collection of current chronicle to parse
     * @param int $maxNumber the maximum number of chronicle to return
     * @return a Collection of Chronicle that doesn't contain the main one displayed in the page 
     */
    private function getDifferentChronicles($currentChronicleId, $chronicles, $maxNumber) {

        $result = array();

        foreach ($chronicles as $chronicle) {
            /* @$chronicle Chronicle */
            if ($chronicle->getId() != $currentChronicleId) {
                $result[] = $chronicle;
                if (count($result) >= $maxNumber) {
                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Test if chronicle nb of views needs to be incremented based on the presence of the chronicle id in a cookie called chroniclesSeen 
     * @param Chronicle $chronicle
     */
    private function incrementChronicleNbViews(Chronicle $chronicle) {

        $cookieName = "chroniclesSeen";
        $chronicleNotSeen = false;

        // Get cookie 'chroniclesSeen'
        $chroniclesSeenCookie = $this->getRequest()->getCookie($cookieName);

        // Parse cookie and tell if current chronicle has been seen already
        if ($chroniclesSeenCookie) {

            $chroniclesSeen = explode(",", $chroniclesSeenCookie);
            if (!in_array($chronicle->getId(), $chroniclesSeen)) {

                $chroniclesSeen[] = $chronicle->getId();
                $cookieValue = implode(",", $chroniclesSeen);

                // Set cookie
                $this->setChronicleSeenCookie($cookieName, $cookieValue);

                // Increment chronicle nb views
                $this->incrementChronicleInDB($chronicle);

            }
        } else {

            // Set cookie
            $this->setChronicleSeenCookie($cookieName, $chronicle->getId());

            // Increment chronicle nb views
            $this->incrementChronicleInDB($chronicle);

        }
    }

    /**
     * Set a cookie for name and value with a 24 hours life time
     * @param String $cookieName
     * @param String $cookieValue
     */
    private function setChronicleSeenCookie($cookieName, $cookieValue) {
        $this->getResponse()->setRawHeader(new Zend_Http_Header_SetCookie($cookieName, $cookieValue, time() + 3600 * 24, '/',
            HTTPHelper::getHostBase(), false, true));
    }

    /**
     * Increment nb of views in database for chronicle
     * @param Chronicle $chronicle
     */
    private function incrementChronicleInDB(Chronicle $chronicle) {
        $chronicle->setNb_views($chronicle->getNb_views() + 1);
        ChronicleDao::getInstance()->update($chronicle);
    }

    private function isReviewd(UserBook $userBook) {
        if ($userBook->getReview()) {
            return true;
        }
    }

    /**
     * Get 3 similar chronicles for current chronicle : with same tag or same keywords
     * @param Chronicle $chronicle the current chronicle
     * @return Collection of chronicle
     */
    private function getSimilarChronicles(Chronicle $chronicle) {

        $nbOfSimilarChronicles = 3;

        // Get the chronicles with same tag
        $similarChronicles = array();
        if ($chronicle->getTag()) {
            $chroniclesWithTag = ChronicleSvc::getInstance()->getChroniclesWithTag($chronicle->getTag()->getId(), $nbOfSimilarChronicles);
            $chroniclesWithTag = $this->getDifferentChronicles($chronicle->getId(), $chroniclesWithTag, $nbOfSimilarChronicles);
            $similarChronicles = $chroniclesWithTag;
        }

        // If there's not enough chronicles (or 0) with same tag and if current chronicle has some keywords :
        // we search for schronicle with same keywords
        if ((!$similarChronicles || count($similarChronicles) < $nbOfSimilarChronicles) && $chronicle->getKeywords()) {

            $chroniclesWithKeywords = ChronicleSvc::getInstance()->getChroniclesWithKeywords(explode(",", $chronicle->getKeywords()), $nbOfSimilarChronicles);

            // If no chronicles with same tag, we just add the one we just get with same keywords
            if (!$similarChronicles) {

                $similarChronicles = $chroniclesWithKeywords;
                $similarChronicles = $this->getDifferentChronicles($chronicle->getId(), $similarChronicles, $nbOfSimilarChronicles);

            } else {

                $filteredChroniclesWithKeywords = array();
                // Loop all chronicles found with keywords and remove the one already found with same tag
                foreach ($chroniclesWithKeywords as $chronicleWithKeyword) {

                    $add = true;
                    foreach ($similarChronicles as $similarChronicle) {
                        if ($similarChronicle->getId() == $chronicleWithKeyword->getId()) {
                            $add = false;
                            break;
                        }
                    }
                    if ($add)
                        $filteredChroniclesWithKeywords[] = $chronicleWithKeyword;
                }
                $filteredChroniclesWithKeywords = $this->getDifferentChronicles($chronicle->getId(), $filteredChroniclesWithKeywords, $nbOfSimilarChronicles);
                
                // Merge the chronicles found with tag and the one found with keywords
                $similarChronicles = array_merge($similarChronicles, $filteredChroniclesWithKeywords);
                $similarChronicles = $this->getDifferentChronicles($chronicle->getId(), $similarChronicles, $nbOfSimilarChronicles);
            }
        }
        return $similarChronicles;
    }
}
