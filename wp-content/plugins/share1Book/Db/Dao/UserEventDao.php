<?php

namespace Sb\Db\Dao;

/**
 * Description of UserEventDao
 *
 * @author Didier
 */
class UserEventDao extends \Sb\Db\Dao\AbstractDao {

    
    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\UserEventDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\UserEventDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\UserEvent");
    }
    
     public function getListUserFriendsUserEvents($userId) {

        $cacheId = $this->getCacheId(__FUNCTION__, array($userId));

        $query = $this->entityManager->createQuery("SELECT ue FROM \Sb\Db\Model\UserEvent ue
            JOIN ue.user f
            JOIN f.friendships_as_target fu
            JOIN fu.user_source u
            WHERE u.id = :user_id AND fu.accepted = 1
            ORDER BY ue.creation_date DESC");

        $query->setParameters(array(
            'user_id' => $userId)
        );

        $query->setMaxResults(15);

        return $this->getResults($query, $cacheId, true);
    }
}