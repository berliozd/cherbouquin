<?php

namespace Sb\Db\Dao;

class FriendShipDao extends \Sb\Db\Dao\AbstractDao {

	const MODEL = "\\Sb\\Db\\Model\\FriendShip";
	
    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\FriendShipDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\FriendShipDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(self::MODEL);
    }

    public function add(\Sb\Db\Model\FriendShip $friendShip) {

        $this->entityManager->persist($friendShip);
        $this->entityManager->flush();
        return true;
    }

    public function getFriendsFriendShips($userId) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);

        $queryBuilder->select("ffs")->from(self::MODEL, "ffs")
                ->join("ffs.user_source", "f")
                ->join("f.friendships_as_target", "mfs")
                ->join("mfs.user_source", "me")
                ->where("me.id = :user_id")
                ->andWhere("ffs.accepted = 1")
                ->andWhere("mfs.accepted = 1")
                ->groupBy("ffs.user_target")
                ->setParameter("user_id", $userId);

        $result = $this->getResults($queryBuilder->getQuery());
        return $result;
    }

}