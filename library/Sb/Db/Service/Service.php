<?php

namespace Sb\Db\Service;

use \Sb\Trace\Trace;

/**
 * Description of Service
 * @author Didier
 */
class Service {

    private $cache;

    private $dao;

    private $serviceName;

    protected function __construct($dao, $serviceName) {

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

    /**
     *
     * @return \Sb\Db\Dao\AbstractDao
     */
    public function getDao() {

        return $this->dao;
    }

    protected function logException($className, $functionName,\Exception $exc) {

        Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", $className, $functionName, $exc->getTraceAsString()));
    }

}