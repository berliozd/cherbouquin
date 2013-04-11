<?php

namespace Sb\Db\Dao;

/**
 * Description of Contributor
 *
 * @author Didier
 */
class ContributorDao extends \Sb\Db\Dao\AbstractDao {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\ContributorDao
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new \Sb\Db\Dao\ContributorDao();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct("\Sb\Db\Model\Contributor");
    }

    /**
     * Get contributor from name
     * @param string $fullName
     * @return \Sb\Db\Model\Contributor
     */
    public function getByFullName($fullName) {

        $query = $this->entityManager->createQuery("SELECT c FROM \Sb\Db\Model\Contributor c
            WHERE c.full_name = :full_name");
        $query->setParameters(array(
            'full_name' => trim($fullName))
        );
        return $query->getOneOrNullResult();
    }

    /**
     * Get contributors for book
     * @param type $bookId
     * @return \Doctrine\Common\Collections\ArrayCollection of \Sb\Db\Model\Contributor
     */
    public function getListForBook($bookId) {

        $query = $this->entityManager->createQuery("SELECT c FROM \Sb\Db\Model\Contributor c
            JOIN c.books b
            WHERE b.id = :book_id");
        $query->setParameters(array(
            'book_id' => $bookId)
        );
        
        $collection = new \Doctrine\Common\Collections\ArrayCollection($query->getResult());
        return $collection;
    }

}