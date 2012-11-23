<?php

namespace Sb\Db\Dao;

/**
 * Description of UserBookGiftDao
 *
 * @author Didier
 */
class UserBookGiftDao extends \Sb\Db\Dao\AbstractDao {


    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\UserBookGiftDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\UserBookGiftDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\UserBookGift");
    }
}