<?php

$home = \Sb\Helpers\HTTPHelper::Link("");
$returnUri = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
// Testing if user is facebook connected
$facebookSvc = new \Sb\Facebook\Service\FacebookSvc($config->getFacebookApiId(), $config->getFacebookSecret(), $returnUri, $home);
$facebookUser = $facebookSvc->getUser();

if ($facebookUser) {
    // Recherche d'une correspondance dans la base avec le user facebook connecté
    $faceBookEmail = $facebookUser->getEmail();
    $facebookId = $facebookUser->getUid();
    $userInDB = \Sb\Db\Dao\UserDao::getInstance()->getFacebookUser($faceBookEmail);
    if (!$userInDB) {
        $facebookSvc->cleanUser();
        \Sb\Flash\Flash::addItem(__("Utilisateur facebook inconnu", "s1b"));
        $facebookUser = null;
        $faceBookEmail = null;
        $facebookId = null;
    }
} else {
    \Sb\Helpers\HTTPHelper::redirectToUrl($facebookSvc->getFacebookLogInUrl());
}

// getting book data
$idBook = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, 'id', null);
if ($idBook) {
    $book = \Sb\Db\Dao\BookDao::getInstance()->get($idBook);
    if (!$book) {
        \Sb\Flash\Flash::addItem(__("Ce livre n'existe pas.", "s1b"));
        \Sb\Helpers\HTTPHelper::redirectToReferer();
    }
    $bookLink = \Sb\Helpers\HTTPHelper::Link($book->getLink());
} else {
    \Sb\Flash\Flash::addItem(__("Vous devez sélectionner un livre.", "s1b"));
    \Sb\Helpers\HTTPHelper::redirectToReferer();
}


// getting user data
$user = $context->getConnectedUser();

// checking if user owns the book
$userBook = \Sb\Db\Dao\UserBookDao::getInstance()->getByBookIdAndUserId($user->getId(), $book->getId());
$bookNotOwned = false;
if (!$userBook)
    $bookNotOwned = true;

if ($facebookUser) {
    if (isset($_POST['go'])) {
        if ($book) {
            if ($user) {
                $facebookTitle = sprintf(__("%s vous recommande via %s", "s1b"), $user->getFirstName(), \Sb\Entity\Constants::SITENAME);
                $facebookMessage = sprintf(__("%s de %s", "s1b"), $book->getTitle(), $book->getOrderableContributors());
                $bookCover = $book->getImageUrl();
                $message = trim($_POST['post_message']);
                if (!empty($message)) {
                    $postOK = $facebookSvc->post($facebookMessage, $facebookTitle, $message,
                            \Sb\Helpers\HTTPHelper::Link($book->getLink()), $bookCover);

                    if ($postOK) {
                        $recipient = \Sb\Db\Dao\UserDao::getInstance()->get(1); // admin
                        $messageObj = new \Sb\Db\Model\Message();
                        $messageObj->setRecipient($recipient);
                        $messageObj->setSender($user);
                        $messageObj->setDate(new \DateTime());
                        $messageObj->setMessage($message);
                        $messageObj->setIs_facebook_post(true);
                        $messageObj->setTitle($facebookMessage);

                        \Sb\Db\Dao\MessageDao::getInstance()->add($messageObj);

                        \Sb\Flash\Flash::addItem(__("Le message a été posté sur votre mur Facebook.", "s1b"));
                        \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_HOME);
                    } else
                        \Sb\Flash\Flash::addItem(__("Une erreur s'est produite lors de la connexion à Facebook.", "s1b"));
                } else {
                    \Sb\Flash\Flash::addItem(__("Le champ message est vide, merci de le renseigner", "s1b"));
                }
            } else {
                \Sb\Flash\Flash::addItem(__("Pas d'utilisateur correspondant.", "s1b"));
            }
        } else {
            \Sb\Flash\Flash::addItem(__("Pas de livre correspondant.", "s1b"));
        }
    }
} else {
    \Sb\Flash\Flash::addItem(__("Vous n'êtes pas connecté à Facebook.", "s1b"));
}