<?php

// post for deleting messages
if ($_POST) {
    $messagesToDeleteIds = \Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, 'delete', null);
    if ($messagesToDeleteIds) {
        $messagesToDelete = array();
        foreach ($messagesToDeleteIds as $messagesToDeleteId) {
            $message = \Sb\Db\Dao\MessageDao::getInstance()->get($messagesToDeleteId);
            if ($message)
                $messagesToDelete[] = $message;
        }
    }
    if ($messagesToDelete && count($messagesToDelete > 0)) {
        \Sb\Db\Dao\MessageDao::getInstance()->bulkRemove($messagesToDelete);
        \Sb\Flash\Flash::addItem(__("Le ou les messages ont été supprimés.", "s1b"));
    }
}

$user = $context->getConnectedUser();
//$messages = $user->getMessages_received();
$messages = \Sb\Db\Dao\MessageDao::getInstance()->getAll(array("recipient" => $user),
        array("date" => \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "sortby", "DESC")));

$dateCSSClass = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "sortby", "DESC");

if ($messages && count($messages) > 0) {
    // preparing pagination
    $paginatedList = new \Sb\Lists\PaginatedList($messages, 4);
    $firstItemIdx = $paginatedList->getFirstPage();
    $lastItemIdx = $paginatedList->getLastPage();
    $nbItemsTot = $paginatedList->getTotalPages();
    $navigation = $paginatedList->getNavigationBar();
    $messages = $paginatedList->getItems();
}

