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

        // Get chronicle type label using first chronicle
        $typeLabel = ChronicleHelper::getTypeLabel($this->chronicles[0]->getType_id());

        // Set Adapter
        $chronicleListAdpater = new ChronicleListAdapter();
        $chronicleListAdpater->setChronicles($this->chronicles);

        $tpl
                ->setVariables(
                        array("chronicles" => $chronicleListAdpater->getAsPushedChronicleViewModelList(),
                                "title" => sprintf(__("A lire aussi dans la catégorie \"%s\"", "s1b"), $typeLabel)));

        return $tpl->output();
    }

}
