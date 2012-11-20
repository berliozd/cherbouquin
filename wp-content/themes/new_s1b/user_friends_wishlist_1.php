<?php

use \Sb\Helpers\EntityHelper;
use \Sb\Helpers\ArrayHelper;

$user = $context->getConnectedUser();

// Get friend list for friend selection form
$friends = $user->getAcceptedFriends();
// Order the friends list by firstname asc 
usort($friends, "compareFirstName");


$selectedFrienId = ArrayHelper::getSafeFromArray($_GET, "friendId", null);
if ($selectedFrienId) {
    $selectedFriend = \Sb\Db\Dao\UserDao::getInstance()->get($selectedFrienId);
    $friendWishedBooks = $selectedFriend->getNotDeletedUserBooks();
    $friendWishedBooks = array_filter($friendWishedBooks, "isWished");
}

function isWished(\Sb\Db\Model\UserBook $userBook) {
    if ($userBook->getIsWished()) {
        return true;
    }
}

function compareFirstName(\Sb\Db\Model\User $user1, \Sb\Db\Model\User $user2) {
    return EntityHelper::compareBy($user1, $user2, EntityHelper::ASC, "getFirstName");
}