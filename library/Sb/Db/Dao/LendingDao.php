<?php

namespace Sb\Db\Dao;

/**
 * Description of LendingDao
 *
 * @author Didier
 */
class LendingDao extends \Sb\Db\Dao\AbstractDao {

	const MODEL = "\\Sb\\Db\\Model\\Lending";
	
    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\LendingDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\LendingDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(self::MODEL);
    }

    /**
     *
     * @param \Sb\Db\Model\Lending $lending
     * @return boolean
     */
    public function add(\Sb\Db\Model\Model $lending) {

        if ($lending->getUserbook())
            $this->entityManager->persist($lending->getUserbook());
        if ($lending->getBorrower_userbook())
            $this->entityManager->persist($lending->getBorrower_userbook());
        if ($lending->getGuest())
            $this->entityManager->persist($lending->getGuest());
        $this->entityManager->persist($lending);
        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param \Sb\Db\Model\Lending $lending
     * @return boolean
     */
    public function update(\Sb\Db\Model\Model $lending) {

        $this->entityManager->persist($lending);
        $this->entityManager->flush();
        return true;
    }

}