<?php

namespace Sb\Service;

use Sb\Cache\ZendFileCache;

/**
 * Description of Service
 *
 * @author Didier
 */
abstract class Service {

    private $cache;    
    private $serviceName;

    protected function __construct($serviceName) {
        $this->cache = ZendFileCache::getInstance();
        $this->serviceName = $serviceName;
    }

    protected function getData($dataKey) {
        $dataKey = $this->formateKey($dataKey);
        return $this->cache->load($dataKey);
    }

    protected function setData($dataKey, $dataValue) {
        $dataKey = $this->formateKey($dataKey);
        $this->cache->save($dataValue, $dataKey);
    }

    private function formateKey($key) {
        return "SVC_" . $this->serviceName . "_KEY_" . $key;
    }


    protected function logException($className, $functionName, \Exception $exc) {
        Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", $className, $functionName, $exc->getTraceAsString()));
    }
}