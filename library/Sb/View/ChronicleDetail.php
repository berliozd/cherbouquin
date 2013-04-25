<?php

namespace Sb\View;

use Sb\Templates\Template;
use Sb\Db\Model\Chronicle;
use Sb\Adapter\ChronicleAdapter;

/**
 *
 * @author Didier
 */
class ChronicleDetail extends \Sb\View\AbstractView {

    private $chronicle;

    function __construct(Chronicle $chronicle = null) {
        parent::__construct();
        $this->chronicle = $chronicle;
    }

    public function get() {

        $tpl = new Template("chronicleDetail");

        $chronicleAdpater = new ChronicleAdapter($this->chronicle);
        $tpl->setVariables(array("chronicle" => $chronicleAdpater->getAsChronicleDetailViewModel($this->defImg)));

        return $tpl->output();
    }

}
