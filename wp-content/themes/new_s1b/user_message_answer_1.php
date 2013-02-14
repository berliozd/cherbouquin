<?php

$messageId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'mid', null);

$redirect = false;
if ($messageId) {
    $message = \Sb\Db\Dao\MessageDao::getInstance()->get($messageId);
    if ($message->getRecipient()->getId() != $context->getConnectedUser()->getId()) {
        \Sb\Flash\Flash::addItem(__("Vous ne pouvez pas répondre à ce message car il ne vous est pas destiné.", "s1b"));
        $redirect = true;
    }
} else {
    \Sb\Flash\Flash::addItem(__("Le message auquel vous tentez de répondre n'existe pas.", "s1b"));
    $redirect = true;
}


if ($_POST) {
    $title = htmlspecialchars($_POST['Title']);
    $messageContent = htmlspecialchars($_POST['Message']);
    /* test if form is not empty */
    if (!empty($title) && !empty($messageContent)) {

        // create new message in db
        $reply = new \Sb\Db\Model\Message;
        $reply->setRecipient($message->getSender());
        $replySender = $context->getConnectedUser();
        $reply->setSender($replySender);
        $reply->setDate(new \DateTime);
        $reply->setTitle($title);
        $reply->setMessage($messageContent);
        $reply->setIs_read(false);
        \Sb\Db\Dao\MessageDao::getInstance()->add($reply);

        if ($message->getSender()->getSetting()->getEmailMe() == 'Yes') {
            // send a email to warn the origianl sender of the email
            $body = \Sb\Helpers\MailHelper::newMessageArrivedBody($replySender->getUserName());
            \Sb\Service\MailSvc::getInstance()->send($message->getSender()->getEmail(), sprintf(__("Un message vous a été envoyé depuis le site %s", "s1b"), \Sb\Entity\Constants::SITENAME), $body);
        }
        \Sb\Flash\Flash::addItem(__("Message envoyé.", "s1b"));
        $redirect = true;
    } else
        \Sb\Flash\Flash::addItem(__("Vous devez renseigné le titre et le contenu du message.", "s1b"));
}

if ($redirect)
    \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_MAILBOX);