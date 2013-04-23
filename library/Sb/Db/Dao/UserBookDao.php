<?php

namespace Sb\Db\Dao;

/**
 * Description of UserBookDao
 *
 * @author Didier
 */
class UserBookDao extends \Sb\Db\Dao\AbstractDao {

    const NOTREAD_STATE_ID = 1;
    const READING_STATE_ID = 2;
    const READ_STATE_ID = 3;
    
	const MODEL = "\\Sb\\Db\\Model\\UserBook";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\UserBookDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\UserBookDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(self::MODEL);
    }

    /**
     *
     * @param \Sb\Db\Model\UserBook $userBook
     * @return int
     */
    public function add(\Sb\Db\Model\Model $userBook) {

        $userBook->getBook()->updateAggregateFields($userBook->getRatingDiff(), $userBook->getRatingAdded(), false, $userBook->getBlowOfHeartAdded(), $userBook->getBlowOfHeartRemoved());
        $this->entityManager->persist($userBook);
        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param \Sb\Db\Model\UserBook $userBook
     * @param type $id
     * @return boolean
     */
    public function update(\Sb\Db\Model\Model $userBook) {
        $userBook->setLastModificationDate(new \DateTime());
        if ($userBook->getNeedToUpdateBook()) {
            $userBook->getBook()->updateAggregateFields($userBook->getRatingDiff(), $userBook->getRatingAdded(), false, $userBook->getBlowOfHeartAdded(), $userBook->getBlowOfHeartRemoved());
            $userBook->getBook()->setLastModificationDate(new \DateTime());
        }

//        if ($userBook->getComments()) {
//            foreach ($userBook->getComments() as $comment) {
//                $this->entityManager->persist($comment);
//            }
//        }

        $this->entityManager->persist($userBook);
        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function delete(\Sb\Db\Model\UserBook $userBook) {

        $ratingDiff = 0;
        if ($userBook->getRating())
            $ratingDiff = -$userBook->getRating();

        $blowOfHeartToRemove = false;
        if ($userBook->getIsBlowOfHeart())
            $blowOfHeartToRemove = true;

        $userBook->getBook()->updateAggregateFields($ratingDiff, false, true, false, $blowOfHeartToRemove);

        $userBook->setIs_deleted(true);

        $this->entityManager->persist($userBook->getBook());
        $this->entityManager->persist($userBook);
        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param type $userId
     * @param type $bookId
     * @return type
     */
    public function getByBookIdAndUserId($userId, $bookId) {

        /* $query = $this->entityManager->createQuery("SELECT ub FROM \Sb\Db\Model\UserBook ub
          JOIN ub.user u
          JOIN ub.book b
          WHERE u.id = :user_id AND b.id = :book_id AND ub.is_deleted = 0"); */
        $query = $this->entityManager->createQuery("SELECT ub FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            WHERE u.id = :user_id AND b.id = :book_id");
        $query->setParameters(array(
            'user_id' => $userId,
            'book_id' => $bookId)
        );
        return $query->getOneOrNullResult();
    }

    public function getBookInFriendsUserBook($bookId, $userId) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("ub")->from(self::MODEL, "ub")
                ->join("ub.user", "u")
                ->join("ub.book", "b")
                ->join("u.friendships_as_target", "fs")
                ->join("fs.user_source", "me")
                ->where("me.id = :user_id")
                ->andWhere("b.id = :book_id")
                ->andWhere("ub.is_deleted = 0")
                ->setParameter("user_id", $userId)
                ->setParameter("book_id", $bookId);

        $result = $this->getResults($queryBuilder->getQuery());

        return $result;
    }

    public function getListMyBooks($userId, $useCache) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($userId));

        $query = $this->entityManager->createQuery("SELECT ub, u, b, r, p, l, c FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            LEFT JOIN ub.reading_state r
            LEFT JOIN b.publisher p
            LEFT JOIN b.contributors c
            LEFT JOIN ub.lendings l
            WHERE u.id = :id AND ub.is_owned = 1 AND ub.is_deleted = 0
            ORDER BY ub.id DESC");
        $query->setParameters(array('id' => $userId));

        $result = $this->getResults($query, $cacheId, $useCache);

        return $result;
    }

    public function getListAllBooks($userId, $useCache) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($userId));

        $query = $this->entityManager->createQuery("SELECT ub, u, b, r, p, l, c FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            LEFT JOIN ub.reading_state r
            LEFT JOIN b.publisher p
            LEFT JOIN b.contributors c
            LEFT JOIN ub.lendings l
            WHERE u.id = :id AND ub.is_deleted = 0
            ORDER BY ub.id DESC");

        $query->setParameters(array('id' => $userId));

        $result = $this->getResults($query, $cacheId, $useCache);

        return $result;
    }

    public function getListWishedBooks($userId, $limit, $useCache) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($userId));

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("ub, u, b, r, p, c")->from(self::MODEL, "ub")
                ->join("ub.user", "u")
                ->join("ub.book", "b")
                ->leftJoin("ub.reading_state", "r")
                ->leftJoin("b.publisher", "p")
                ->leftJoin("b.contributors", "c")
                ->orderBy("ub.id", "DESC")
                ->where("u.id = :id")
                ->andWhere("ub.is_deleted = 0")
                ->andWhere("ub.is_wished = 1")
                ->setParameter("id", $userId);

        if ($limit != -1) {
            $queryBuilder->setMaxResults($limit);
        }

        $result = $this->getResults($queryBuilder->getQuery(), $cacheId, $useCache);

        return $result;
    }

    public function getListBorrowedBooks($userId, $useCache) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($userId));

        $query = $this->entityManager->createQuery("SELECT ub, u, b, r, p, bo, c FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            LEFT JOIN ub.reading_state r
            LEFT JOIN b.publisher p
            LEFT JOIN b.contributors c
            JOIN ub.borrowings bo
            WHERE u.id = :id AND bo.state != 0 AND ub.is_deleted = 0
            ORDER BY ub.id DESC");

        $query->setParameters(array('id' => $userId));

        $result = $this->getResults($query, $cacheId, $useCache);

        return $result;
    }

    public function getListLendedBooks($userId, $useCache) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($userId));

        $query = $this->entityManager->createQuery("SELECT ub, u, b, r, p, l, c FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            LEFT JOIN ub.reading_state r
            LEFT JOIN b.publisher p
            LEFT JOIN b.contributors c
            JOIN ub.lendings l
            WHERE u.id = :id AND l.state != 0 AND ub.is_deleted = 0
            ORDER BY ub.id DESC");

        $query->setParameters(array('id' => $userId));

        $result = $this->getResults($query, $cacheId, $useCache);

        return $result;
    }

    public function getReadingNow($userId) {

        $result = $this->getCurrentlyReadingsNow($userId);
        if ($result)
            return $result[0];
    }

    public function getCurrentlyReadingsNow($userId) {

        $query = $this->entityManager->createQuery("SELECT ub FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            JOIN ub.reading_state r
            WHERE u.id = :user_id AND r.id = 2 AND ub.is_deleted = 0
            ORDER BY ub.last_modification_date DESC"); //2 = READING
        $query->setParameters(array(
            'user_id' => $userId)
        );

        $result = $this->getResults($query);

        return $result;
    }

    public function getListLastlyRead($userId) {

        $query = $this->entityManager->createQuery("SELECT ub FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            JOIN ub.reading_state r
            WHERE u.id = :user_id AND r.id = 3 AND ub.is_deleted = 0
            ORDER BY ub.reading_date DESC"); // 3 = LU

        $query->setParameters(array(
            'user_id' => $userId)
        );

        $query->setMaxResults(10);

        return $this->getResults($query);
    }

    public function getListUserBOH($userId) {

        $query = $this->entityManager->createQuery("SELECT ub FROM " . self::MODEL . " ub
            JOIN ub.user u
            JOIN ub.book b
            JOIN ub.reading_state r
            WHERE u.id = :user_id AND ub.is_blow_of_heart = 1 AND ub.is_deleted = 0
            ORDER BY ub.last_modification_date DESC");

        $query->setParameters(array(
            'user_id' => $userId)
        );

        $query->setMaxResults(5);

        return $this->getResults($query);
    }

    public function getLastlyReadUserbookByBookId($bookId, $maxResult) {
        
        $query = $this->entityManager->createQuery("SELECT ub, u FROM " . self::MODEL . " ub        
            JOIN ub.book b
            JOIN ub.user u
            JOIN ub.reading_state r
            WHERE 
                b.id = :book_id AND 
                r.id = 3
            ORDER BY ub.last_modification_date DESC");

        $query->setParameters(array(
            'book_id' => $bookId)
        );

        $query->setMaxResults($maxResult);

        return $this->getResults($query);
    }
}