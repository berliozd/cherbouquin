<?php

namespace Sb\Db\Service;

use \Sb\Db\Dao\BookDao;
use \Sb\Db\Dao\UserDao;
use \Sb\Db\Model\Book;
use \Sb\Db\Model\UserBook;
use \Sb\Db\Model\Model;

/**
 * Description of BookSvc
 *
 * @author Didier
 */
class BookSvc extends Service {

    private static $instance;
    private $userUserbooksBookIds;
    private $userUserbooksBookTitles;
    private $currentViewedBook;

    /**
     *
     * @return \Sb\Db\Service\BookSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new BookSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(BookDao::getInstance(), "Book");
    }

    /**
     * Get the books a user could like
     * @param int $userId
     * @return array of Book
     */
    public function getBooksUserCouldLike($userId) {

        $key = __FUNCTION__ . "_" . $userId;

        $resultInCache = $this->getData($key);

        if ($resultInCache === false) {
            // Getting the books liked by the user
            $booksLikedByUser = BookDao::getInstance()->getListLikedByUser($userId);
            if (count($booksLikedByUser) > 0) {
                // Getting the books ids
                $bookIds = array_map(array(&$this, 'getId'), $booksLikedByUser);

                // Getting the users who also likes theses books
                $usersWhoLikeBooks = UserDao::getInstance()->getListWhoLikesBooks($bookIds);
                if (count($usersWhoLikeBooks) > 0) {
                    // Getting the ids
                    $userIds = array_map(array(&$this, 'getId'), $usersWhoLikeBooks);

                    // Getting the books liked by these users
                    $booksLikedByUsers = BookDao::getInstance()->getListLikedByUsers($userIds);
                    if (count($booksLikedByUsers) > 0) {

                        // Get the user and his userbooks
                        $user = UserDao::getInstance()->get($userId);
                        $userUserbooks = $user->getNotDeletedUserBooks();
                        $this->userUserbooksBookIds = array_map(array(&$this, 'getBookId'), $userUserbooks);
                        $this->userUserbooksBookTitles = array_map(array(&$this, 'getBookTitle'), $userUserbooks);
                        // Remove the book the user already have
                        $booksLikedByUsers = array_filter($booksLikedByUsers, array(&$this, "hasNot"));
                        $booksLikedByUsers = array_slice($booksLikedByUsers, 0, 5);
                        $result = $booksLikedByUsers;
                    } else {
                        $result = null;
                    }
                } else {
                    $result = null;
                }
            } else {
                $result = null;
            }

            $this->setData($key, $result);
        }
        return $this->getData($key);
    }

    public function getBooksCouldBeLiked($bookId) {

        $key = __FUNCTION__ . "_" . $bookId;

        $resultInCache = $this->getData($key);

        if ($resultInCache === false) {
            // Get the users who liked that book
            $usersWhoLiked = UserDao::getInstance()->getListWhoLikesBooks(array($bookId));
            if (count($usersWhoLiked) > 0) {
                // Get the ids
                $usersWhoLikedIds = array_map(array(&$this, "getId"), $usersWhoLiked);

                // Get the books these user liked
                $booksLikedByUsers = BookDao::getInstance()->getListLikedByUsers($usersWhoLikedIds);
                if (count($booksLikedByUsers) > 0) {
                    // Setting the current viewed book
                    $this->currentViewedBook = BookDao::getInstance()->get($bookId);

                    // Removing the current viewed book
                    $booksLikedByUsers = array_filter($booksLikedByUsers, array(&$this, "isNotCurrentViewedBook"));
                    $booksLikedByUsers = array_slice($booksLikedByUsers, 0, 5);
                    $result = $booksLikedByUsers;
                } else {
                    $result = null;
                }
            } else {
                $result = null;
            }
            $this->setData($key, $result);        
        }

        return $this->getData($key);
    }

    private function isNotCurrentViewedBook(Book $book) {
        return ($book->getId() != $this->currentViewedBook->getId()) && ($book->getTitle() != $this->currentViewedBook->getTitle());
    }

    /**
     * Return true if there is no book with same id in user userbooks and no book with same title in user userbooks
     * @param \Sb\Db\Model\UserBook $book
     * @return type
     */
    private function hasNot(Book $book) {
        return !in_array($book->getId(), $this->userUserbooksBookIds, true) && !in_array($book->getTitle(), $this->userUserbooksBookTitles, true);
    }

    private function getId(Model $model) {
        return $model->getId();
    }

    private function getBookId(UserBook $userbook) {
        return $userbook->getBook()->getId();
    }

    private function getBookTitle(UserBook $userbook) {
        return $userbook->getBook()->getTitle();
    }

}