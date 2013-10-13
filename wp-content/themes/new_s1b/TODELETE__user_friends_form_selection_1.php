<?php

$user = $context->getConnectedUser();
$friends = $user->getFriendsForEmailing();
sortByUserName($friends);


$nbRecipients = count($friends);

if ($nbRecipients <= 0) {
    \Sb\Flash\Flash::addItem(__("Pas de destinataire possible. Vous devez ajouter des amis pour pouvoir envoyer des messages.", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}

$action = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_MAILBOX_SEND_MESSAGE);

function sortByUserName(&$friends) {
    usort($friends, "compareByUserNameAsc");
}

function compareByUserNameAsc(\Sb\Db\Model\User $friend1, \Sb\Db\Model\User $friend2) {
    $val1 = strtoupper(call_user_func(array(&$friend1, "getUserName")));
    $val2 = strtoupper(call_user_func(array(&$friend2, "getUserName")));
    if ($val1 == $val2) {
        return 0;
    } return ($val1 < $val2) ? -1 : 1;
}