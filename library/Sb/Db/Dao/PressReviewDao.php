<?php

namespace Sb\Db\Dao;

/**
 *
 * @author Didier
 */
class PressReviewDao extends AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\PressReview";

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

        parent::__construct(self::MODEL);
    }

}
