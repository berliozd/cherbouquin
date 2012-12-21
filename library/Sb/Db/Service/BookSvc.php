<?php

namespace Sb\Db\Service;

use \Sb\Db\Dao\BookDao;
use \Sb\Db\Dao\UserDao;
use \Sb\Db\Dao\TagDao;
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
     * Get the books a user could like, the list is cached for 1 day
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

    /**
     * Get a list of books that also liked by the people who like the book passed, the list is cached for 1 day
     * @param type $bookId
     * @return a array of Book
     */
    public function getBooksAlsoLiked($bookId) {

        $key = __FUNCTION__ . "_" . $bookId;

        $resultInCache = $this->getData($key);

        if ($resultInCache === false) {

            $result = null;

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
                    $result = array_filter($booksLikedByUsers, array(&$this, "isNotCurrentViewedBook"));
                }
            }

            $this->setData($key, $result);
        }

        return $this->getRandomNumber($this->getData($key), 5);
    }

    /**
     * Get the books with the same tag as the book passed, the list is cached for 1 day
     * @param type $bookId
     * @return a array of Book
     */
    public function getBooksWithSameTags($bookId) {

        $key = __FUNCTION__ . "_" . $bookId;

        $resultInCache = $this->getData($key);

        if ($resultInCache === false) {

            $result = null;

            // Get the tags for the current book
            $tags = TagDao::getInstance()->getTagsForBook($bookId);
            if (count($tags) > 0) {
                $tagsId = array_map(array(&$this, "getId"), $tags);

                // Get the book with these tags
                $booksWithTags = BookDao::getInstance()->getListWithTags($tagsId);
                if (count($booksWithTags) > 0) {
                    // Setting the current viewed book
                    $this->currentViewedBook = BookDao::getInstance()->get($bookId);
                    // Removing the current viewed book
                    $result = array_filter($booksWithTags, array(&$this, "isNotCurrentViewedBook"));
                }
            }

            $this->setData($key, $result);
        }

        return $this->getRandomNumber($this->getData($key), 5);
    }

    public function getBooksWithSameContributors($bookId) {

        $key = __FUNCTION__ . "_" . $bookId;

        $resultInCache = $this->getData($key);

        if ($resultInCache === false) {

            $result = null;

            // Get the book
            $book = BookDao::getInstance()->get($bookId);

            // Get the book contributors
            $contributors = $book->getContributors();
            if (count($contributors) > 0) {
                $contributorsIds = array_map(array(&$this, "getId"), $contributors->toArray());
                $booksWithSameContributors = BookDao::getInstance()->getListWithSameContributors($contributorsIds);

                if (count($booksWithSameContributors) > 0) {
                    // Setting the current viewed book
                    $this->currentViewedBook = $book;
                    // Removing the current viewed book
                    $result = array_filter($booksWithSameContributors, array(&$this, "isNotCurrentViewedBook"));
                }
            }

            $this->setData($key, $result);
        }

        return $this->getRandomNumber($this->getData($key), 5);
    }

    /**
     * Get 25 top books order by average rating and creation date for tops page linked in footer
     * @return type
     */
    public function getTopsPageTops() {
        return $this->getTops(25);
    }

    /**
     * Get 5 top books order by average rating and creation date visible in footer
     * @return type
     */
    public function getTopsFooter() {
        return $this->getTops(5);
    }

    /**
     * Get 10 top books order by average rating and creation date visible on user homepage
     * @return type
     */
    public function getTopsUserHomePage() {
        return $this->getTops(10);
    }

    private function getTops($nbBooks) {
        try {
            
            $nbBooksMax = 25; // Number of books in the list cached. Items are alays taken from that list. 
            //This value will have to be changed if a bigger list needs to be return.
            
            $dataKey = __FUNCTION__ . "_" . $nbBooksMax;
            $result = $this->getData($dataKey);
            if ($result === false) {
                $result = BookDao::getInstance()->getListTops($nbBooksMax);
                $this->setData($dataKey, $result);
            }

            return array_slice($result, 0, $nbBooks);
            
        } catch (\Exception $exc) {
            $this->logException("BookSvc", __FUNCTION__, $exc);
        }
    }

    /**
     * Get 25 boh books order by last modification date for boh page
     * @return type
     */
    public function getBOHPageBOH() {
        return $this->getBOH(25);
    }

    /**
     * Get 5 boh books order by last modification date for Footer
     * @return type
     */
    public function getBOHForFooter() {
        return $this->getBOH(5);
    }

    /**
     * Get 10 boh books order by last modification date for user homepage
     * @return type
     */
    public function getBOHForUserHomePage() {
        return $this->getBOH(10);
    }

    /**
     * Get 10 boh books order by last modification date for homepage
     * @return type
     */
    public function getBOHForHomePage() {
        return $this->getBOH(5);
    }

    private function getBOH($nbBooks) {        
        try {
            
            $nbBooksMax = 25; // Number of books in the list cached. Items are alays taken from that list.
            //This value will have to be changed if a bigger list needs to be return.
            $dataKey = __FUNCTION__ . "_" . $nbBooksMax;
            $result = $this->getData($dataKey);
            if ($result === false) {
                $result = BookDao::getInstance()->getListBOH($nbBooksMax);
                $this->setData($dataKey, $result);
            }

            return array_slice($result, 0, $nbBooks);
            
        } catch (\Exception $exc) {
            $this->logException("BookSvc", __FUNCTION__, $exc);
        }
    }

    /**
     * Get 25 lastly added books order by creation date for specific page
     * @return type
     */
    public function getLastlyAddedForPage() {
        return $this->getLastlyAdded(25);
    }
    
    /**
     * Get 25 lastly added books order by creation date for footer
     * @return type
     */
    public function getLastlyAddedForFooter() {
        return $this->getLastlyAdded(5);
    }

    private function getLastlyAdded($nbBooks) {
        try {
            
            $nbBooksMax = 25; // Number of books in the list cached. Items are always taken from that list.
            //This value will have to be changed if a bigger list needs to be return.
            $dataKey = __FUNCTION__ ."_" . $nbBooksMax;
            $result = $this->getData($dataKey);
            if ($result === false) {
                $result = BookDao::getInstance()->getLastlyAdded($nbBooksMax);
                $this->setData($dataKey, $result);
            }

            return array_slice($result, 0, $nbBooks);
            
        } catch (\Exception $exc) {
            $this->logException("BookSvc", __FUNCTION__, $exc);
        }
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

    private function getRandomNumber($books, $number) {
        if ($books && count($books) > 0) {
            if (shuffle($books))
                return array_slice($books, 0, $number);
        }
        return null;
    }

}