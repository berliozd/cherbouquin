<?php

namespace Sb\Db\Service;

use \Sb\Trace\Trace;

/**
 * Description of UserBookSvc
 *
 * @author Didier
 */
class UserBookSvc extends \Sb\Db\Service\Service {

    private static $instance;

    const ALL_BOOKS_KEY = 'allBooks';
    const WISHED_BOOKS_KEY = 'wishedBooks';
    const BORROWED_BOOKS_KEY = 'borrowedBooks';
    const LENDED_BOOKS_KEY = 'lendedBooks';
    const MY_BOOKS_KEY = 'myBooks';

    /**
     *
     * @return \Sb\Db\Service\UserBookSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\UserBookSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(\Sb\Db\Dao\UserBookDao::getInstance(), "UserBook");
    }

    public function getUserBooks($key, $id, $cleanCache) {
        $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();
        switch ($key) {
            case self::ALL_BOOKS_KEY:
                $userBooks = $userBookDao->getListAllBooks($id, $cleanCache);
                break;
            case self::BORROWED_BOOKS_KEY:
                $userBooks = $userBookDao->getListBorrowedBooks($id, $cleanCache);
                break;
            case self::LENDED_BOOKS_KEY:
                $userBooks = $userBookDao->getListLendedBooks($id, $cleanCache);
                break;
            case self::MY_BOOKS_KEY:
                $userBooks = $userBookDao->getListMyBooks($id, $cleanCache);
                break;
            case self::WISHED_BOOKS_KEY:
                $userBooks = $userBookDao->getListWishedBooks($id, -1, $cleanCache);
                break;
            default:
                $userBooks = $userBookDao->getListMyBooks($id, $cleanCache);
                break;
        }
        return $userBooks;
    }

    public function addFromPost(\Sb\Db\Model\User $user, \Sb\Config\Model\Config $config) {

        $bookForm = new \Sb\Form\Book($_POST);

        \Sb\Trace\Trace::addItem("bookForm->getId : " . $bookForm->getId());

        // Testing if book can be found in db by id
        if ($bookForm->getId())
            $book = \Sb\Db\Dao\BookDao::getInstance()->get($bookForm->getId());
        // Testing if book can be found in db by isbn10, isbn13, asin
        if (!$book)
            $book = \Sb\Db\Dao\BookDao::getInstance()->getOneByCodes($bookForm->getISBN10(), $bookForm->getISBN13(), $bookForm->getASIN());

        // Testing if we need to add the book first
        if (!$book) {
            // Getting book from POST
            $book = new \Sb\Db\Model\Book();
            \Sb\Db\Mapping\BookMapper::map($book, $_POST, "book_");

            // Completing Book data by calling google in needed
            if (!$book->IsComplete())
                \Sb\Helpers\BookHelper::completeInfos($book);

            $book->setCreationDate(new \DateTime());
            $book->setLastModificationDate(new \DateTime());
            \Sb\Db\Dao\BookDao::getInstance()->add($book);

            // Updating the book in cache to make it available for adding a userbook on form (borrowfromfriends, etc...)
            \Sb\Cache\ZendFileCache::getInstance()->save($book, \Sb\Entity\Constants::BOOK_TO_ADD_PREFIX . session_id());
        }
        if ($book)
            return $this->addUserBook($book, $user, $config);
        else {
            \Sb\Trace\Trace::addItem("Erreur lors de l'ajout d'un livre");
            return __("Une erreur s'est produite lors de l'ajout du libre", "s1b");
        }
    }

    public function addFromBookId($id, \Sb\Db\Model\User $user, \Sb\Config\Model\Config $config) {

        $book = \Sb\Db\Dao\BookDao::getInstance()->get($id);
        return $this->addUserBook($book, $user, $config);
    }

    private function addUserBook(\Sb\Db\Model\Book $book, \Sb\Db\Model\User $user, \Sb\Config\Model\Config $config) {

        $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();
        $userBook = $userBookDao->getByBookIdAndUserId($user->getId(), $book->getId());

        // Testing if the user :
        // - doesn't already have that book or 
        // - have it but is deleted : in this case we will undelete the book
        if ($userBook && !$userBook->getIs_deleted())
            $returnMsg = __("Vous avez déjà ce livre dans votre bibliothèque.", "s1b");
        else {
            // Getting current user current nb userbooks in libary
            $userNbUserBooks = count($user->getNotDeletedUserBooks());

            if ($userNbUserBooks >= $config->getMaximumNbUserBooksForPublic())
                $returnMsg = sprintf(__("Vous ne pouvez pas avoir plus de %s livres dans votre bibliothèque.", "s1b"), $config->getMaximumNbUserBooksForPublic());
            else {
                // Ajout du UserBook

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

                // Updating userbook data
                $userBook->setLastModificationDate(new \DateTime);

                $userBook->setUser($user);
                $userBook->setBook($book);

                $bookLink = \Sb\Helpers\HTTPHelper::Link($book->getLink());
                // Persisting userbook in DB
                $addOk = false;
                if ($existingUserBook) {
                    if (\Sb\Db\Dao\UserBookDao::getInstance()->update($userBook)) {
                        $editUserBookLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_EDIT, "ubid" => $userBook->getId()));
                        $returnMsg = sprintf(__("Vous aviez déjà le livre \"%s\" dans votre bibliothèque mais l'aviez supprimé. Il a été rajouté.<br/><a class=\"link\" href=\"%s\">Remplir votre fiche de lecture</a> ou <a class=\"link\" href=\"%s\">Voir ce livre</a>", "s1b"), $book->getTitle(), $editUserBookLink, $bookLink);
                        $addOk = true;
                    }
                } else {
                    if (\Sb\Db\Dao\UserBookDao::getInstance()->add($userBook)) {
                        $editUserBookLink = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::USERBOOK_EDIT, "ubid" => $userBook->getId()));
                        $returnMsg = sprintf(__("Le livre \"%s\" a été ajouté à votre bibliothèque.<br/><a class=\"link\" href=\"%s\">Remplir votre fiche de lecture</a> ou <a class=\"link\" href=\"%s\">Voir ce livre</a>", "s1b"), $book->getTitle(), $editUserBookLink, $bookLink);
                        $addOk = true;
                    }
                }

                if ($addOk) {
                    try {
                        $userEvent = new \Sb\Db\Model\UserEvent;
                        $userEvent->setItem_id($userBook->getId());
                        $userEvent->setType_id(\Sb\Entity\EventTypes::USERBOOK_ADD);
                        $userEvent->setUser($user);
                        \Sb\Db\Dao\UserEventDao::getInstance()->add($userEvent);
                    } catch (Exception $exc) {
                        Trace::addItem("Une erreur s'est produite lors de l'ajour de l'événement suite à l'ajout d'un livre " . $exc->getMessage());
                    }
                }
            }
        }
        return $returnMsg;
    }

}