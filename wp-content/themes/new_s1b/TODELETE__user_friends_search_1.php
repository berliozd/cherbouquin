<?php
$user = $context->getConnectedUser();
$allUsers = \Sb\Db\Dao\UserDao::getInstance()->getAll();
$allUsers = array_filter($allUsers, "isNotDeleted");
$nbUsers = count($allUsers);


$query = null;
if ($_GET) {
    $query = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'q', null);

    if (strpos($query, "%") !== false && strlen($query) == 1) {
        \Sb\Flash\Flash::addItem(__("Le caractère % n'est pas autorisé lors des recherches.", "s1b"));
        \Sb\Helpers\HTTPHelper::redirectToReferer();
    }

    if ($query) {
        $foundUsers = \Sb\Db\Dao\UserDao::getInstance()->getListByKeyword($query);
        $foundUsers = array_filter($foundUsers, "isNotMe");
        $foundUsers = array_filter($foundUsers, "isNotAdmin");
        $foundUsers = array_filter($foundUsers, "isNotDeleted");

        if ($foundUsers && count($foundUsers) > 0) {
            // preparing pagination
            $paginatedList = new \Sb\Lists\PaginatedList($foundUsers, 9);
            $firstItemIdx = $paginatedList->getFirstPage();
            $lastItemIdx = $paginatedList->getLastPage();
            $nbItemsTot = $paginatedList->getTotalPages();
            $navigation = $paginatedList->getNavigationBar();
            $foundUsers = $paginatedList->getItems();
        }
    }
}

function isNotMe(\Sb\Db\Model\User $foundUser) {
    global $user;
    return $foundUser->getId() != $user->getId();
}

function isNotDeleted(\Sb\Db\Model\User $foundUser) {
    return !$foundUser->getDeleted();
}

function isNotAdmin(\Sb\Db\Model\User $foundUser) {
    return $foundUser->getId() != 1;
}
