<?php

$user = $context->getConnectedUser();

// get friend list for friend selection form
$friends = $user->getAcceptedFriends();

if ($_POST) {
    $friendId = $_POST['friendId'];
    if ($friendId) {
        $selectedFriend = \Sb\Db\Dao\UserDao::getInstance()->get($friendId);
        $friendWishedBooks = $selectedFriend->getNotDeletedUserBooks();
        $friendWishedBooks = array_filter($friendWishedBooks, "isWished");
    }
}

function isAccepted(\Sb\Db\Model\FriendShip $friendShip) {
    if ($friendShip->getAccepted()) {
        return true;
    }
}

function isWished(\Sb\Db\Model\UserBook $userBook) {
    if ($userBook->getIsWished()) {
        return true;
    }
}