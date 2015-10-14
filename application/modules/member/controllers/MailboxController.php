<?php
use Sb\Flash\Flash;
use Sb\Db\Dao\MessageDao;
use Sb\Db\Dao\UserDao;
use Sb\Db\Model\Message;
use Sb\Entity\Urls;
use Sb\Entity\Constants;
use Sb\Authentification\Service\AuthentificationSvc;
use Sb\Lists\PaginatedList;
use Sb\Helpers\HTTPHelper;
use Sb\Helpers\ArrayHelper;
use Sb\Helpers\MailHelper;
use Sb\Service\MailSvc;

class Member_MailboxController extends Zend_Controller_Action {

    public function init() {

        // Checks is user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
    }

    public function indexAction() {

        try {

            $globalContext = new \Sb\Context\Model\Context();

            // post for deleting messages
            if ($_POST) {
                $messagesToDeleteIds = ArrayHelper::getSafeFromArray($_POST, 'delete', null);
                if ($messagesToDeleteIds) {
                    $messagesToDelete = array();
                    foreach ($messagesToDeleteIds as $messagesToDeleteId) {
                        $message = MessageDao::getInstance()->get($messagesToDeleteId);
                        if ($message)
                            $messagesToDelete[] = $message;
                    }
                }
                if ($messagesToDelete && count($messagesToDelete > 0)) {
                    MessageDao::getInstance()->bulkRemove($messagesToDelete);
                    Flash::addItem(__("Le ou les messages ont été supprimés.", "s1b"));
                }
            }

            $user = $globalContext->getConnectedUser();
            $messages = MessageDao::getInstance()->getAll(array(
                    "recipient" => $user
            ), array(
                    "date" => ArrayHelper::getSafeFromArray($_GET, "sortby", "DESC")
            ));

            $this->view->dateCSSClass = ArrayHelper::getSafeFromArray($_GET, "sortby", "DESC");

            if ($messages && count($messages) > 0) {
                // preparing pagination
                $paginatedList = new PaginatedList($messages, 4);
                $this->view->firstItemIdx = $paginatedList->getFirstPage();
                $this->view->lastItemIdx = $paginatedList->getLastPage();
                $this->view->nbItemsTot = $paginatedList->getTotalPages();
                $this->view->navigation = $paginatedList->getNavigationBar();
                $this->view->messages = $paginatedList->getItems();
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function readMessageAction() {

        try {
            $globalContext = new \Sb\Context\Model\Context();

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
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function deleteAction() {

        try {

            $globalContext = new \Sb\Context\Model\Context();

            $messageId = ArrayHelper::getSafeFromArray($_GET, 'mid', null);
            if ($messageId) {
                $message = MessageDao::getInstance()->get($messageId);
                if ($message->getRecipient()->getId() == $globalContext->getConnectedUser()->getId()) {
                    MessageDao::getInstance()->remove($message);
                    Flash::addItem(__("Le message a été supprimé.", "s1b"));
                } else
                    Flash::addItem(__("Vous ne pouvez pas supprimer ce message car il ne vous est pas destiné.", "s1b"));
            } else {
                Flash::addItem(__("Le message que vous tentez de supprimer n'existe pas.", "s1b"));
            }
            HTTPHelper::redirect(Urls::USER_MAILBOX);
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function sendAction() {

        return;

        try {

            $globalContext = new \Sb\Context\Model\Context();

            $user = $globalContext->getConnectedUser();
            $friends = $user->getFriendsForEmailing();
            $nbRecipients = count($friends);

            if ($nbRecipients <= 0) {
                Flash::addItem(__("Pas de destinataire possible. Vous devez ajouter des amis pour pouvoir envoyer des messages.", "s1b"));
                HTTPHelper::redirectToReferer();
            }

            $friendSelectionsFromPost = ArrayHelper::getSafeFromArray($_POST, 'selection', null);
            $friendSelectionsFromGet = ArrayHelper::getSafeFromArray($_GET, 'selection', null);
            $sendingMessage = ArrayHelper::getSafeFromArray($_POST, 'go', null);

            $friendList = null;
            if ($friendSelectionsFromGet || $friendSelectionsFromPost || $sendingMessage) {
                // coming from friend selection page
                if ($friendSelectionsFromPost || $friendSelectionsFromGet) {
                    if ($friendSelectionsFromPost)
                        $friendSelectionsIds = ArrayHelper::getSafeFromArray($_POST, 'Friends', null);
                    elseif ($friendSelectionsFromGet) {
                        $fid = ArrayHelper::getSafeFromArray($_GET, 'Friends', null);
                        if ($fid)
                            $friendSelectionsIds = array(
                                    $fid
                            );
                    }
                    if ($friendSelectionsIds) {
                        $friendList = array();
                        $friendIdList = "";
                        foreach ($friendSelectionsIds as $friendSelection) {
                            $friend = UserDao::getInstance()->get($friendSelection);
                            $friendList[] = $friend;
                            $friendIdList .= $friend->getId() . ",";
                        }
                        $this->view->friendList = $friendList;
                        $this->view->friendIdList = $friendIdList;
                    }
                } elseif ($sendingMessage) { // Validating the mailing form
                    if (!empty($_POST['Title']) && !empty($_POST['Message']) && !empty($_POST['IdAddressee'])) {
                        $titleVal = trim($_POST['Title']);
                        $messageVal = trim($_POST['Message']);
                        $recipients = ArrayHelper::getSafeFromArray($_POST, 'IdAddressee', null);
                        $recipientsIds = explode(",", $recipients);
                        foreach ($recipientsIds as $recipientId) {
                            if (trim($recipientId) != "") {
                                $recipient = UserDao::getInstance()->get($recipientId);
                                if ($recipient) {
                                    // adding message in db
                                    $message = new Message();
                                    $message->setSender($user);
                                    $message->setRecipient($recipient);
                                    $message->setIs_read(false);
                                    $message->setTitle($titleVal);
                                    $message->setMessage($messageVal);
                                    MessageDao::getInstance()->add($message);

                                    // sending email if user authorized it
                                    $userSetting = $recipient->getSetting();
                                    if ($userSetting->getEmailMe() == 'Yes') {
                                        $body = MailHelper::newMessageArrivedBody($user->getUserName());
                                        MailSvc::getInstance()->send($recipient->getEmail(), sprintf(__("Un message vous a été envoyé depuis le site %s", "s1b"), Constants::SITENAME), $body);
                                    }
                                }
                            }
                        }
                        Flash::addItem(__("Message envoyé.", "s1b"));
                        HTTPHelper::redirect(Urls::USER_MAILBOX);
                    } else {
                        Flash::addItem(__("Au moins l'un des champs n'est pas rempli", "s1b"));
                    }
                }
            }
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function replyAction() {

        return;

        try {

            $globalContext = new \Sb\Context\Model\Context();

            $messageId = ArrayHelper::getSafeFromArray($_GET, 'mid', null);

            $redirect = false;
            if ($messageId) {
                $message = MessageDao::getInstance()->get($messageId);
                $this->view->message = $message;
                if ($message->getRecipient()->getId() != $globalContext->getConnectedUser()->getId()) {
                    Flash::addItem(__("Vous ne pouvez pas répondre à ce message car il ne vous est pas destiné.", "s1b"));
                    $redirect = true;
                }
            } else {
                Flash::addItem(__("Le message auquel vous tentez de répondre n'existe pas.", "s1b"));
                $redirect = true;
            }

            if ($_POST) {
                $title = htmlspecialchars($_POST['Title']);
                $messageContent = htmlspecialchars($_POST['Message']);
                /* test if form is not empty */
                if (!empty($title) && !empty($messageContent)) {

                    // create new message in db
                    $reply = new Message();
                    $reply->setRecipient($message->getSender());
                    $replySender = $globalContext->getConnectedUser();
                    $reply->setSender($replySender);
                    $reply->setDate(new \DateTime());
                    $reply->setTitle($title);
                    $reply->setMessage($messageContent);
                    $reply->setIs_read(false);
                    MessageDao::getInstance()->add($reply);

                    if ($message->getSender()->getSetting()->getEmailMe() == 'Yes') {
                        // send a email to warn the origianl sender of the email
                        $body = MailHelper::newMessageArrivedBody($replySender->getUserName());
                        MailSvc::getInstance()->send($message->getSender()->getEmail(), sprintf(__("Un message vous a été envoyé depuis le site %s", "s1b"), Constants::SITENAME), $body);
                    }
                    Flash::addItem(__("Message envoyé.", "s1b"));
                    $redirect = true;
                } else
                    Flash::addItem(__("Vous devez renseigné le titre et le contenu du message.", "s1b"));
            }

            if ($redirect)
                HTTPHelper::redirect(Urls::USER_MAILBOX);
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

}

