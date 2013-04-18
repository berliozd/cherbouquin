<?php

namespace Sb\Db\Dao;

/**
 * Description of GroupChronicleDao
 *
 * @author Didier
 */
class GroupChronicleDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\GroupChronicleDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\GroupChronicleDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\\Sb\\Db\\Model\\GroupChronicle");
    }

    /**
     * Get list of GroupChronicle ordered by creation date descendant. The group type the chronicles are owned by can be specified. The number of item wanted can be specified. 
     * @param string $maxResults number of item to return
     * @param int $groupType the group type the chronicle must be owned by
     * @param string $excludedGroupTypes list of group types seperate by comma to exclude
     * @return Ambigous <multitype:, \Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, string>
     */
    public function getLastChronicles($maxResults = null, $groupType = null, $excludedGroupTypes = null) {

        $dql = "SELECT gc, u, b FROM \\Sb\\Db\\Model\\GroupChronicle gc JOIN gc.user u LEFT JOIN gc.book b";

        if ($groupType || $excludedGroupTypes) {
            $dql .= " JOIN gc.group g ";
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

        $dql .= " ORDER BY gc.creation_date DESC";
        
        $query = $this->entityManager->createQuery($dql);

        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);

        return $this->getResults($query);

    }
}
