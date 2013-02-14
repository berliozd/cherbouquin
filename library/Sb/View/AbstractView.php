<?php

namespace Sb\View;

/**
 *
 * @author Didier
 */
abstract class AbstractView {

    protected $defImg;

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
    }

    public function get() {

    }

}