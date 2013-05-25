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

    public function getLastPressReviews($bookId = null, $typeId = null, $maxResults = null) {

        $dql = "SELECT pr, b, m FROM " . self::MODEL . " pr
                LEFT JOIN pr.media m
                LEFT JOIN pr.book b";
        
        if (isset($bookId) || isset($typeId))
            $dql .= " WHERE ";
        
        if (isset($bookId))
            $dql .= " b.id= " . $bookId;
        
        if (isset($typeId)) {
            if (isset($bookId))
                $dql .= " AND ";
            $dql .= " pr.type = " . $typeId;
        }
        
        $dql .= " ORDER BY pr.date DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

}
