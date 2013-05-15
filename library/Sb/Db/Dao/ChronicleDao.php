<?php

namespace Sb\Db\Dao;

/**
 * Description of ChronicleDao
 * @author Didier
 */
class ChronicleDao extends \Sb\Db\Dao\AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\Chronicle";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\ChronicleDao
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\ChronicleDao();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(self::MODEL);
    }

    /**
     * Get list of Chronicle ordered by creation date descendant.
     * The group type the chronicles are owned by can be specified. The number of item wanted can be specified.
     * @param string $maxResults number of item to return
     * @param int $groupType the group type the chronicle must be owned by
     * @param string $excludedGroupTypes list of group types seperate by comma to exclude
     * @param array orderBy an array , first item is colmun and second is the order (ASC or DESC)
     * @return Ambigous <multitype:, \Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, string>
     */
    public function getLastChronicles($maxResults = null, $groupType = null, $excludedGroupTypes = null, $searchTerm = null, $orderBy = null) {

        $dql = "SELECT gc, u, b, t FROM " . self::MODEL . " gc 
        		LEFT JOIN gc.tag t 
        		JOIN gc.user u 
        		LEFT JOIN gc.book b ";
        
        if ($groupType || $excludedGroupTypes) {
            $dql .= "JOIN gc.group g ";
            if ($groupType)
                $dql .= "JOIN g.type gt_included ";
            if ($excludedGroupTypes)
                $dql .= "JOIN g.type gt_excluded ";
            $dql .= "WHERE ";
            if ($groupType)
                $dql .= "gt_included.id = $groupType ";
            if ($excludedGroupTypes) {
                if ($groupType)
                    $dql .= "AND ";
                $dql .= "gt_excluded.id NOT IN ($excludedGroupTypes) ";
            }
        }
        
        if ($searchTerm) {
            if ($groupType || $excludedGroupTypes) {
                $dql .= "AND ";
            } else {
                $dql .= "WHERE ";
            }
            $dql .= "(gc.keywords LIKE '%" . $searchTerm . "%' OR gc.title LIKE '%" . $searchTerm . "%' OR gc.text LIKE '%" . $searchTerm . "%')";
        }
        
        if ($orderBy)
            $dql .= " ORDER BY gc." . $orderBy[0] . " " . $orderBy[1];
        else
            $dql .= " ORDER BY gc.creation_date DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

    /**
     * Get a collection of chronicles with a specific tag
     * @param int $tagId the tag to search
     * @param int $maxResults number of chronicles to get
     * @return Collection of Chronicle
     */
    public function getChroniclesWithTag($tagId, $maxResults = null) {

        $dql = "SELECT gc FROM " . self::MODEL . " gc";
        $dql .= " JOIN gc.tag t WHERE t.id = " . $tagId;
        $dql .= " ORDER BY gc.creation_date DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

    /**
     * Get a collection of chronicle with at least one keywords in common
     * @param Array of String $keywords
     * @param int $maxResults
     * @return Collection of chronicle with similar keywords
     */
    public function getChroniclesWithKeywords($keywords, $maxResults = null) {

        $dql = "SELECT gc FROM " . self::MODEL . " gc";
        $dql .= " WHERE " . $this->getLikeKeywordsQuery($keywords);
        $dql .= " ORDER BY gc.creation_date DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

    /**
     * Get collection of chronicle of a certain author (user) ordered by number of views descending
     * @param int $authorId
     * @param int $maxResults
     * @return Collection of Chronicle
     */
    public function getChroniclesOfAuthor($authorId, $maxResults = null) {

        $dql = "SELECT gc FROM " . self::MODEL . " gc";
        $dql .= " JOIN gc.user u";
        $dql .= " WHERE u.id = " . $authorId;
        $dql .= " ORDER BY gc.nb_views DESC";
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

    /**
     * Get the "like" query part for a list of keywords
     * @param array of String $keywords
     * @return string the "like" query
     */
    private function getLikeKeywordsQuery($keywords) {

        $likeKeywords = array();
        
        foreach ($keywords as $keyword)
            $likeKeywords[] = " gc.keywords LIKE '%" . $keyword . "%' ";
        
        $result = implode(" OR ", $likeKeywords);
        
        return $result;
    }

}
