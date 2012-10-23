<?php

require_once 'includes/init.php';
/**
 * Template Name: user_message_delete
 */
$messageId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'mid', null);
if ($messageId) {
    $message = \Sb\Db\Dao\MessageDao::getInstance()->get($messageId);
    if ($message->getRecipient()->getId() == $context->getConnectedUser()->getId()) {
        \Sb\Db\Dao\MessageDao::getInstance()->remove($message);
        \Sb\Flash\Flash::addItem(__("Le message a été supprimé.", "s1b"));
    } else {
        \Sb\Flash\Flash::addItem(__("Vous ne pouvez pas supprimer ce message car il ne vous est pas destiné.", "s1b"));
    }
} else {
    \Sb\Flash\Flash::addItem(__("Le message que vous tentez de supprimer n'existe pas.", "s1b"));
}
\Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_MAILBOX);
