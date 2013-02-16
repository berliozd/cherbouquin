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

    /**
     * Get all events of users friend with specified user
     * @param type $userId
     * @return type
     */
    public function getListUserFriendsUserEvents($userId) {

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

        return $this->getResults($query);
    }

    /**
     * Get all events a certain type
     * @param type $typeId
     * @return type
     */
    public function getListLastEventsOfType($typeId = null, $maxResult = 0) {
        
        if ($typeId != null)
            $sql = "SELECT ue FROM \Sb\Db\Model\UserEvent ue
            WHERE ue.type_id = :type_id
            ORDER BY ue.creation_date DESC";
        else
            $sql = "SELECT ue FROM \Sb\Db\Model\UserEvent ue        
            ORDER BY ue.creation_date DESC";
        
        $query = $this->entityManager->createQuery($sql);
       
        // Set type id value
        if ($typeId != null)
            $query->setParameter('type_id', $typeId);
        
        if ($maxResult > 0)
            $query->setMaxResults($maxResult);
        

        // Not cached because always used via a service who do the caching
        return $this->getResults($query);
    }

    /**
     * Get last 10 events of a certain type of users friend with specified user
     * @param type $typeId
     * @return array of UserEvent
     */
    public function getListUserFriendsUserEventsOfType($userId, $typeId) {

        $query = $this->entityManager->createQuery("SELECT ue FROM \Sb\Db\Model\UserEvent ue
            JOIN ue.user f
            JOIN f.friendships_as_target fu
            JOIN fu.user_source u
            WHERE u.id = :user_id AND fu.accepted = 1
            AND ue.type_id = :type_id
            ORDER BY ue.creation_date DESC");

        $query->setParameters(array(
            'user_id' => $userId,
            'type_id' => $typeId)
        );

        $query->setMaxResults(10);

        return $this->getResults($query);
    }

    /**
     * Get last events of a certain type for specified user
     * @param int $userId
     * @param int $typeId : if not passed or null, all user events types will be returned
     * @param int $maxResult : if not passed, all user events will be returned
     * @return Array
     */
    public function getListUserUserEventsOfType($userId, $typeId = null, $maxResult = 0) {

        if ($typeId != null)
            $sql = "SELECT ue FROM \Sb\Db\Model\UserEvent ue
            JOIN ue.user u
            WHERE u.id = :user_id
            AND ue.type_id = :type_id
            ORDER BY ue.creation_date DESC";
        else
            $sql = "SELECT ue FROM \Sb\Db\Model\UserEvent ue
            JOIN ue.user u
            WHERE u.id = :user_id            
            ORDER BY ue.creation_date DESC";

        
        $query = $this->entityManager->createQuery($sql);
        
//        \Sb\Trace\FireBugTrace::Trace($query->getSQL());
//        \Sb\Trace\FireBugTrace::Trace($userId);
//        \Sb\Trace\FireBugTrace::Trace($typeId);

        $query->setParameter('user_id', $userId);

        // Set type id value
        if ($typeId != null)
            $query->setParameter('type_id', $typeId);

        if ($maxResult > 0)
            $query->setMaxResults($maxResult);

        $rs = $this->getResults($query);
        return $rs;
    }

}