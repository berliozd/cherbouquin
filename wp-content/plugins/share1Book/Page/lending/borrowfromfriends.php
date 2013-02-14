<?php

use \Sb\Helpers\ArrayHelper;
use \Sb\Helpers\StringHelper;
use \Sb\Helpers\HTTPHelper;
use \Sb\Entity\Constants;
use \Sb\Flash\Flash;
use \Sb\Entity\Urls;
use \Sb\Db\Dao\UserBookDao;

global $s1b;
$context = $s1b->getContext();


if (!$s1b->getIsSubmit()) {

    $bookIdInIQS = \Sb\Helpers\ArrayHelper::getSafeFromArray($_GET, "bid", null);
    if ($bookIdInIQS) {
        $book = \Sb\Db\Dao\BookDao::getInstance()->get($bookIdInIQS);
    } else {
        // Get Book to add from cache
        $book = \Sb\Cache\ZendFileCache::getInstance()->load(Constants::BOOK_TO_ADD_PREFIX . session_id());
    }

    if ($book) {
        $userBookInDb = UserBookDao::getInstance()->getByBookIdAndUserId($context->getConnectedUser()->getId(), $book->getId());
        if ($userBookInDb && !$userBookInDb->getIs_deleted()) {
            Flash::addItem(__("Vous avez déjà ce livre dans votre bibliothèque.", "s1b"));
            HTTPHelper::redirectToLibrary();
        }else
            showBorrowingForm($book, $context);
    }
    else
        HTTPHelper::redirectToHome();
} else {

    if (validateUserInputForm()) {

        $bookForm = new \Sb\Form\Book($_POST);

        // testing if book can be found in db by id
        if ($bookForm->getId()) {
            $bookInDb = \Sb\Db\Dao\BookDao::getInstance()->get($bookForm->getId());
        }

        // testing if book can be found in db by isbn10, isbn13, asin
        if (!$bookInDb) {
            $bookInDb = \Sb\Db\Dao\BookDao::getInstance()->getOneByCodes($bookForm->getISBN10(), $bookForm->getISBN13(), $bookForm->getASIN());
        }

        // getting the book data from post and adding to db
        if (!$bookInDb) {
            // Récupération du Book depuis le POST
            $bookInDb = new \Sb\Db\Model\Book();
            //var_dump($_POST);
            \Sb\Db\Mapping\BookMapper::map($bookInDb, $_POST, "book_");
            // book not in db : need to add it
            $bookInDb->setCreationDate(new \DateTime());
            $bookInDb->setLastModificationDate(new \DateTime());
            \Sb\Db\Dao\BookDao::getInstance()->add($bookInDb);
        }


        if ($bookInDb) {

            $guestName = ArrayHelper::getSafeFromArray($_POST, "guest_name", null);
            $guestEmail = ArrayHelper::getSafeFromArray($_POST, "guest_email", null);

            $guest = new \Sb\Db\Model\Guest;
            $guest->setName($guestName);
            $guest->setEmail($guestEmail);
            $guest->setCreation_date(new \DateTime);

            $createLending = true;

            if ($guestEmail) {
                $friendToBorrowInDb = \Sb\Db\Dao\UserDao::getInstance()->getByEmail($guestEmail);
                if ($friendToBorrowInDb) {
                    $createLending = false;
                    showBorrowingForm($bookInDb, $context);
                    //$connectedUser = $context->getConnectedUser();
                    Flash::addItem(sprintf(__("Un utilisateur existe déjà avec l'email que vous avez saisi. Nous vous proposons de lui envoyer une demande d'ami. Vous pourrez ensuite lui emprunter ce livre directement depuis sa bibliothèque. <a class=\"link\" href=\"%s\">Envoyer une demande d'ami</a>", "s1b"), \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_FRIENDS_REQUEST, array("fid" => $friendToBorrowInDb->getId()))));
                } else {
                    $token = sha1(uniqid(rand()));

                    // Send invite email                
                    $message = __(sprintf("%s vous invite à rejoindre %s, réseau communautaire autour du livre et de la lecture.", sprintf("%s %s", $context->getConnectedUser()->getFirstName(), $context->getConnectedUser()->getLastName()), $_SERVER["SERVER_NAME"]), "s1b");
                    $message .= "<br/><br/>";
                    $message .= sprintf(__("Il a utilisé %s pour noter qu'il vous a emprunté \"%s\"."), Constants::SITENAME, $bookInDb->getTitle());
                    $message .= "<br/><br/>";
                    $message .= __("Venez échanger sur vos lectures et coups de coeur, chercher l'inspiration grâce aux recommandations, gérer et partager votre bibliothèque avec vos amis et trouver dans leurs listes d'envies des idées de cadeaux.");
                    $message .= "<br/><br/>";
                    $subscriptionLink = HTTPHelper::Link(Urls::SUBSCRIBE);
                    $refuseInvitationLink = HTTPHelper::Link(Urls::REFUSE_INVITATION, array("Token" => $token, "Email" => $guestEmail));
                    $message .= sprintf(__("L'inscription est gratuite ! Rejoignez-nous... <a href=\"%s\">S'inscrire</a> ou <a href=\"%s\">Refuser l'invitation</a>"), $subscriptionLink, $refuseInvitationLink);
                    $message .= "<br/><br/>";
                    $message .= sprintf(__("<strong>L'équipe Cherbouquin</strong>", "s1b"), Constants::SITENAME);
                    \Sb\Service\MailSvc::getInstance()->send($guestEmail, sprintf(__("Invitation à rejoindre %s", "s1b"), Constants::SITENAME), $message);

                    // Create invitation in DB
                    $invitation = new \Sb\Db\Model\Invitation;
                    $invitation->setSender($context->getConnectedUser());
                    $invitation->setGuest($guest);
                    $invitation->setCreation_date(new \DateTime);
                    $invitation->setToken($token);

                    \Sb\Db\Dao\InvitationDao::getInstance()->add($invitation);
                    Flash::addItem(__("Un email d'invitation a été envoyé à votre ami.", "s1b"));
                }
            } else {
                \Sb\Db\Dao\GuestDao::getInstance()->add($guest);
            }

            if ($createLending) {

                // Testing if the user has the book in his lib but marked as deleted
                $userBookBorrower = \Sb\Db\Dao\UserBookDao::getInstance()->getByBookIdAndUserId($context->getConnectedUser()->getId(), $bookInDb->getId());
                if ($userBookBorrower && $userBookBorrower->getIs_deleted()) {
                    $userBookBorrower->setIs_deleted(false);
                    $userBookBorrower->setLastModificationDate(new \DateTime);
                    \Sb\Db\Dao\UserBookDao::getInstance()->update($userBookBorrower);
                    Flash::addItem(sprintf(__("Vous aviez déjà le livre \"%s\" dans votre bibliothèque mais l'aviez supprimé. Il a été rajouté.", "s1b"), $bookInDb->getTitle()));
                } else {
                    // Create userbook for connected user
                    $userBookBorrower = new \Sb\Db\Model\UserBook;
                    $userBookBorrower->setUser($context->getConnectedUser());
                    $userBookBorrower->setBook($bookInDb);
                    $userBookBorrower->setCreationDate(new \DateTime());
                    $userBookBorrower->setBorrowedOnce(true);
                    $userBookBorrowerId = \Sb\Db\Dao\UserBookDao::getInstance()->add($userBookBorrower);
                    Flash::addItem(__("Le livre a été ajouté à votre bibliothèque.", "s1b"));
                }

                $lending = new \Sb\Db\Model\Lending;
                $lending->setBorrower_userbook($userBookBorrower);
                $lending->setGuest($guest);
                $lending->setCreationDate(new \DateTime());
                $lending->setState(\Sb\Lending\Model\LendingState::ACTIV);
                $lending->setStartDate(new \DateTime());
                $lendingId = \Sb\Db\Dao\LendingDao::getInstance()->add($lending);
                HTTPHelper::redirectToLibrary();
            }
        }
    }
}

//////////////////////////////////////////////////////////
function IsBorrowable(\Sb\Db\Model\UserBook $userBook) {
    // test if user owns the book
    if (!$userBook->getIsOwned())
        return false;
    // test if books is not lent
    $activeLending = $userBook->getActiveLending();
    if ($activeLending) {
        return false;
    }
    return true;
}

function showBorrowingForm(\Sb\Db\Model\Book $book, \Sb\Context\Model\Context $context) {

    $bookId = $book->getId();

    if ($bookId) {
        // Checking if a friend has this book in his library
        $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();

        $user = $context->getConnectedUser();
        $friends = $user->getAcceptedFriends();
        if ($friends) {
            $userBooks = $userBookDao->getBookInFriendsUserBook($bookId, $context->getConnectedUser()->getId());
            if ($userBooks)
                $userBooksBorrowable = array_filter($userBooks, "IsBorrowable");
        }
    }

    // Préparation du template
    $tpl = new \Sb\Templates\Template("borrowFromFriendsForm");

    $bookView = new \Sb\View\Book($book, false, false, true, false);
    $tpl->set("book", $bookView->get());

    $tpl->setVariables(array("friendUserBooks" => $userBooksBorrowable));

    echo $tpl->output();
}

function validateUserInputForm() {
    $ret = true;
    if ($_POST) {

        if (strlen(ArrayHelper::getSafeFromArray($_POST, "guest_name", NULL)) < 3) {
            Flash::addItem(__("Le nom doit comprendre au moins 3 caractères.", "s1b"));
            $ret = false;
        }

        if (ArrayHelper::getSafeFromArray($_POST, "send_invitation", NULL) == 1) {
            $guestEmail = ArrayHelper::getSafeFromArray($_POST, "guest_email", NULL);
            if (!$guestEmail) {
                Flash::addItem(__("Vous devez renseigné un email si vous souhaitez envoyer une invitation.", "s1b"));
                $ret = false;
            } else {
                if (!StringHelper::isValidEmail($guestEmail)) {
                    Flash::addItem(__("L'email que vous avez renseigné n'est pas valide. Merci de réessayer.", "s1b"));
                    $ret = false;
                }
            }
        }
    } else {
        $ret = false;
    }
    return $ret;
}