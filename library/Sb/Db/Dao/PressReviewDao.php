<?php
namespace Sb\Db\Dao;

/** 
 * @author Didier
 * 
 */
class PressReviewDao extends AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\PressReviewDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new PressReviewDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\\Sb\\Db\\Model\\PressReview");
    }
}
