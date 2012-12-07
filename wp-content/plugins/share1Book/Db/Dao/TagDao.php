<?php

namespace Sb\Db\Dao;

/**
 * Description of TagDao
 *
 * @author Didier
 */
class TagDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\TagDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\TagDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Tag");
    }

    /**
     * Get tags for a specific book
     * @param type $bookId
     * @param type $cacheDuration
     * @return a list of Tag
     */
    public function getTagsForBook($bookId, $cacheDuration = null) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($bookId));

        $dql = sprintf("SELECT t FROM \Sb\Db\Model\Tag t 
            JOIN t.userbooks ub 
            JOIN ub.book b 
            WHERE b.id = %s", $bookId);
        $query = $this->entityManager->createQuery($dql);

        // Set cache duration
        if ($cacheDuration)
            $this->setCacheDuration($cacheDuration);

        $result = $this->getResults($query, $cacheId, true);

        return $result;
    }
}