<?php

$noUser = false;
$friendId = $_GET['fid'];

if ($friendId) {

    $friend = \Sb\Db\Dao\UserDao::getInstance()->get($friendId);
    if ($friend) {
        if ($friend->getId() == $context->getConnectedUser()->getId()) {
            \Sb\Flash\Flash::addItem(__("Il s'agit de votre profile!", "s1b"));
            \Sb\Helpers\HTTPHelper::redirectToReferer();
        } else {
            $requestingUser = $context->getConnectedUser();
            if (\Sb\Helpers\SecurityHelper::IsUserAccessible($friend, $requestingUser)) {
                $friendSetting = $friend->getSetting();

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

                // getting last boh books
                $bohBooks = \Sb\Db\Dao\UserBookDao::getInstance()->getListUserBOH($friend->getId());

                // getting wished books
                $wishedBooks = \Sb\Db\Dao\UserBookDao::getInstance()->getListWishedBooks($friend->getId(), 10, true);

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