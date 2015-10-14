<?php

namespace Sb\Db;

/**
 * Description of EntityManager
 *
 * @author Didier
 */
class EntityManager extends \Doctrine\ORM\EntityManager {

    public static $instance;

    public static function getInstance() {
        if (!self::$instance)
            self::$instance = self::getEntityManager();
        return self::$instance;
    }

    public static function clearInstance() {
        self::$instance = null;
    }
    
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private static function getEntityManager() {
        $sbConfig = new \Sb\Config\Model\Config();

        $cache = new \Doctrine\Common\Cache\ApcCache;
        $cache->setNamespace($sbConfig->getApcCacheNamespace());

        $config = new \Doctrine\ORM\Configuration;
        $driverImpl = $config->newDefaultAnnotationDriver(array("/Sb/Db/Model"));
        $config->setMetadataDriverImpl($driverImpl);

        // settings caches
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setResultCacheImpl($cache);


        $config->setProxyDir("Sb/Db/Proxies");
        $config->setProxyNamespace('Proxies');

        $config->setAutoGenerateProxyClasses(false);

        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => $sbConfig->getDatabaseParams()->user,
            'password' => $sbConfig->getDatabaseParams()->password,
            'host' => $sbConfig->getDatabaseParams()->host,
            'dbname' => $sbConfig->getDatabaseParams()->name
        );

        // Create EntityManager
        $entityManager = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $entityManager->getEventManager()->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit('utf8', 'utf8_general_ci'));

        return $entityManager;
    }

}