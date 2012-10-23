<?php

use Sb\Config\Model;

namespace Sb\View;

/**
 *
 * @author Didier
 */
abstract class AbstractView {

    protected $defImg;
    protected $baseDir;
    protected $baseUrl;
    protected $cacheTemplatingEnabled;

    /**
     *
     * @return Config
     */
    protected function getConfig() {
        global $s1b;
        return $s1b->getConfig();
    }

    protected function getContext() {
        global $s1b;
        return $s1b->getContext();
    }

    function __construct() {
        $context = \Sb\Context\Model\Context::getInstance();
        $this->defImg = $context->getDefaultImage();
        $this->baseDir = $context->getBaseDirectory();
        $this->baseUrl = $context->getBaseUrl();
    }

    public function get() {

    }

}