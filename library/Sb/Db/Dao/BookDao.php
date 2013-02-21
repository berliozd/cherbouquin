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
    public function add(\Sb\Db\Model\Model $book) {

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

        $result = $this->getResults($queryBuilder->getQuery());
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

        return $this->getOneResult($queryBuilder->getQuery());
    }

    /**
     * return a Collection of top books, the result is not cached as this function is always called by a service which caches the result on his side
     * @return type
     */
    public function getListTops($nbMaxResults) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b, p")->from("\Sb\Db\Model\Book ", "b")
                ->distinct()
                ->join("b.publisher", "p")
                ->join("b.userbooks", "ub")
                ->orderBy("b.average_rating", "DESC")
                ->addOrderBy("ub.creation_date", "DESC")
                ->setMaxResults($nbMaxResults);

        // We don't use cache as this is called always by service which cache the result
        $result = $this->getResults($queryBuilder->getQuery(), null, false);

        return $result;
    }

    public function getListTopsForTags($tagsIds, $nbMaxResults) {

        $tagsIdsAsStr = implode(",", $tagsIds);

        $cacheId = $this->getCacheId(__FUNCTION__, array($nbMaxResults, $tagsIdsAsStr));

        $dql = sprintf("SELECT DISTINCT b,p FROM \Sb\Db\Model\Book b 
            JOIN b.userbooks ub
            JOIN b.publisher p
            JOIN ub.tags t             
            WHERE t.id IN (%s)
            ORDER BY b.average_rating DESC, ub.creation_date DESC", $tagsIdsAsStr);

        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults($nbMaxResults);

        // we use cache
        $result = $this->getResults($query, $cacheId, true); // false

        return $result;
    }

    /**
     * return a Collection of boh books
     * @return type
     */
    public function getListBOH($nbMaxResults) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b,p")->from("\Sb\Db\Model\Book ", "b")
                ->distinct()
                ->join("b.publisher", "p")
                ->join("b.userbooks", "ub")
                ->where("ub.is_blow_of_heart = 1")                
                ->orderBy("ub.last_modification_date", "DESC")
                ->setMaxResults($nbMaxResults);

        // We don't use cache as this is called always by service which cache the result
        $result = $this->getResults($queryBuilder->getQuery(), null, false);

        return $result;
    }
    
     /**
     * return a Collection of last rated books
     * @return type
     */
    public function getListLastRated($nbMaxResults) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b,p")->from("\Sb\Db\Model\Book ", "b")
                ->distinct()
                ->join("b.publisher", "p")
                ->join("b.userbooks", "ub")
                ->where("ub.rating >= 0")
                ->orderBy("ub.last_modification_date", "DESC")
                ->setMaxResults($nbMaxResults);

        // We don't use cache as this is called always by service which cache the result
        $result = $this->getResults($queryBuilder->getQuery(), null, false);

        return $result;
    }

    /**
     *
     * @param type $userId
     * return a Collection of books
     */
    public function getListBOHFriends($userId) {

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
                ->orderBy("ub.last_modification_date", "DESC")
                ->addOrderBy("b.nb_blow_of_hearts", "DESC")
                ->setParameter("user_id", $userId);

        $result = $this->getResults($queryBuilder->getQuery());

        return $result;
    }

    /**
     * Get a collection of lastly added books
     * @param type $nbMaxResults
     * @return type
     */
    public function getLastlyAdded($nbMaxResults) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b")->from("\Sb\Db\Model\Book ", "b")
                ->join("b.userbooks", "ub")
                ->setMaxResults($nbMaxResults)
                ->orderBy("ub.creation_date", "DESC")
                ->distinct(true);

        // We don't use cache as this function is always called by a service which do the caching
        $result = $this->getResults($queryBuilder->getQuery(), null, false);

        return $result;
    }

    public function getListLikedByUser($userId) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("b")->from("\Sb\Db\Model\Book ", "b")
                ->join("b.userbooks", "ub")
                ->join("ub.user", "u")
                ->where("u.id = :user_id")
                ->andWhere("(ub.rating >= 4 OR ub.is_wished = 1)")
                ->andWhere("ub.is_deleted != 1")
                ->orderBy("ub.last_modification_date", "DESC")
                ->setParameter("user_id", $userId);

        // We don't cache this result as the function is only called by the book svc which dies the caching
        $result = $this->getResults($queryBuilder->getQuery(), null);

        return $result;
    }

    public function getListLikedByUsers($userIds) {

        $userIdsAsStr = implode(", ", $userIds);

        $dql = sprintf("SELECT b,c FROM \Sb\Db\Model\Book b 
            JOIN b.userbooks ub 
            JOIN ub.user u 
            JOIN b.contributors c 
            WHERE u.id IN (%s)
            AND ub.is_deleted != 1 
            AND (ub.rating >=4 OR ub.is_wished = 1)
            ORDER BY b.average_rating DESC", $userIdsAsStr);
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(30);

        $result = $this->getResults($query, null);

        return $result;
    }

    public function getListWithTags($tagIds, $cacheDuration = null) {

        $tagIdsAsStr = implode(",", $tagIds);

        $cacheId = $this->getCacheId(__FUNCTION__, array($tagIdsAsStr));

        $dql = sprintf("SELECT b,c FROM \Sb\Db\Model\Book b 
            JOIN b.contributors c
            JOIN b.userbooks ub 
            JOIN ub.tags t             
            WHERE t.id IN (%s)
            ORDER BY ub.last_modification_date DESC", $tagIdsAsStr);
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(30);

        // Set cache duration
        if ($cacheDuration)
            $this->setCacheDuration($cacheDuration);

        $result = $this->getResults($query, $cacheId, true);

        return $result;
    }

    public function getListWithSameContributors($contributorIds, $cacheDuration = null) {

        $contributorIdsAsStr = implode(",", $contributorIds);

        $cacheId = $this->getCacheId(__FUNCTION__, array($contributorIdsAsStr));

        $dql = sprintf("SELECT b,c FROM \Sb\Db\Model\Book b 
            JOIN b.contributors c
            JOIN b.userbooks ub             
            WHERE c.id IN (%s)
            ORDER BY ub.last_modification_date DESC", $contributorIdsAsStr);
        $query = $this->entityManager->createQuery($dql);
        $query->setMaxResults(30);

        // Set cache duration
        if ($cacheDuration)
            $this->setCacheDuration($cacheDuration);

        $result = $this->getResults($query, $cacheId, true);

        return $result;
    }

}