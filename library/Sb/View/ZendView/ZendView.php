<?php

namespace Sb\View\ZendView;

/**
 * Description of ZendView
 *
 * @author Didier
 */
class ZendView extends \Zend_View {

    private $scriptName;

    public function __construct($scriptName) {
        \Sb\Trace\FireBugTrace::Trace("in controller");
        $this->setScriptPath(APPLICATION_PATH . "/scripts");
        $this->scriptName = $scriptName;
    }

    public function get() {
        return parent::render($this->scriptName);
    }

}