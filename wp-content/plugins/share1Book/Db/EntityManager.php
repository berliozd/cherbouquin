<?php

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache,
    Doctrine\ORM\Tools\Setup,
    Doctrine\DBAL\Event\Listeners\MysqlSessionInit,
    Sb\Config\Model;

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

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private static function getEntityManager() {

        $cache = new \Doctrine\Common\Cache\ApcCache;
        //$cache = new \Doctrine\Common\Cache\ArrayCache();

        $config = new \Doctrine\ORM\Configuration;
        $driverImpl = $config->newDefaultAnnotationDriver(array(SHARE1BOOK_PLUGIN_PATH . "/Db/Model"));
        $config->setMetadataDriverImpl($driverImpl);

        // settings caches
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setResultCacheImpl($cache);


        $config->setProxyDir(SHARE1BOOK_PLUGIN_PATH . "/Db/Proxies");
        $config->setProxyNamespace('Proxies');

        //$config->setAutoGenerateProxyClasses(true);
        $config->setAutoGenerateProxyClasses(false);

        $logger = new \Doctrine\DBAL\Logging\EchoSQLLogger();
//        $logger = new \Sb\Trace\SqlTrace;
//        $config->setSQLLogger($logger);
        // Database connection information
        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => DB_USER,
            'password' => DB_PASSWORD,
            'host' => DB_HOST,
            'dbname' => DB_NAME
        );

        // Create EntityManager
        $entityManager = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $entityManager->getEventManager()->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit('utf8', 'utf8_general_ci'));

        return $entityManager;
    }

}

?>
