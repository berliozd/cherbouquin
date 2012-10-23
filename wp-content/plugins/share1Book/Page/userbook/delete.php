<?php

use \Sb\Entity\Urls;
use Sb\Entity\LibraryListKeys;
use Sb\Entity\LibraryPages;
use \Sb\Helpers\HTTPHelper;

\Sb\Trace\Trace::addItem(\Sb\Entity\LibraryPages::USERBOOK_DELETE);

global $s1b;
$context = $s1b->getContext();

if ($context->getIsShowingFriendLibrary())
    Throw new \Sb\Exception\UserException(__("Vous ne pouvez pas supprimer le livre d'un ami.", "s1b"));

$userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($_GET['ubid']);
if ($userBook) {

    if ($userBook->getUser()->getId() != $context->getConnectedUser()->getId())
        Throw new \Sb\Exception\UserException(__("Vous ne pouvez pas supprimer un livre qui ne vous appartient pas.", "s1b"));

    if ($userBook->getActiveLending() || $userBook->getActiveborrowing())
        \Sb\Flash\Flash::addItem(sprintf(__("Le livre \"%s\" ne peut pas être supprimé de votre bibliothèque car il est associé à un prêt en cours.", "share1book"), $userBook->getBook()->getTitle()));

    else {
        \Sb\Db\Dao\UserBookDao::getInstance()->delete($userBook);
        \Sb\Flash\Flash::addItem(sprintf(__("Le livre \"%s\" a été supprimé de votre bibliothèque.", "s1b"), $userBook->getBook()->getTitle()));
    }
} else {
    \Sb\Flash\Flash::addItem(__("Le livre que vous souhaitez supprimer n'existe pas.", "s1b"));
}

$referer = HTTPHelper::getReferer();
\Sb\Helpers\HTTPHelper::redirectToUrl($referer);