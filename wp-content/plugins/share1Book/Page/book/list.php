<?php

global $s1b;
$context = $s1b->getContext();

Sb\Trace\Trace::addItem(Sb\Entity\LibraryPages::BOOK_LIST);

// Get the list key (allBooks, wishedBooks, etc...)
$key = share1Book::ALL_BOOKS_KEY;
if ($_GET && array_key_exists("key", $_GET) && $s1b->isValidBooksKey($_GET["key"])) {
    $key = $_GET["key"];
}
$fullKey = $this->formateListKey($key);

// Reset the list options (sorting, searching, paging, filtering) if requested
$reset = Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "reset", false);
if ($reset)
    $s1b->resetListOption($fullKey);

$filteringOrSearching = (array_key_exists("searchvalue", $_GET) || (array_key_exists("filter", $_GET) && array_key_exists("filtertype", $_GET)));

if ($filteringOrSearching) {

    if (array_key_exists("searchvalue", $_GET)) {
        // assignation du paramètre de recherche
        $searchValue = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "searchvalue", null);
        $s1b->setListOptionsForSearching($fullKey, $searchValue);
    } else if (array_key_exists("filter", $_GET) && array_key_exists("filtertype", $_GET)) {
        // assignation du paramètre de filtrage
        $filteringValue = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "filter", null);
        $filteringType = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "filtertype", null);
        $s1b->setListOptionsForFiltering($fullKey, $filteringValue, $filteringType);
    }

    // repositionnement sur la premiere page
    $s1b->setListOptionsForNavigation($fullKey, 1);

    // We work on the data in cache
    $books = \Sb\Db\Service\UserBookSvc::getInstance()->getUserBooks($key, $context->getLibraryUserId(), true);
} else { // Pas de POST : requetage de SQL
    // Dont use cache on first call
    $books = \Sb\Db\Service\UserBookSvc::getInstance()->getUserBooks($key, $context->getLibraryUserId(), false);

    //var_dump($books);
    if ($books) {
        // Save authors first letters in cache
        $authorsFirstLetter = array_unique(array_map("firstLetterFromAuthor", $books));
        usort($authorsFirstLetter, "compareLetters");
        $s1b->setListMetaData($fullKey, $authorsFirstLetter, Sb\Lists\MetaDataType::AUTHORS_FIRST_LETTERS);

        // Save titles first letters in cache
        $titlesFirstLetter = array_unique(array_map("firstLetterFromTitle", $books));
        usort($titlesFirstLetter, "compareLetters");
        $s1b->setListMetaData($fullKey, $titlesFirstLetter, Sb\Lists\MetaDataType::TITLES_FIRST_LETTERS);
    }
}

//Doctrine\Common\Util\Debug::dump($books);
// Prepare list view
$booksTableView = $s1b->createBookTableView($key, $books, false);
$view = new \Sb\View\BookList($key, $booksTableView, $key);

echo $view->get();

//////////////////////////////////////////////////////////////
function firstLetterFromTitle(\Sb\Db\Model\UserBook $userBook) {
    return strtoupper(substr($userBook->getBook()->getTitle(), 0, 1));
}

function firstLetterFromAuthor(\Sb\Db\Model\UserBook $userBook) {
    return strtoupper(substr($userBook->getBook()->getOrderableContributors(), 0, 1));
}

function compareLetters($letterA, $letterB) {
    return ($letterA < $letterB) ? -1 : 1;
}
