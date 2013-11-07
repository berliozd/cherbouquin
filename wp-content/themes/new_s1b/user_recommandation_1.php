<?php

// getting user
$user = $context->getConnectedUser();
$potentialRecipients = $user->getFriendsForEmailing();

if (count($potentialRecipients) <= 0) {
    \Sb\Flash\Flash::addItem(__("Pas de destinataire possible. Vous devez ajouter des amis pour pouvoir envoyer des recommandations.",
                    "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}

// getting book
$bookId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'id', null);
if (!$bookId) {
    \Sb\Flash\Flash::addItem(__("Vous devez sélectionner un livre.", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}

$book = \Sb\Db\Dao\BookDao::getInstance()->get($bookId);
if (!$book) {
    \Sb\Flash\Flash::addItem(__("Le livre n'existe pas.", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}

$userBook = \Sb\Db\Dao\UserBookDao::getInstance()->getByBookIdAndUserId($context->getConnectedUser()->getId(), $bookId);

$bookLink = \Sb\Helpers\HTTPHelper::Link($book->getLink());

$textMessage = sprintf(__("Recommander %s par %s", "s1b"), $book->getTitle(), $book->getOrderableContributors());

function userAcceptEmail(\Sb\Db\Model\User $user) {
    $setting = $user->getSetting();
    if ($setting->getEmailMe() == "Yes") {
        return true;
    }
}

$friendList = null;

if ($_POST) {
    $friendSelections = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'selection', null);
    $sendingMessage = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'go', null);
    // coming from friend selection page    
    if ($friendSelections) {
        $friendSelectionsIds = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'Friends', null);
        $friendList = array();
        $friendIdList = "";
        foreach ($friendSelectionsIds as $friendSelection) {
            $friend = \Sb\Db\Dao\UserDao::getInstance()->get($friendSelection);
            $friendList[] = $friend;
            $friendIdList .= $friend->getId() . ",";
        }
    } elseif ($sendingMessage) {
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
                            \Sb\Service\MailSvc::getInstance()->send($recipient->getEmail(),
                                    sprintf(__("%s vous recommande %s ", "s1b"), $user->getUserName(), $book->getTitle()), $body);
                        }
                    }
                }
            }
            \Sb\Flash\Flash::addItem(__("Message envoyé.", "s1b"));
            \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_HOME);
        } else {
            \Sb\Flash\Flash::addItem(__("Au moins l'un des champs n'est pas rempli", "s1b"));
        }
    }
}