<?php

use \Sb\Helpers\EntityHelper;
use \Sb\Helpers\ArrayHelper;
use \Sb\Trace\Trace;
use \Sb\Db\Service\BookSvc;
use \Sb\Db\Dao\UserDao;
use \Sb\Db\Model\Model;
use \Sb\Db\Model\UserBook;
use \Sb\Db\Model\Book;
use \Sb\Db\Model\User;

$user = $context->getConnectedUser();

// Get friend list for friend selection form
$friends = $user->getAcceptedFriends();
// Order the friends list by firstname asc 
usort($friends, "compareFirstName");


$selectedFrienId = ArrayHelper::getSafeFromArray($_GET, "friendId", null);
if ($selectedFrienId) {
    $selectedFriend = UserDao::getInstance()->get($selectedFrienId);

    $friendBooks = $selectedFriend->getNotDeletedUserBooks();
    $friendWishedBooks = array_filter($friendBooks, "isWished");

    try {
        $booksHeCouldLikes = BookSvc::getInstance()->getBooksUserCouldLike($selectedFrienId);
    } catch (\Exception $exc) {
        Trace::addItem("une erreur s'est produite lors de la récupération des livres qui pourrait plaire : " . $exc->getMessage());
    }
}

function hasNot(Book $book) {
    global $friendBookIds;
    return !in_array($book->getId(), $friendBookIds, true);
}

function getId(Model $model) {
    return $model->getId();
}

function getBookId(UserBook $userBook) {
    return $userBook->getBook()->getId();
}

function isWished(UserBook $userBook) {
    if ($userBook->getIsWished()) {
        return true;
    }
}

function compareFirstName(User $user1, User $user2) {
    return EntityHelper::compareBy($user1, $user2, EntityHelper::ASC, "getFirstName");
}