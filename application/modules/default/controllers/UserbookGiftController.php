<?php

use Sb\Helpers\HTTPHelper;
use Sb\Db\Dao\UserBookGiftDao;
use Sb\Flash\Flash;

class Default_UserbookGiftController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function disableAction()
    {
        $dest = (HTTPHelper::getReferer() ? HTTPHelper::getReferer() : HTTPHelper::Link());
        $id = $this->_getParam('ubgid', -1);
        if ($id > 0) {
            // Getting the userbook gift item
            $userbookGift = UserBookGiftDao::getInstance()->get($id);
            if ($userbookGift) {
                // Checking if the connected user is the offerer
                global $globalContext;
                $connectedUser = $globalContext->getConnectedUser();
                if ($userbookGift->getOfferer()->getId() == $connectedUser->getId()) {
                    $userbookGift->setIs_active(false);
                    $userbookGift->setLast_modification_date(new \DateTime);
                    if (UserBookGiftDao::getInstance()->update($userbookGift)) {
                        Flash::addItem(__("L'option d'achat a été annulée correctement.", "s1b"));
                        $this->_redirect($dest);
                        exit();
                    }
                }
            }
        }
        Flash::addItem(__("une erreur s'est produite lors de l'anulation de l'option d'achat.", "s1b"));
        $this->_redirect($dest);
        exit();
    }
}