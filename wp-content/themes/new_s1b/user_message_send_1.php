<?php

$user = $context->getConnectedUser();
$friends = $user->getFriendsForEmailing();
$nbRecipients = count($friends);

if ($nbRecipients <= 0) {
    \Sb\Flash\Flash::addItem(__("Pas de destinataire possible. Vous devez ajouter des amis pour pouvoir envoyer des messages.", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}

$friendSelectionsFromPost = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'selection', null);
$friendSelectionsFromGet = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'selection', null);
$sendingMessage = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'go', null);

$friendList= null;
if ($friendSelectionsFromGet || $friendSelectionsFromPost || $sendingMessage) {
    // coming from friend selection page
    if ($friendSelectionsFromPost || $friendSelectionsFromGet) {
        if ($friendSelectionsFromPost) {
            $friendSelectionsIds = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'Friends', null);
        } elseif ($friendSelectionsFromGet) {
            $fid = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'Friends', null);
            if ($fid)
                $friendSelectionsIds = array($fid);
        }
        if ($friendSelectionsIds) {
            $friendList = array();
            foreach ($friendSelectionsIds as $friendSelection) {
                $friend = \Sb\Db\Dao\UserDao::getInstance()->get($friendSelection);
                $friendList[] = $friend;
                $friendIdList .= $friend->getId() . ",";
            }
        }
    } elseif ($sendingMessage) { // Validating the mailing form
        if (!empty($_POST['Title']) && !empty($_POST['Message']) && !empty($_POST['IdAddressee'])) {
            $titleVal = trim($_POST['Title']);
            $messageVal = trim($_POST['Message']);
            $recipients = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'IdAddressee', null);
            $recipientsIds = explode(",", $recipients);
            foreach ($recipientsIds as $recipientId) {
                if (trim($recipientId) != "") {
                    $recipient = \Sb\Db\Dao\UserDao::getInstance()->get($recipientId);
                    if ($recipient) {
                        // adding message in db
                        $message = new \Sb\Db\Model\Message;
                        $message->setSender($user);
                        $message->setRecipient($recipient);
                        $message->setIs_read(false);
                        $message->setTitle($titleVal);
                        $message->setMessage($messageVal);
                        \Sb\Db\Dao\MessageDao::getInstance()->add($message);

                        // sending email if user authorized it
                        $userSetting = $recipient->getSetting();
                        if ($userSetting->getEmailMe() == 'Yes') {
                            $body = \Sb\Helpers\MailHelper::newMessageArrivedBody($user->getUserName());
                            \Sb\Service\MailSvc::getInstance()->send($recipient->getEmail(), sprintf(__("Un message vous a été envoyé depuis le site %s", "s1b"), \Sb\Entity\Constants::SITENAME), $body);
                        }
                    }
                }
            }
            \Sb\Flash\Flash::addItem(__("Message envoyé.", "s1b"));
            \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_MAILBOX);
        } else {
            \Sb\Flash\Flash::addItem(__("Au moins l'un des champs n'est pas rempli", "s1b"));
        }
    }
}