<?php

namespace Sb\Cache;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZendFileCache
 *
 * @author Didier
 */
class ZendFileCache {

    private $frontendOptions;
    private $backendOptions;
    private $cacheObject;

    /**
     *
     * @return \Sb\Context\Model\Context
     */
    private function getContext() {
        global $s1b;
        return $s1b->getContext();
    }

    private static $instance;

    /**
     *
     * @return ZendFileCache
     */
    public static function getInstance() {
        if (!self::$instance)
            self::$instance = new ZendFileCache;
        return self::$instance;
    }

    protected function __construct() {
        // front end options, cache for 1 hour
        $this->frontendOptions = array(
            'lifetime' => 3600,
            'automatic_serialization' => true
        );
        // backend options
        $this->backendOptions = array(
            'cache_dir' => $this->getContext()->getBaseDirectory() . 'var/cache', // Directory where to put the cache files
        );
        $this->cacheObject = \Zend_Cache::factory('Core', 'File', $this->frontendOptions, $this->backendOptions);
    }

    public function load($key) {
        return $this->cacheObject->load($key);
    }

    public function save($value, $key) {
        return $this->cacheObject->save($value, $key);
    }

    public function remove($key) {
        $this->cacheObject->remove($key);
    }

}

?>
