<?php

require_once 'includes/init.php';
/**
 * Template Name: user_friends_request
 */
$user = $context->getConnectedUser();

$requestFriendId = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'fid', null);
if ($requestFriendId) {

    // testing if a request to that user has been done or if the requested user is already a friend
    $userFriendShips = $user->getFriendships_as_source();
    if ($userFriendShips && count($userFriendShips)) {
        foreach ($userFriendShips as $userFriendShip) {
            if (($userFriendShip->getUser_target()->getId() == $requestFriendId) && ($userFriendShip->getAccepted())) {
                \Sb\Flash\Flash::addItem(__("Vous êtes déja ami avec cet utilisateur.", "s1b"));
                \Sb\Helpers\HTTPHelper::redirectToReferer();
            }
            if (($userFriendShip->getUser_target()->getId() == $requestFriendId) && (!$userFriendShip->getValidated())) {
                \Sb\Flash\Flash::addItem(__("Une demande a déjà été transmise à cet utilisateur.", "s1b"));
                \Sb\Helpers\HTTPHelper::redirectToReferer();
            }
        }
    }

    // testing the accepted or pending frienship that the requested user has initiated
    $requestedUser = \Sb\Db\Dao\UserDao::getInstance()->get($requestFriendId);
    $requestedUserFriendShips = $requestedUser->getFriendships_as_source();
    $connectedUserId = $user->getId();
    if ($requestedUserFriendShips && count($requestedUserFriendShips)) {
        foreach ($requestedUserFriendShips as $requestedUserFriendShip) {
            if (($requestedUserFriendShip->getUser_target()->getId() == $connectedUserId) && ($requestedUserFriendShip->getAccepted())) {
                \Sb\Flash\Flash::addItem(__("Vous êtes déja ami avec cet utilisateur.", "s1b"));
                \Sb\Helpers\HTTPHelper::redirectToReferer();
            }
            if (($requestedUserFriendShip->getUser_target()->getId() == $connectedUserId) && (!$requestedUserFriendShip->getValidated())) {
                \Sb\Flash\Flash::addItem(__("Une demande vous a déjà été transmise de la part de cet utilisateur.", "s1b"));
                \Sb\Helpers\HTTPHelper::redirectToReferer();
            }
        }
    }
} else {
    \Sb\Flash\Flash::addItem(__("Vous devez sélectioner un utilisateur", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}

// add friendship line
$newFriendShip = new \Sb\Db\Model\FriendShip;
$newFriendShip->setCreationDate(new \DateTime);
$newFriendShip->setUser_source($user);
$newFriendShip->setUser_target($requestedUser);
\Sb\Db\Dao\FriendShipDao::getInstance()->add($newFriendShip);

// send email to the requested user
$mailSvc->send($requestedUser->getEmail(), sprintf(__("%s - Vous avez reçu une demande d'ami.", "s1b"), \Sb\Entity\Constants::SITENAME), \Sb\Helpers\MailHelper::friendRequestEmailBody($user->getUserName()));

// add message line for requestedUser
$message = new \Sb\Db\Model\Message;
$message->setRecipient($requestedUser);
$message->setSender($user);
$message->setDate(new \DateTime);
$message->setTitle(__("Demande d'ami", "s1b"));
$message->setMessage(sprintf(__("Bonjour,<br/><br/>Vous avez reçu une demande d'ami de %s.", "s1b"), $user->getUserName()));
$message->setIs_read(false);
\Sb\Db\Dao\MessageDao::getInstance()->add($message);


\Sb\Flash\Flash::addItem(__("Votre demande a bien été envoyée.", "s1b"));
\Sb\Helpers\HTTPHelper::redirectToReferer();