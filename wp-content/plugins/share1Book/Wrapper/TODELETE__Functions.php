<?php

use Sb\Config\Model;

namespace Sb\Wrapper;


/**
 * Description of \Sb\Wrapper\Functions
 *
 * @author Didier
 */
class Functions {

    /**
     *
     * @return Config
     */
    private function getConfig() {
        global $s1b;
        return $s1b->getConfig();
    }

    /**
     *
     * @return \Sb\Context\Model\Context
     */
    private function getContext() {
        global $s1b;
        return $s1b->getContext();
    }


    function __construct($s1b) {
        $this->s1b = $s1b;
    }


}