<?php
namespace Sb\Db\Dao;

/** 
 * @author Didier
 * 
 */
class GroupTypeDao extends AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\GroupTypeDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new GroupTypeDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\\Sb\\Db\\Model\\GroupType");
    }

}
