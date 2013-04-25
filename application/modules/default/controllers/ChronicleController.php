<?php

use Sb\Db\Dao\ChronicleDao;
use Sb\Db\Model\Chronicle;
use Sb\View\ChronicleDetail;
use Sb\Db\Service\ChronicleSvc;
use Sb\View\OtherChroniclesSameType;
use Sb\Trace\Trace;
/**
 * ChronicleController
 * 
 * @author
 * @version 
 */

class Default_ChronicleController extends Zend_Controller_Action {
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
            $chronicleView = new ChronicleDetail($chronicle);
            // Add main chronicle to model
            $this->view->chronicle = $chronicleView->get();

            // Get other chonicles same type
            $chroniclesSameType = ChronicleSvc::getInstance()->getChroniclesOfType($chronicle->getType_id());
            $chroniclesSameType = $this->getDifferentChronicles($chronicle->getId(), $chroniclesSameType, 3);
            $otherChoniclesSameTypeView = new OtherChroniclesSameType($chroniclesSameType);
            // Add same type chronicles to model
            $this->view->otherChoniclesSameType = $otherChoniclesSameTypeView->get();

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }

    }

    /**
     * Get only different chronicles than the main one in the page  
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

}
