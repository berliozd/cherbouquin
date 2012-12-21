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
        parent::__construct("\Sb\Db\Model\GroupChronicle");
    }
    
    public function getLast() {
        
        $query = $this->entityManager->createQuery("SELECT gc FROM \Sb\Db\Model\GroupChronicle gc ORDER BY gc.creation_date DESC");        
        $query->setMaxResults(1);
        return $this->getOneResult($query, null, false);
        
    }
}