<?php

namespace Sb\Db\Dao;

/**
 * Description of ChronicleDao
 * @author Didier
 */
class ChronicleDao extends \Sb\Db\Dao\AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\Chronicle";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\ChronicleDao
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\ChronicleDao();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(self::MODEL);
    }

}
