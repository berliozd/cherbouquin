<?php
use Sb\Db\Dao\UserBookDao;
use Sb\Db\Service\BookSvc;
use Sb\Db\Service\UserEventSvc;
use Sb\Entity\EventTypes;
use Sb\Db\Model\UserBook;

$noUser = false;
$friendId = $_GET['fid'];

if ($friendId) {

    $friend = \Sb\Db\Dao\UserDao::getInstance()->get($friendId);
    if ($friend) {
        if ($friend->getId() == $context->getConnectedUser()->getId()) {
            \Sb\Flash\Flash::addItem(__("Il s'agit de votre profil!", "s1b"));
            \Sb\Helpers\HTTPHelper::redirectToReferer();
        } else {
            $requestingUser = $context->getConnectedUser();
            if (\Sb\Helpers\SecurityHelper::IsUserAccessible($friend, $requestingUser)) {
                $friendSetting = $friend->getSetting();

                $isFriend = \Sb\Db\Service\UserSvc::getInstance()->areUsersFriends($context->getConnectedUser(), $friend);

                // getting currently reading or lastly read books
                $currentlyReading = \Sb\Db\Dao\UserBookDao::getInstance()->getReadingNow($friend->getId());
                $lastlyReads = \Sb\Db\Dao\UserBookDao::getInstance()->getListLastlyRead($friend->getId());
                if ($currentlyReading && $lastlyReads) {
                    $currentlyReadingOrLastlyReadBooks = array_merge(array($currentlyReading), $lastlyReads);
                } elseif ($lastlyReads) {
                    $currentlyReadingOrLastlyReadBooks = $lastlyReads;
                } elseif ($currentlyReading) {
                    $currentlyReadingOrLastlyReadBooks = array($currentlyReading);
                }
                
                // Getting friend currently reading user books
                $allCurrentlyReadingUserBooks = UserBookDao::getInstance()->getCurrentlyReadingsNow($friend->getId());

                // Getting friend last boh books
                $bohUserBooks = UserBookDao::getInstance()->getListUserBOH($friend->getId());
                $bohBooks = array_map("getBook", $bohUserBooks);

                // Getting books friend could like
                $booksHeCouldLikes = BookSvc::getInstance()->getBooksUserCouldLike($friend->getId());

                // Getting friend's friends last reviews
                $friendLastReviews = UserEventSvc::getInstance()->getUserLastEventsOfType($friend->getId(), EventTypes::USERBOOK_REVIEW_CHANGE);                

                // Getting friend last friends added events
                $friendLastFriendsAddedEvents = UserEventSvc::getInstance()->getUserLastEventsOfType($friend->getId(), EventTypes::USER_ADD_FRIEND);
                
                // Getting friend last events
                $friendLastEvents = UserEventSvc::getInstance()->getUserLastEventsOfType($friend->getId(), null, 15);
                
            } else {
                \Sb\Flash\Flash::addItem(__("Vous ne pouvez pas accéder à ce profil.", "s1b"));
                \Sb\Helpers\HTTPHelper::redirectToReferer();
            }
        }
    } else {
        $noUser = true;
    }
} else {
    $noUser = true;
}

if ($noUser) {
    \Sb\Flash\Flash::addItem(__("Cet utilisateur n'existe pas.", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}

function getBook(UserBook $userBook) {
    return $userBook->getBook();
}