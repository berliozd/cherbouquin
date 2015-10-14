<?php
use Sb\Db\Dao\UserbookCommentDao;
use Sb\Db\Model\UserbookComment;
use Sb\Db\Dao\UserBookDao;

class Member_CommentsController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add-userbook-comment', 'html')->initContext();
    
    }

    public function indexAction() {
        // action body
    }

    public function addUserbookCommentAction() {

        $this->view->setEncoding('utf-8');

        $globalContext = new \Sb\Context\Model\Context();

        $this->view->errorMessage = __("Une erreur s'est produite et votre commentaire n'a pas été posté correctement.", "s1b");
        
        if ($globalContext->getConnectedUser()) {
            // Getting params
            $bookId = $this->_getParam('bookId');
            $reviewPageId = $this->_getParam('reviewPageId');
            $userBookId = $this->_getParam('ubid');
            $commentValue = $this->_getParam('comment');
            
            // Add userbook comment
            $userbook = UserBookDao::getInstance()->get($userBookId);
            $comment = new UserbookComment();
            $comment->setValue($commentValue);
            $comment->setCreation_date(new \DateTime());
            $comment->setOwner($globalContext->getConnectedUser());
            $comment->setUserbook($userbook);
            
            // If the adding happens correctly, we forward to the get-reviews-page action
            if (UserbookCommentDao::getInstance()->add($comment)) {
                
                $reviewUser = $userbook->getUser();
                // Sends a mail only if connected user is not the userbook owner
                if (($reviewUser->getId() != $globalContext->getConnectedUser()->getId()) && ($reviewUser->getSetting()->getEmailMe() == \Sb\Helpers\UserSettingHelper::EMAIL_ME_YES)) {
                    // Send a email to the userbook owner
                    $subject = sprintf(__("%s - Un nouveau commentaire sur un de vos livres.", "s1b"), \Sb\Entity\Constants::SITENAME);
                    $body = \Sb\Helpers\MailHelper::newCommentPosted($commentValue, $userbook->getBook());
                    \Sb\Service\MailSvc::getInstance()->send($reviewUser->getEmail(), $subject, $body);
                }
                
                // Forward to review page action
                $this->forward("get-reviews-page", "book", "default", array (
                        "key" => $bookId,
                        "param" => $reviewPageId,
                        "format" => "html" 
                ));
            }
        } else
            $this->view->errorMessage = __("Vous devez être connecté pour poster un commentaire.", "s1b");
        
        // Otherwise, we let the message 'KO' get rendered by the view
        // This message will be intercepted in javascript code to display a coherent flash message
    }

}