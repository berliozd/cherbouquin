<?php

namespace Sb\Db\Service;

use \Sb\Trace\Trace;

/**
 * Description of Service
 * @author Didier
 */
abstract class AbstractService {

    const CRITERIA_TYPE_MODEL = "MODEL";

    const CRITERIA_TYPE_STRING = "STRING";

    private $cache;
    private $dao;
    private $serviceName;

    protected function __construct($dao, $serviceName) {

        $this->cache = \Sb\Cache\ZendFileCache::getInstance();
        $this->dao = $dao;
        $this->serviceName = $serviceName;
    }

    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new BookSvc();
        return self::$instance;
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

    protected function logException($className, $functionName, \Exception $exc) {

        Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", $className, $functionName, $exc->getTraceAsString()));
    }

    public function getCacheSuffixFromCriteria($criteria) {

        $suffix = "";
        if (isset($criteria)) {
            foreach ($criteria as $arrayKey => $arrayValue) {
                if (isset($arrayValue)) {
                    if ($arrayValue[0]) // Value passed is a model or an array of models
                        $suffix .= "_" . $arrayKey . "_" . $arrayValue[2]->getId();
                    else {
                        // in that case $arrayValue is an array and contains operator (=, LIKE) as first element and value to compare as second element
                        $suffix .= "_" . $arrayKey;
                        $suffix .= "_" . $arrayValue[2];
                    }
                }
            }
        }

        return $suffix;
    }

}