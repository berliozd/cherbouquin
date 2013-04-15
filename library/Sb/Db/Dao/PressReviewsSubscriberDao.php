<?php
namespace Sb\Db\Dao;

/** 
 * @author Didier
 * 
 */
class PressReviewsSubscriberDao extends AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\PressReviewsSubscriberDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new PressReviewsSubscriberDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\\Sb\\Db\\Model\\PressReviewsSubscriber");
    }
}
