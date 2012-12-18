<?php

namespace Sb\Db\Dao;

/**
 * Description of PublisherDao
 *
 * @author Didier
 */
class PublisherDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\PublisherDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\PublisherDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Publisher");
    }

    /**
     *
     * @param string $name
     * @return \Sb\Db\Model\PublisherDao
     */
    public function getByName($name) {
        
        $query = $this->entityManager->createQuery("SELECT p FROM \Sb\Db\Model\Publisher p
            WHERE p.name = :name");
        $query->setParameters(array(
            'name' => $name)
        );
                
        $res = $query->getOneOrNullResult();
        return $res;
    }

}