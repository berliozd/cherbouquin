<?php

namespace Sb\Db\Dao;

/**
 * Description of MessageDao
 *
 * @author Didier
 */
class MessageDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\MessageDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\MessageDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Message");
    }

    /**
     *
     * @param \Sb\Db\Model\Message $message
     * @return int
     */
    public function add(\Sb\Db\Model\Message $message) {

        $this->entityManager->persist($message);
        $this->entityManager->flush();
        return $message;
    }
    

}