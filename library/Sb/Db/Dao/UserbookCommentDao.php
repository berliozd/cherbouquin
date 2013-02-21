<?php

namespace Sb\Db\Dao;

/**
 * Description of UserbookCommentDao
 *
 * @author Didier
 */
class UserbookCommentDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\UserbookCommentDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\UserbookCommentDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Comment");
    }   
}