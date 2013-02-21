<?php

/*
 * First page when clicking on "Add" from search result page
 * - complete book info if necessary
 * - check if book is valid for adding or not
 * - store book in cache (or session)
 * - redirect to preadd page (where choice between add and borrow)
 */

use \Sb\Helpers\HTTPHelper;
use \Sb\Entity\Constants;

Sb\Trace\Trace::addItem(Sb\Entity\LibraryPages::USERBOOK_PREPAREADD);

global $s1b;
$context = $s1b->getContext();

if ($context->getIsShowingFriendLibrary()) {
    Throw new \Sb\Exception\UserException(__("Vous ne pouvez pas ajouter un livre à la bibliothèque d'un ami.", "s1b"));
}

// Remove book to add in cache
\Sb\Cache\ZendFileCache::getInstance()->remove(Constants::BOOK_TO_ADD_PREFIX . session_id());

// Get Book from POST
$book = new \Sb\Db\Model\Book();
\Sb\Db\Mapping\BookMapper::map($book, $_POST, "book_");

// checking if book is already in DB
$isBookInDb = false;
$bookInUserLib = false;

if ($book->getId()) {
    $isBookInDb = true;
} else {
    $bookInDb = \Sb\Db\Dao\BookDao::getInstance()->getOneByCodes($book->getISBN10(), $book->getISBN13(), $book->getASIN());
    if ($bookInDb) {
        $isBookInDb = true;
        $book = $bookInDb;
    }
}

$requestedDest = HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_ADDCHOICE), false, false);
if (\Sb\Helpers\ArrayHelper::getSafeFromArray($_POST, \Sb\Entity\LibraryPages::LENDING_BORROWFROMFRIENDS, null))
    $requestedDest = HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::LENDING_BORROWFROMFRIENDS), false, false);
else
    $seeBookLinkClicked = true;


// Si le livre existe déjà en base
// Vérification de l'existence du livre pour l'utilisateur
// et si oui redirection vers la page d'édition
if ($isBookInDb) {

    \Sb\Trace\Trace::addItem(__("Vérification de l'existence du livre pour l'utilisateur.", "s1b"));
    $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();
    $userBook = $userBookDao->getByBookIdAndUserId($context->getConnectedUser()->getId(), $book->getId());

    // Testing if the user :
    // - doesn't already have that book or 
    // - have it but is deleted : in this case we will undelete the book
    if ($userBook && !$userBook->getIs_deleted()) {
        $bookInUserLib = true;
        if (!$seeBookLinkClicked)
            \Sb\Flash\Flash::addItem(__("Vous avez déjà ce livre dans votre bibliothèque.", "s1b"));
    }
}

// On complète les infos qui manquent éventuellement
if (!$book->IsComplete()) {
    \Sb\Trace\Trace::addItem('Requêtage de Google.');
    \Sb\Helpers\BookHelper::completeInfos($book);
}
if (!$book->IsValid()) {
    \Sb\Flash\Flash::addItem('Il manque certaines données pour ajouter ce livre à notre base de données.');
    HTTPHelper::redirectToReferer();
} else {
    \Sb\Cache\ZendFileCache::getInstance()->save($book, Constants::BOOK_TO_ADD_PREFIX . session_id());
}

if ($isBookInDb) {
    if ($bookInUserLib) {
        HTTPHelper::redirect($book->getLink());
    } else {
        HTTPHelper::redirect($requestedDest);
    }
}
else
    HTTPHelper::redirect($requestedDest);