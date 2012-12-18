<?php

namespace Sb\Db\Dao;

/**
 * Description of UserSettingDao
 *
 * @author Didier
 */
class UserSettingDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\UserSettingDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\UserSettingDao ();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\UserSetting");
    }

    public function add(\Sb\Db\Model\UserSetting $userSetting) {

        $this->entityManager->persist($userSetting);
        $this->entityManager->flush();
        return $userSetting;
    }

    public function update(\Sb\Db\Model\UserSetting $userSetting) {

        $this->entityManager->persist($userSetting);
        $this->entityManager->flush();
        return $userSetting;
    }
    
}