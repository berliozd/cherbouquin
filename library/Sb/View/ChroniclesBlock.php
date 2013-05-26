<?php

namespace Sb\View;

use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class ChroniclesBlock extends \Sb\View\AbstractView {
    
    /* @var $chronicles array of ChronicleViewModelLight */
    private $chronicles;

    private $title;

    /**
     *
     * @param Array of ChronicleViewModelLight $chronicles
     */
    function __construct($chronicles, $title) {

        parent::__construct();
        $this->chronicles = $chronicles;
        $this->title = $title;
    }

    public function get() {

        $tpl = new Template("chroniclesBlock");
        
        $tpl->setVariables(array(
                "chronicles" => $this->chronicles,
                "title" => $this->title
        ));
        
        return $tpl->output();
    }

}
