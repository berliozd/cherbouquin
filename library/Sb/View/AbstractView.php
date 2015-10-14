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
        return new \Sb\Config\Model\Config();
    }

    protected function getContext() {
        return new \Sb\Context\Model\Context();
    }

    function __construct() {
        $this->defImg = \Sb\Helpers\BookHelper::getDefaultImage();
    }

    public function get() {

    }

}