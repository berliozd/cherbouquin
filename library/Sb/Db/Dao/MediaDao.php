<?php

namespace Sb\Db\Dao;

/**
 * Description of MediaDao
 * @author Didier
 */
class MediaDao extends \Sb\Db\Dao\AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\Media";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\MediaDao
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\MediaDao();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(self::MODEL);
    }

}