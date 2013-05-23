<?php

namespace Sb\Db\Dao;

/**
 *
 * @author Didier
 */
class PressReviewDao extends AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\PressReview";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\PressReviewDao
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new PressReviewDao();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(self::MODEL);
    }

    public function getLastPressReviews($maxResults = null, $typeId = null, $orderBy = null) {

        $dql = "SELECT pr, m FROM " . self::MODEL . " pr
        		LEFT JOIN pr.media m";
        
        if (isset($typeId))
            $dql .= " WHERE pr.type = " . $typeId;
        
        if ($orderBy)
            $dql .= " ORDER BY pr." . $orderBy[0] . " " . $orderBy[1];
        else
            $dql .= " ORDER BY pr.date DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

    public function getLastPressReviewsForBookId($bookId, $typeId = null, $maxResults = null) {

        $dql = "SELECT pr, b, m FROM " . self::MODEL . " pr
                JOIN pr.book b
                LEFT JOIN pr.media m
        		WHERE b.id = " . $bookId;
        
        if (isset($typeId))
            $dql .= " AND pr.type = " . $typeId;
        
        $dql .= " ORDER BY pr.date DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

}
