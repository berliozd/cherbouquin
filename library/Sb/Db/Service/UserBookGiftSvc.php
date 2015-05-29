<?php

namespace Sb\Db\Service;

/**
 *
 * @author Didier
 */
class UserBookGiftSvc extends AbstractService {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Service\UserBookGiftSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\UserBookGiftSvc();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(\Sb\Db\Dao\UserBookGiftDao::getInstance(), "UserBookGift");
    }

}