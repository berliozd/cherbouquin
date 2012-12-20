<?php

namespace Sb\Db\Dao;

/**
 * Description of \Sb\Db\Dao\AbstractDao
 *
 * @author Didier
 */
abstract class AbstractDao {

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager = null;
    protected $entityName;
    private $cacheDuration = 3600; // Cache duration in seconds

    /**
     *
     * @return Sb\Model\
     */
//    private function getConfig() {
//        global $s1b;
//        return $s1b->getConfig();
//    }

    protected function __construct($entityName) {
        $this->entityName = $entityName;
        $this->initEntityManager();
    }

    private function initEntityManager() {
        // get EntityManager singleton
        $this->entityManager = \Sb\Db\EntityManager::getInstance();
    }

    /**
     *
     * @param type $id
     * @return \Sb\Db\Model\Model
     */
    public function get($id) {
        $user = $this->entityManager->find($this->entityName, $id);
        return $user;
    }

    public function getAll($criteria = null, $orderby = null, $limit = null) {
        if ($criteria || $orderby || $limit) {
            return $this->entityManager->getRepository($this->entityName)->findBy($criteria, $orderby, $limit);
        } else {
            return $this->entityManager->getRepository($this->entityName)->findAll();
        }
    }

    public function getResults(\Doctrine\ORM\Query $query, $cacheId, $useCache = false) {

        $query->useResultCache($useCache, $this->getCacheDuration(), $cacheId);

        return $query->getResult();
    }

    public function getOneResult(\Doctrine\ORM\Query $query, $cacheId, $useCache = false) {

        $query->useResultCache($useCache, $this->getCacheDuration(), $cacheId);

        return $query->getOneOrNullResult();
    }

    /**
     * Get a cache id for APC cache item to store the result
     * @param type $func
     * @param type $args
     * @return string
     */
    public function getCacheId($func, $args) {
        $result = get_called_class() . "_" . $func . "_" . implode("-", $args);
        return $result;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }
    
    public function getCacheDuration() {
        return $this->cacheDuration;
    }

    public function setCacheDuration($cacheDuration) {
        $this->cacheDuration = $cacheDuration;
    }
    
    public function bulkRemove($entities) {
        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }
        $this->getEntityManager()->flush();
    }

    public function remove($entity) {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
        return true;
    }

    /**
     * 
     * @param \Sb\Db\Model\Model $entity
     * @return boolean true if update occured successfuly
     */
    public function update(\Sb\Db\Model\Model $entity) {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return true;
    }

    /**
     * 
     * @param \Sb\Db\Model\Model $entity
     * @return boolean true if adding occured successfuly
     */
    public function add(\Sb\Db\Model\Model $entity) {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return true;
    }

}