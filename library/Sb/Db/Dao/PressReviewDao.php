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

    public function getLastVideoPressReviewForBookId($bookId) {

        $dql = "SELECT pr, b FROM " . self::MODEL . " pr
                JOIN pr.book b
        		WHERE pr.type = 1 AND b.id = " . $bookId . "
                ORDER BY pr.date DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        $query->setMaxResults(1);
        
        return $this->getOneResult($query);
    }

}
