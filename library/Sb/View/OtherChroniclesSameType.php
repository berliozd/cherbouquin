<?php

namespace Sb\View;

use Sb\Templates\Template;
use Sb\Adapter\ChronicleListAdapter;
use Sb\Helpers\ChronicleHelper;

/**
 *
 * @author Didier
 */
class OtherChroniclesSameType extends \Sb\View\AbstractView {

    private $chronicles;

    function __construct($chronicles = null) {
        parent::__construct();
        $this->chronicles = $chronicles;
    }

    public function get() {

        $tpl = new Template("otherChroniclesSameType");

        // Set Adapter
        $chronicleListAdpater = new ChronicleListAdapter();
        $chronicleListAdpater->setChronicles($this->chronicles);

        $tpl->setVariables(array(
            "chronicles" => $chronicleListAdpater->getAsPushedChronicleViewModelList(), "title" => __("Chroniques similaires", "s1b")
        ));

        return $tpl->output();
    }

}
