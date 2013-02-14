<?php

use Sb\Flash\Flash;
use Sb\Db\Dao\UserBookDao;
use Sb\Db\Model\UserBookGift;
use Sb\Db\Dao\UserBookGiftDao;
use Sb\Helpers\HTTPHelper;

class Default_WishedUserbookController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        echo "boo";
    }

    public function setAsOfferedAction() {
        $dest = (HTTPHelper::getReferer() ? HTTPHelper::getReferer() : HTTPHelper::Link());
        $id = $this->_getParam('ubid', -1);
        // Checking if passed id is > 0
        if ($id > 0) {            
            $userBook = UserBookDao::getInstance()->get($id);
            // Checking if id passed matches a user book
            if ($userBook) {
                // Checking if user book not set as offered already
                if (!$userBook->getActiveGiftRelated()) {
                    $userBookGift = new UserBookGift;
                    $userBookGift->setUserbook($userBook);
                    global $globalContext;
                    $connectedUser = $globalContext->getConnectedUser();
                    $userBookGift->setOfferer($connectedUser);
                    $userBookGift->setIs_active(true);
                    if (UserBookGiftDao::getInstance()->add($userBookGift)) {
                        Flash::addItem(__("Le livre a correctement été marqué 'déjà acheté'.", "s1b"));
                        $this->_redirect($dest);
                        exit();
                    }
                }
            }
        }
        Flash::addItem(__("une erreur s'est produite et le livre n'a pas pu être marqué 'déjà acheté'.", "s1b"));
        $this->_redirect($dest);
        exit();
    }

}

