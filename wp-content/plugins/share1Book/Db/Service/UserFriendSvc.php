<?php

namespace Sb\Db\Service;

/**
 * Description of UserFriend
 *
 * @author Didier
 */
class UserFriendSvc extends \Sb\Db\Service\Service {

    const LISTS_DATA_KEY = "lists";

    private static $instance;

    /**
     *
     * @param type $baseDir
     * @return \Sb\Db\Service\UserFriendSvc
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\UserFriendSvc();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(\Sb\Db\Dao\UserFriendDao::getInstance(), "UserFriend");
    }

    /**
     * Get friends for a user from cache or sql
     * @param type $userId
     * @return array \\Sb\Db\Model\UserFriend
     */
    public function getFriends($userId) {
        // TODO : réactiver le cache lorsque l'on utilisera le service de partout pour accéder/modifier la liste des amis d'un utilisateur
        \Sb\Trace\Trace::addItem("Récup de la liste des amis de $userId dans SQL.");
        $data = $this->getDao()->getList($userId);
        $allLists[$userId] = $data;
        $this->setData(self::LISTS_DATA_KEY, $allLists);
        return $data;
    }

    /**
     * Remove friends in cache for a user
     * @param type $userId
     */
    public function cleanFriends($userId) {
        $allLists = $this->getData(self::LISTS_DATA_KEY);
        if ($allLists) {
            if (array_key_exists($userId, $allLists)) {
                unset($allLists[$userId]);
                $this->setData(self::LISTS_DATA_KEY, $allLists);
            }
        }
    }

}

?>
