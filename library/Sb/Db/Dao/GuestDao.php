<?php

namespace Sb\Db\Dao;

/**
 * Description of GuestDao
 *
 * @author Didier
 */
class GuestDao extends \Sb\Db\Dao\AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\Guest";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\GuestDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\GuestDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(self::MODEL);
    }

    /**
     * 
     * @param \Sb\Db\Model\Guest $guest
     * @return boolean
     */
    public function add(\Sb\Db\Model\Guest $guest) {

        $this->entityManager->persist($guest);

        if ($guest->getInvitations()) {
            foreach ($guest->getInvitations() as $invitationToAdd) {
                $this->entityManager->persist($invitationToAdd);
            }
        }

        $this->entityManager->flush();

        return true;
    }

    public function getListByEmail($email) {

        $query = $this->entityManager->createQuery("SELECT g FROM " . self::MODEL . " g
            WHERE g.email = :email");
        $query->setParameters(array('email' => $email));
        return $this->getResults($query);
    }

    public function update(\Sb\Db\Model\Guest $guest) {
        $this->entityManager>persist($guest);

        if ($guest->getInvitations()) {
            foreach ($guest->getInvitations() as $invitationToAdd) {
                $this->entityManager->persist($invitationToAdd);
            }
        }

        $this->entityManager>flush();
        return true;
    }

}
