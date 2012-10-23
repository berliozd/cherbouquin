<?php

namespace Sb\Db\Service;

/**
 * Description of Service
 *
 * @author Didier
 */
class Service {

    private $cache;
    private $dao;
    private $serviceName;

    protected function __construct($dao, $serviceName) {
        $context = \Sb\Context\Model\Context::getInstance();
        $this->cache = \Sb\Cache\ZendFileCache::getInstance();
        $this->dao = $dao;
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

    public function getDao() {
        return $this->dao;
    }

    protected function logException($className, $functionName, \Exception $exc) {
        \Sb\Trace\Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", $className, $functionName, $exc->getTraceAsString()));
    }

}