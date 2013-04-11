<?php

namespace Sb\Db\Dao;

class CountryDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\CountryDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\CountryDao;
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Country");
    }

    public function getCountryByCode($code) {
        $query = $this->entityManager->createQuery("SELECT c FROM \Sb\Db\Model\Country c
            WHERE c.iso3166= :code");
        $query->setParameters(array(
            'code' => $code)
        );
        return $query->getOneOrNullResult();
    }

}