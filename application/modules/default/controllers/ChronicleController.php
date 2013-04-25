<?php

use Sb\Db\Dao\ChronicleDao;
use Sb\View\PushedChronicle;
use Sb\View\ChronicleDetail;
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

            $chronicle = ChronicleDao::getInstance()->get($chronicleId);

            $chronicleView = new ChronicleDetail($chronicle);
            $this->view->chronicle = $chronicleView->get();

        } catch (\Exception $e) {

        }

    }

}
