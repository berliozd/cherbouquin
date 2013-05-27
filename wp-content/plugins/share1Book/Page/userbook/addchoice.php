<?php

use Sb\Entity\Constants;
use Sb\Entity\LibraryPages;
use Sb\Entity\Urls;
use Sb\Db\Dao\BookDao;
use Sb\Db\Dao\UserBookDao;
use Sb\Db\Mapping\BookMapper;
use Sb\Flash\Flash;
use Sb\Db\Model\UserBook;
use Sb\Db\Model\Book;
use Sb\View\Book as BookView;
use Sb\Form\Book as BookForm;
use Sb\Helpers\HTTPHelper;
use Sb\Templates\Template;
use Sb\View\Components\ButtonsBar;
use Sb\Cache\ZendFileCache;
use Sb\Service\BookPageSvc;

Sb\Trace\Trace::addItem(LibraryPages::USERBOOK_ADDCHOICE);

global $s1b;
$context = $s1b->getContext();
$config = $s1b->getConfig();

if ($context->getIsShowingFriendLibrary()) {
    Throw new Sb\Exception\UserException(__("Vous ne pouvez pas ajouter un livre à la bibliothèque d'un ami.", "s1b"));
}

if (!$s1b->getIsSubmit()) {
    // Récupération du Book depuis le cache
    $book = ZendFileCache::getInstance()->load(Constants::BOOK_TO_ADD_PREFIX . session_id());
    
    // If id is known, getting the book from db to have all associated members and userbooks to show the potential reviews
    $booksAlsoLiked = null;
    $bookWithSameTags = null;
    $reviewdUserBooks = null;
    if ($book->getId()) {
        $book = BookDao::getInstance()->get($book->getId());
        $bookPage = BookPageSvc::getInstance()->get($book->getId());
        $booksAlsoLiked = $bookPage->getBooksAlsoLiked();
        $bookWithSameTags = $bookPage->getBooksWithSameTags();
        $reviewdUserBooks = $bookPage->getReviewedUserBooks();
    }   
    
    showBookDetail($book, $booksAlsoLiked, $bookWithSameTags, $reviewdUserBooks);
} else {

    $bookForm = new BookForm($_POST);

    // testing if book can be found in db by id
    if ($bookForm->getId())
        $book = BookDao::getInstance()->get($bookForm->getId());

    // testing if book can be found in db by isbn10, isbn13, asin
    if (!$book)
        $book = BookDao::getInstance()->getOneByCodes($bookForm->getISBN10(), $bookForm->getISBN13(), $bookForm->getASIN());

    // getting the book data from post and adding to db
    if (!$book) {
        // Récupération du Book depuis le POST
        $book = new Book();
        //var_dump($_POST);
        BookMapper::map($book, $_POST, "book_");
        // book not in db : need to add it
        $book->setCreationDate(new \DateTime());
        $book->setLastModificationDate(new \DateTime());
        BookDao::getInstance()->add($book);
    }

    // Getting current user current nb userbooks in libary
    $user = $context->getConnectedUser();
    $userNbUserBooks = count($user->getNotDeletedUserBooks());

    if ($userNbUserBooks >= $config->getMaximumNbUserBooksForPublic()) {
        Flash::addItem(sprintf(__("Vous ne pouvez pas avoir plus de %s livres dans votre bibliothèque.", "s1b"), $config->getMaximumNbUserBooksForPublic()));
    } else {
        // Ajout du UserBook
        if ($book) {

            $existingUserBook = false;
            $userBook = UserBookDao::getInstance()->getByBookIdAndUserId($user->getId(), $book->getId());
            // testing if the user already had the book but deleted it :
            // if yes, then the userbook is undeleted
            if ($userBook && $userBook->getIs_deleted()) {
                $userBook->setIs_deleted(false);
                $existingUserBook = true;
            } else {
                // Création du UserBoook
                $userBook = new UserBook();
                $userBook->setCreationDate(new \DateTime);
            }

            // updating userbook data
            $userBook->setLastModificationDate(new \DateTime);

            $userBook->setUser($user);
            $userBook->setBook($book);

            // persisting book in DB
            if ($existingUserBook) {
                if (UserBookDao::getInstance()->update($userBook)) {
                    Flash::addItem(sprintf(__("Vous aviez déjà le livre '%s' dans votre bibliothèque mais l'aviez supprimé. Il a été rajouté.", "s1b"), $book->getTitle()));
                    Flash::addItem(__("Vous pouvez maintenant finir de remplir votre fiche de lecture.", "s1b"));
                }
            } else {
                if (UserBookDao::getInstance()->add($userBook)) {
                    Flash::addItem(sprintf(__("Le livre '%s' a été ajouté à votre bibliothèque.", "s1b"), $book->getTitle()));
                    Flash::addItem(__("Vous pouvez maintenant finir de remplir votre fiche de lecture.", "s1b"));
                }
            }
        }
    }

    HTTPHelper::redirect(Urls::USER_LIBRARY_DETAIL, array("page" => LibraryPages::USERBOOK_EDIT, "ubid" => $userBook->getId()));
}

function showBookDetail(Book $book, $booksAlsoLiked, $booksWithSameTags, $reviewdUserBooks) {

    // Préparation du template
    $tpl = new Template("userBook");

    $tpl->set("action", "");

    $bookView = new BookView($book, true, true, true, $booksAlsoLiked, $booksWithSameTags, $reviewdUserBooks);
    $tpl->set("book", $bookView->get());

    $tpl->set("bookForm", "");

    $buttonsBar = new ButtonsBar(false);
    $tpl->set("buttonsBar", $buttonsBar->get());

    echo $tpl->output();
}