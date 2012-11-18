<?php

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
        global $globalConfig;
        return $globalConfig;
    }

    protected function getContext() {
        global $globalContext;
        return $globalContext;
    }

    function __construct() {
        $this->defImg = \Sb\Helpers\BookHelper::getDefaultImage();
        $this->baseDir = SHARE1BOOK_PLUGIN_PATH;
        $this->baseUrl = SHARE1BOOK_PLUGIN_URL;
    }

    public function get() {

    }

}