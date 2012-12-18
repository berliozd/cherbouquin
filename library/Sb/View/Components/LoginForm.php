<?php

namespace Sb\View\Components;

/**
  *
 * @author Didier
 */
class LoginForm extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/loginForm";
        $tpl = new \Sb\Templates\Template($baseTpl, $this->baseDir);
        return $tpl->output();
    }
}