<?php

$user = $context->getConnectedUser();

if (!$_POST) {
    $totalPendingRequests = $user->getPendingFriendShips();

    if ($totalPendingRequests && count($totalPendingRequests) > 0) {
        // preparing pagination
        $paginatedList = new \Sb\Lists\PaginatedList($totalPendingRequests, 6);
        $firstItemIdx = $paginatedList->getFirstPage();
        $lastItemIdx = $paginatedList->getLastPage();
        $nbItemsTot = $paginatedList->getTotalPages();
        $navigation = $paginatedList->getNavigationBar();
        $pendingRequests = $paginatedList->getItems();
    }
} else {

    $friendShipId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'friendShipId', null);
    $Title = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'Title', null);
    $Message = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'Message', null);
    $Refused = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'Refused', null);
    if ($friendShipId) {
        if ($Refused == 0) {

            // update the requested friendship
            $friendShip = \Sb\Db\Dao\FriendShipDao::getInstance()->get($friendShipId);
            if ($friendShip) {
                $friendShip->setAccepted(true);
                $friendShip->setValidated(true);
                \Sb\Db\Dao\FriendShipDao::getInstance()->update($friendShip);
            }
            // create a friendship on the other side
            $inverseFriendShip = new \Sb\Db\Model\FriendShip;
            $inverseFriendShip->setAccepted(true);
            $inverseFriendShip->setValidated(true);
            $inverseFriendShip->setCreationDate(new \DateTime());
            $inverseFriendShip->setUser_source($user);
            $inverseFriendShip->setUser_target($friendShip->getUser_source());
            \Sb\Db\Dao\FriendShipDao::getInstance()->add($inverseFriendShip);


            // send email to the requesting user
            $mailSvc->send($friendShip->getUser_source()->getEmail(), __("Demande d'ami", "s1b"),
                    \Sb\Helpers\MailHelper::friendShipAcceptationEmailBody($user->getFirstName() . " " . $user->getLastName()));


            // add a message in requesting user internal mailbox
            $message = new \Sb\Db\Model\Message;
            $message->setDate(new \DateTime());
            $message->setMessage($Message);
            $message->setTitle($Title);
            $message->setRecipient($friendShip->getUser_source());
            $message->setSender($user);
            \Sb\Db\Dao\MessageDao::getInstance()->add($message);

            // redirect to pending request page
            \Sb\Flash\Flash::addItem("Demande acceptée.");
            \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_FRIENDS_PENDING_REQUEST);
        } elseif ($Refused == 1) {

            // update the requested friendship
            $friendShip = \Sb\Db\Dao\FriendShipDao::getInstance()->get($friendShipId);
            if ($friendShip) {
                $friendShip->setAccepted(false);
                $friendShip->setValidated(true);
                \Sb\Db\Dao\FriendShipDao::getInstance()->update($friendShip);
            }

            // send email to the requesting user
            $mailSvc->send($friendShip->getUser_source()->getEmail(), __("Votre demande d'ami a été refusée", "s1b"),
                    \Sb\Helpers\MailHelper::friendShipDenyEmailBody($user->getFirstName() . " " . $user->getLastName()));

            // add a message in requesting user internal mailbox
            $message = new \Sb\Db\Model\Message;
            $message->setDate(new \DateTime());
            $message->setMessage($Message);
            $message->setTitle($Title);
            $message->setRecipient($friendShip->getUser_source());
            $message->setSender($user);
            \Sb\Db\Dao\MessageDao::getInstance()->add($message);

            // redirect to pending request page
            \Sb\Flash\Flash::addItem(__("Demande refusée.", "s1b"));
            \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_FRIENDS_PENDING_REQUEST);
        }
    } else {
        \Sb\Flash\Flash::addItem(__("Vous devez sélectionner une demande d'ami.", "s1b"));
        \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_FRIENDS_PENDING_REQUEST);
    }
}
