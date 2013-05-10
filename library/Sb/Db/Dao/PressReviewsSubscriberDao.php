<?php

namespace Sb\Db\Dao;

/**
 * @author Didier
 */
class PressReviewsSubscriberDao extends AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\PressReviewsSubscriber";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\PressReviewsSubscriberDao
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new PressReviewsSubscriberDao();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(self::MODEL);
    }

    /**
     * Get a press review subscriber by email
     * @param unknown $email the email to search the press review subscriber with
     * @return Ambigous <\Doctrine\ORM\mixed, NULL, mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, string>
     */
    public function getByEmail($email) {

        $query = $this->entityManager->createQuery("SELECT prs FROM " . self::MODEL . " prs
            WHERE prs.email = :email");
        $query->setParameters(array(
                'email' => $email
        ));
        return $query->getOneOrNullResult();
    }

}
