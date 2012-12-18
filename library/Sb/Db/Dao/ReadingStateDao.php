<?php

namespace Sb\Db\Dao;

/**
 * Description of ReadingStateDao
 *
 * @author Didier
 */
class ReadingStateDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\ReadingStateDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\ReadingStateDao();
        return self::$instance;
    }

    function __construct() {
        parent::__construct("\Sb\Db\Model\ReadingState");
    }

    public function getByCode($code) {

        $query = $this->entityManager->createQuery("SELECT r FROM \Sb\Db\Model\ReadingState r WHERE r.code = ?1");
        $query->setParameters(array(1 => $code));

        try {
            $result = $query->getSingleResult();
        } catch (Exception $exc) {
            $result = null;
        }

        return $result;
    }

}