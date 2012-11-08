<?php

namespace Sb\Db\Dao;

/**
 * Description of BookDao
 *
 * @author Didier
 */
class BookDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\BookDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\BookDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Book");
    }

    /**
     *
     * @param \Sb\Db\Model\Book $book
     */
    public function add(\Sb\Db\Model\Book $book) {

        if ($book->getContributors()) {
            foreach ($book->getContributors() as $contributor) {
                $this->entityManager->persist($contributor);
            }
        }

        if ($book->getPublisher())
            $this->entityManager->persist($book->getPublisher());

        $this->entityManager->persist($book);

        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param type $keyword
     */
    public function getListByKeyword($keyword) {

        $reference = $keyword;

        $cacheId = $this->getCacheId(__FUNCTION__, array($keyword));

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b, allcontributors, p")->from("\Sb\Db\Model\Book ", "b")
                ->leftJoin("b.contributors", "c")->where("c.full_name LIKE :keyword")
                ->leftJoin("b.contributors", "allcontributors")
                ->leftJoin("b.publisher", "p")
                ->orWhere("b.title LIKE :keyword")
                ->orWhere("b.isbn10 = :reference")
                ->orWhere("b.isbn13 = :reference")
                ->orWhere("b.asin = :reference")
                ->setParameter("keyword", "%" . $keyword . "%")
                ->setParameter("reference", $reference);

        $result = $this->getResults($queryBuilder->getQuery(), $cacheId, true);
        return $result;
    }

    /**
     *
     * @param type $isbn10
     * @param type $isbn13
     * @param type $asin
     * @return type
     */
    public function getOneByCodes($isbn10, $isbn13, $asin) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($isbn10, $isbn13, $asin));

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b, c, p")->from("\Sb\Db\Model\Book ", "b")
                ->leftJoin("b.contributors", "c")->where("c.full_name LIKE :keyword")
                ->leftJoin("b.publisher", "p")
                ->Where("b.isbn10 = :isbn10")
                ->andWhere("b.isbn13 = :isbn13")
                ->andWhere("b.asin = :asin")
                ->setParameter("isbn10", $isbn10)
                ->setParameter("isbn13", $isbn13)
                ->setParameter("asin", $asin);

        return $this->getOneResult($queryBuilder->getQuery(), $cacheId, true);
    }

    /**
     * return a Collection of books
     * @return type
     */
    public function getListTops() {

        $cacheId = $this->getCacheId(__FUNCTION__, array(""));

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b, p")->from("\Sb\Db\Model\Book ", "b")
                ->distinct()
                ->join("b.publisher", "p")
                ->join("b.userbooks", "ub")
                ->orderBy("b.average_rating", "DESC")
                ->addOrderBy("ub.creation_date", "DESC")
                ->setMaxResults(10);

        // we don't clean cache
        $result = $this->getResults($queryBuilder->getQuery(), $cacheId, false); // false

        return $result;
    }

    /**
     * return a Collection of books
     * @return type
     */
    public function getListBOH() {

        $cacheId = $this->getCacheId(__FUNCTION__, array(""));

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b")->from("\Sb\Db\Model\Book ", "b")
                ->distinct()
                ->join("b.userbooks", "ub")
                ->where("ub.is_blow_of_heart = 1")
                ->setMaxResults(10)
                ->orderBy("ub.last_modification_date", "DESC");

        $result = $this->getResults($queryBuilder->getQuery(), $cacheId, false);

        return $result;
    }

    /**
     *
     * @param type $userId
     * return a Collection of books
     */
    public function getListBOHFriends($userId) {

        $cacheId = $this->getCacheId(__FUNCTION__, array(""));

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b")->from("\Sb\Db\Model\Book ", "b")
                ->distinct()
                ->join("b.userbooks", "ub")
                ->join("ub.user", "u")
                ->join("u.friendships_as_target", "f")
                ->join("f.user_source", "me")
                ->where("me.id = :user_id")
                ->andWhere("ub.is_blow_of_heart = 1")
                ->andWhere("f.accepted = 1")
                ->setMaxResults(5)
                ->orderBy("b.nb_blow_of_hearts", "DESC")
                ->addOrderBy("ub.last_modification_date", "DESC")
                ->setParameter("user_id", $userId);

        $result = $this->getResults($queryBuilder->getQuery(), $cacheId, true);

        return $result;
    }

    public function getLastlyAddedBooks() {

        $cacheId = $this->getCacheId(__FUNCTION__, array(""));

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b")->from("\Sb\Db\Model\Book ", "b")
                ->join("b.userbooks", "ub")
                ->setMaxResults(5)
                ->orderBy("ub.creation_date", "DESC")
                ->distinct(true);

        $result = $this->getResults($queryBuilder->getQuery(), $cacheId, false);

        return $result;
    }

}