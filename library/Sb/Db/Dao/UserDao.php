<?php

namespace Sb\Db\Dao;

/**
 * Description of UserDao
 *
 * @author Didier
 */
class UserDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\UserDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\UserDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\User");
    }

    /**
     *
     * @param type $user
     * @return type
     */
    public function add(\Sb\Db\Model\Model $user) {
        $this->entityManager->persist($user);
        if ($user->getSetting())
            $this->entityManager->persist($user->getSetting());
        $this->entityManager->flush();
        return $user;
    }

    /**
     *
     * @param \Sb\Db\Model\User $user
     * @param type $id
     * @return boolean
     */
    public function update(\Sb\Db\Model\Model $user) {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     *
     * @param type $email
     * @return \Sb\Db\Model\User
     */
    public function getByEmail($email) {

        $query = $this->entityManager->createQuery("SELECT u FROM \Sb\Db\Model\User u
            WHERE u.email = :email");
        $query->setParameters(array(
            'email' => $email)
        );
        return $query->getOneOrNullResult();
    }

    /**
     *
     * @param type $userName
     * @return \Sb\Db\Model\User
     */
    public function getByUserName($userName) {

        $query = $this->entityManager->createQuery("SELECT u FROM \Sb\Db\Model\User u
            WHERE u.user_name = :user_name");
        $query->setParameters(array(
            'user_name' => $userName)
        );
        return $query->getOneOrNullResult();
    }

    public function getS1bUser($email, $password) {

        $query = $this->entityManager->createQuery("SELECT u FROM \Sb\Db\Model\User u
            WHERE u.email = :email AND u.password = :password");
        $query->setParameters(array(
            'email' => $email,
            'password' => $password)
        );
        return $query->getOneOrNullResult();
    }

    public function getFacebookUser($email) {

        $query = $this->entityManager->createQuery("SELECT u FROM \Sb\Db\Model\User u
            WHERE u.email = :email");
        $query->setParameters(array(
            'email' => $email)
        );
        return $query->getOneOrNullResult();
    }

    public function getListByKeyword($keyword) {

        $queryBuilder = new \Doctrine\ORM\QueryBuilder($this->entityManager);
        $queryBuilder->select("u")->from("\Sb\Db\Model\User", "u")
                ->Where("u.email LIKE :keyword")
                ->orWhere("u.user_name LIKE :keyword")
                ->orWhere("u.last_name LIKE :keyword")
                ->orWhere("u.first_name LIKE :keyword")
                ->setParameter("keyword", "%" . $keyword . "%");

        $result = $this->getResults($queryBuilder->getQuery());
        return $result;
    }

    public function getListWhoLikesBooks($bookIds) {

        $bookIdsAsStr = implode(",", $bookIds);

        $dql = sprintf("SELECT u FROM \Sb\Db\Model\User u 
            JOIN u.userbooks ub 
            JOIN ub.book b 
            WHERE b.id IN (%s)
            AND ub.is_deleted != 1 
            AND (ub.rating >=4 OR ub.is_wished = 1)
            ORDER BY ub.last_modification_date DESC", $bookIdsAsStr);

        $query = $this->entityManager->createQuery($dql);

        // We don't use cahe as this function is always called by book svc which does the caching
        $result = $this->getResults($query, null);

        return $result;
    }

}