<?php

use Sb\Helpers\StringHelper;
use Sb\Db\Dao\UserDao;
use Sb\Flash\Flash;
use Sb\Service\MailSvc;
use Sb\Entity\Constants;

class Default_UserbookGiftsController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function sendByEmailAction() {

        $uid = $this->_getParam('uid');
        $emails = $this->_getParam('emails');

        $origin = $this->getRequest()->getHeader('referer');
        $origin .= "&emails=" . $emails;

        // Checking if parameters are passed
        if ($uid && $emails) {
            // Checking if uid is a valid user
            $user = UserDao::getInstance()->get($uid);
            if ($user) {

                // Getting user wished books
                $wishedUserbooks = $user->getNotDeletedUserBooks();
                $wishedUserbooks = array_filter($wishedUserbooks, array(&$this, "isWished"));

                // Cheking if some valid emails are passed    
                $emailsArray = array($emails);
                if (strpos(",", $emails) !== 0)
                    $emailsArray = explode(",", $emails);

                foreach ($emailsArray as $email) {
                    if (!StringHelper::isValidEmail($email)) {
                        Flash::addItem(__("Un des emails renseigné n'est pas valide.", "s1b"));
                        $this->_redirect($origin);
                        exit();
                    }
                }

                // Building the mail content
                $emailContent = \Sb\Helpers\MailHelper::wishedUserBooksEmailBody($user, $wishedUserbooks);
                
                // Sending mail
                MailSvc::getInstance()->send($emails, sprintf(__("%s - Liste des livres souhaités par %s", "s1b"), Constants::SITENAME, $user->getFriendlyName()), $emailContent);

                Flash::addItem(__("La liste a bien été envoyée par email.", "s1b"));
                $this->_redirect($origin);
                exit();
            }
        }
        Flash::addItem(__("Une erreur s'est produite lors de l'envoi de la liste par email", "s1b"));
        $this->_redirect($origin);
        exit();
    }

    function isWished(\Sb\Db\Model\UserBook $userBook) {
        if ($userBook->getIsWished()) {
            return true;
        }
    }

}

