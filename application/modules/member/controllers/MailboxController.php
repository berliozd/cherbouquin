<?php

use Sb\Flash\Flash;
use Sb\Db\Dao\MessageDao;
use Sb\Entity\Urls;
use Sb\Authentification\Service\AuthentificationSvc;

class Member_MailboxController extends Zend_Controller_Action {

    public function init() {

        // Checks is user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
    }

    public function indexAction() {
        // action body
    }

    public function readMessageAction() {

        global $globalContext;

        $redirect = false;
        
        $messageId = $this->_getParam('mid');
        if ($messageId) {
            $message = MessageDao::getInstance()->get($messageId);
            if ($message) {
                if ($message->getRecipient()->getId() != $globalContext->getConnectedUser()->getId()) {
                    $redirect = true;
                    Flash::addItem(__("Le message que vous tentez de lire ne vous est pas destiné.", "s1b"));
                } else {
                    $message->setIs_read(true);
                    MessageDao::getInstance()->update($message);
                }
            } else {
                $redirect = true;
                Flash::addItem(__("Le message que vous tentez de lire n'existe pas.", "s1b"));
            }
        } else {
            $redirect = true;
            Flash::addItem(__("Le message que vous tentez de lire n'existe pas.", "s1b"));
        }

        if ($redirect)
            $this->_redirect(Urls::USER_MAILBOX);
        else
            $this->view->message = $message;
    }

}

