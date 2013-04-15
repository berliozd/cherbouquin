<?php
namespace Sb\Db\Dao;

/** 
 * @author Didier
 * 
 */
class GroupDao extends AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\GroupDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new GroupDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\\Sb\\Db\\Model\\Group");
    }
}
