<?php

namespace Sb\Db\Dao;

/**
 * Description of InvitationDao
 *
 * @author Didier
 */
class InvitationDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\InvitationDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\InvitationDao ();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Invitation");
    }

    /**
     *
     * @param \Sb\Db\Model\Invitation $Invitation
     */
    public function add(\Sb\Db\Model\Invitation $invitation) {

        \Doctrine\Common\Util\Debug::dump($invitation);
        \Doctrine\Common\Util\Debug::dump($invitation->getGuest());
        if ($invitation->getGuest()) {
            $this->entityManager->persist($invitation->getGuest());
        }

        $this->entityManager->persist($invitation);

        $this->entityManager->flush();

        return true;
    }

    /**
     * Allows to get a list of \Sb\Db\Model\Invitation sent by the specified user to the specified email
     * @param \Sb\Db\Model\User $sender
     * @param type $email
     * @return array of \Sb\Db\Model\Invitation
     */
    public function getListForSenderAndGuestEmail(\Sb\Db\Model\User $sender, $email) {

        $query = $this->entityManager->createQuery("SELECT i FROM \Sb\Db\Model\Invitation i
            JOIN i.guest g            
            WHERE g.email = :email
            AND i.sender = :sender");

        $query->setParameters(array('email' => $email,
            'sender' => $sender));

        $result = $this->getResults($query);

        return $result;
    }

    /**
     * Allows to get a liste of Invitation sent to the specified email
     * @param type $email
     * @return array of \Sb\Db\Model\Invitation
     */
    public function getListByGuestEmail($email) {
        
        $query = $this->entityManager->createQuery("SELECT i FROM \Sb\Db\Model\Invitation i
            JOIN i.guest g            
            WHERE g.email = :email");

        $query->setParameters(array('email' => $email));

        $result = $this->getResults($query);

        return $result;
    }

    /**
     * Allows to get a an invitation for the email and token passed
     * @param type $email email searched
     * @param type $token token searched
     * @return \Sb\Db\Model\Invitation : a single invitation or null
     */
    public function getByEmailAndToken($email, $token) {

        $query = $this->entityManager->createQuery("SELECT i FROM \Sb\Db\Model\Invitation i
            JOIN i.guest g
            WHERE g.email = :email 
            AND i.token = :token");
        $query->setParameters(array(
            'email' => $email,
            'token' => $token)
        );
        return $query->getOneOrNullResult();
    }

}