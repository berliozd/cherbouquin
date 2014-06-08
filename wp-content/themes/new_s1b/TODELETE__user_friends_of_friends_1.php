<?php

$user = $context->getConnectedUser();
$friendsFriendShips = \Sb\Db\Dao\FriendShipDao::getInstance()->getFriendsFriendShips($user->getId());
$friendsFriends = array_map("getTargetUser", $friendsFriendShips);
$friendsFriends = array_filter($friendsFriends, "isNotMe");
$friendsFriends = array_filter($friendsFriends, "isNotDeleted");

$allUsers = \Sb\Db\Dao\UserDao::getInstance()->getAll();
$allUsers = array_filter($allUsers, "isNotDeleted");
$nbUsers = count($allUsers);

if ($friendsFriends && count($friendsFriends) > 0) {
    // preparing pagination
    $paginatedList = new \Sb\Lists\PaginatedList($friendsFriends, 9);
    $firstItemIdx = $paginatedList->getFirstPage();
    $lastItemIdx = $paginatedList->getLastPage();
    $nbItemsTot = $paginatedList->getTotalPages();
    $navigation = $paginatedList->getNavigationBar();
    $friendsFriends = $paginatedList->getItems();
}

function getTargetUser(\Sb\Db\Model\FriendShip $friendShip) {
    return $friendShip->getUser_target();
}

function isNotMe(\Sb\Db\Model\User $friend) {
    global $user;
    return $friend->getId() != $user->getId();
}

function isNotDeleted(\Sb\Db\Model\User $friend) {
    return !$friend->getDeleted();
}
