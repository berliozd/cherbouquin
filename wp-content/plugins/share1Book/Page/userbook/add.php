<?php

use \Sb\Db\Service\UserBookSvc;
use \Sb\Helpers\HTTPHelper;
use \Sb\Flash\Flash;
use \Sb\Exception\UserException;

\Sb\Trace\Trace::addItem("userbook/addbook");

global $s1b;
$context = $s1b->getContext();
$config = $s1b->getConfig();

if ($context->getIsShowingFriendLibrary()) {
    Throw new UserException(__("Vous ne pouvez pas ajouter un livre à la bibliothèque d'un ami.", "s1b"));
}

if ($context->getConnectedUser()) {
    $id = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "bid", null);
    if ($id) {
        $returnCode = UserBookSvc::getInstance()->addFromBookId($id, $context->getConnectedUser(), $config);
        Flash::addItem($returnCode);
    }
        
    else
        Flash::addItem(__("Il faut passer un code livre.", "s1b"));
} else
    Flash::addItem(__("Vous devez être connecté pour pouvoir ajouter un livre.", "s1b"));

HTTPHelper::redirectToReferer();