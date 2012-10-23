<?php

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache,
    Doctrine\ORM\Tools\Setup,
    Doctrine\Common\Util\Debug;

require_once 'Library/Doctrine/Doctrine/ORM/Tools/Setup.php';
Doctrine\ORM\Tools\Setup::registerAutoloadGit(dirname(__FILE__) . "/Library/Doctrine");

$applicationMode = "development";
if ($applicationMode == "development") {
    $cache = new \Doctrine\Common\Cache\ArrayCache;
} else {
    $cache = new \Doctrine\Common\Cache\ApcCache;
}

$config = new Configuration;
$config->setMetadataCacheImpl($cache);
$driverImpl = $config->newDefaultAnnotationDriver(array(__DIR__ . "/Db/Model"));
$config->setMetadataDriverImpl($driverImpl);
$config->setQueryCacheImpl($cache);
$config->setProxyDir(__DIR__ . "/Db/Proxies");
$config->setProxyNamespace('Proxies');

if ($applicationMode == "development") {
    $config->setAutoGenerateProxyClasses(true);
} else {
    $config->setAutoGenerateProxyClasses(false);
}

//$logger = new Doctrine\DBAL\Logging\EchoSQLLogger();
//$config->setSQLLogger($logger);
// Database connection information
$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'user' => 'share1book',
    'password' => 'share1book',
    'host' => 'localhost',
    'dbname' => 'share1book'
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$helpers = array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
);