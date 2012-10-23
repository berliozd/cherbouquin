<?php

$user = $context->getConnectedUser();
$friends = $user->getAcceptedFriends();

// filter in case a search query is done
$searchTerm = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "q", null);
if ($searchTerm) {
    $friends = array_filter($friends , "filterBySearchTerm");
}

if ($friends && count($friends) > 0) {
    // preparing pagination
    $paginatedList = new \Sb\Lists\PaginatedList($friends, 6);
    $firstItemIdx = $paginatedList->getFirstPage();
    $lastItemIdx = $paginatedList->getLastPage();
    $nbItemsTot = $paginatedList->getTotalPages();
    $navigation = $paginatedList->getNavigationBar();
    $friends = $paginatedList->getItems();
}

$nbFriends = count($friends);
if ($nbFriends == 0)
    $noFriendsMessage = __("Aucun amis", "s1b");

function filterBySearchTerm(\Sb\Db\Model\User $user) {
    global $searchTerm;
    if (preg_match("/$searchTerm/i", $user->getFirstName()) || preg_match("/$searchTerm/i", $user->getLastName()) || preg_match("/$searchTerm/i",
                    $user->getUserName())) {
        return true;
    }
    return false;
}

function isAccepted(\Sb\Db\Model\FriendShip $friendShip) {
    if ($friendShip->getAccepted()) {
        return true;
    }
}