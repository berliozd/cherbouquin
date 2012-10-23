<?php
use \Sb\Entity\Constants;

global $s1b;
$context = $s1b->getContext();
$config = $s1b->getConfig();

if ($context->getIsShowingFriendLibrary()) {
    Throw new \Sb\Exception\UserException(__("Vous ne pouvez pas ajouter un livre à la bibliothèque d'un ami.", "s1b"));
}

if (!$s1b->getIsSubmit()) {
    // Récupération du Book depuis le cache
    $book = \Sb\Cache\ZendFileCache::getInstance()->load(Constants::BOOK_TO_ADD_PREFIX . session_id());
    showBookDetail($book);
} else {

    $bookForm = new \Sb\Form\Book($_POST);

    // testing if book can be found in db by id
    if ($bookForm->getId())
        $book = \Sb\Db\Dao\BookDao::getInstance()->get($bookForm->getId());

    // testing if book can be found in db by isbn10, isbn13, asin
    if (!$book)
        $book = \Sb\Db\Dao\BookDao::getInstance()->getOneByCodes($bookForm->getISBN10(), $bookForm->getISBN13(), $bookForm->getASIN());

    // getting the book data from post and adding to db
    if (!$book) {
        // Récupération du Book depuis le POST
        $book = new \Sb\Db\Model\Book();
        //var_dump($_POST);
        \Sb\Db\Mapping\BookMapper::map($book, $_POST, "book_");
        // book not in db : need to add it
        $book->setCreationDate(new \DateTime());
        $book->setLastModificationDate(new \DateTime());
        \Sb\Db\Dao\BookDao::getInstance()->add($book);
    }

    // Getting current user current nb userbooks in libary
    $user = $context->getConnectedUser();
    $userNbUserBooks = count($user->getNotDeletedUserBooks());

    if ($userNbUserBooks >= $config->getMaximumNbUserBooksForPublic()) {
        \Sb\Flash\Flash::addItem(sprintf(__("Vous ne pouvez pas avoir plus de %s livres dans votre bibliothèque.", "s1b"), $config->getMaximumNbUserBooksForPublic()));
    } else {
        // Ajout du UserBook
        if ($book) {

            $existingUserBook = false;
            $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->getByBookIdAndUserId($user->getId(), $book->getId());
            // testing if the user already had the book but deleted it :
            // if yes, then the userbook is undeleted
            if ($userBook && $userBook->getIs_deleted()) {
                $userBook->setIs_deleted(false);
                $existingUserBook = true;
            } else {
                // Création du UserBoook
                $userBook = new \Sb\Db\Model\UserBook();
                $userBook->setCreationDate(new \DateTime);
            }

            // updating userbook data
            $userBook->setLastModificationDate(new \DateTime);

            $userBook->setUser($user);
            $userBook->setBook($book);

            // persisting book in DB
            if ($existingUserBook) {
                if (\Sb\Db\Dao\UserBookDao::getInstance()->update($userBook)) {
                    \Sb\Flash\Flash::addItem(sprintf(__("Vous aviez déjà le livre '%s' dans votre bibliothèque mais l'aviez supprimé. Il a été rajouté.", "s1b"), $book->getTitle()));
                    \Sb\Flash\Flash::addItem(__("Vous pouvez maintenant finir de remplir votre fiche de lecture.", "s1b"));
                }
            } else {
                if (\Sb\Db\Dao\UserBookDao::getInstance()->add($userBook)) {
                    \Sb\Flash\Flash::addItem(sprintf(__("Le livre '%s' a été ajouté à votre bibliothèque.", "s1b"), $book->getTitle()));
                    \Sb\Flash\Flash::addItem(__("Vous pouvez maintenant finir de remplir votre fiche de lecture.", "s1b"));
                }
            }
        }
    }

    \Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_EDIT, "ubid" => $userBook->getId()));

    //\Sb\Helpers\HTTPHelper::redirect(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_ADD));
}

//////////////////////////////////////////////////////
//function showBookDetail(\Sb\Db\Model\Book $book) {
//
//    $bookView = new \Sb\View\Book($book, true, false, true);
//    $buttonsBar = new \Sb\View\Components\ButtonsBar(false);
//
//    echo $bookView->get() . $buttonsBar->get();
//}

function showBookDetail(\Sb\Db\Model\Book $book) {

    // Préparation du template
    $tpl = new \Sb\Templates\Template("userBook");

    $tpl->set("action", "");

    $bookView = new \Sb\View\Book($book, true, false, true);
    $tpl->set("book", $bookView->get());

    $tpl->set("bookForm", "");

    $buttonsBar = new \Sb\View\Components\ButtonsBar(false);
    $tpl->set("buttonsBar", $buttonsBar->get());

    echo $tpl->output();
}